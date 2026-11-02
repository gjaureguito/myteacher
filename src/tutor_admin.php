<?php
declare(strict_types=1);

function tutor_admin_int(array $in,string $key,int $min,int $max): int {
    $v=$in[$key]??null;
    if(!is_string($v)||!preg_match('/^\d{1,10}$/D',$v)|| (int)$v<$min || (int)$v>$max)throw new InvalidArgumentException('Revisá el campo '.$key.'.');
    return (int)$v;
}
function tutor_admin_cents(array $in,string $key,string $currency='USD'): int {
    $v=$in[$key]??null;
    $digits=$currency==='ARS'?7:4;
    if(!is_string($v)||!preg_match('/^(\d{1,'.$digits.'})(?:[.,](\d{1,2}))?$/D',$v,$m))throw new InvalidArgumentException('Ingresá un importe en '.$currency.' con hasta dos decimales, sin separador de miles.');
    return (int)$m[1]*100+(int)str_pad($m[2]??'',2,'0');
}
function tutor_admin_apply(int $adminId,array $in): void {
    $pdo=db();$pdo->beginTransaction();
    try {
        // Same lock order as reservations/enrollment; never reset reserved funds.
        q('UPDATE tutor_budget SET reserved_cents=reserved_cents WHERE id=1');
        $action=$in['action']??'';$details=[];
        if($action==='settings') {
            if(!array_key_exists('price_ars',$in))throw new InvalidArgumentException('Recargá el panel: el precio de recarga ahora se configura en pesos argentinos.');
            $before=tutor_settings();$budget=tutor_admin_cents($in,'budget_usd');
            $price=$in['price_ars']===''?null:tutor_admin_cents($in,'price_ars','ARS');
            if($budget>100000)throw new InvalidArgumentException('El presupuesto no puede superar USD 1000.');
            if($price!==null && ($price<1||$price>100000000))throw new InvalidArgumentException('El precio debe ser entre ARS 0,01 y ARS 1.000.000, o quedar vacío mientras está pendiente.');
            $daily=tutor_admin_int($in,'daily_limit',0,1000);$total=tutor_admin_int($in,'total_limit',0,10000);
            $students=tutor_admin_int($in,'max_students',0,100000);$minutes=tutor_admin_int($in,'pack_minutes',1,60);
            if($daily>$total)throw new InvalidArgumentException('El límite diario no puede superar el total.');
            q('UPDATE tutor_budget SET budget_cents=?,daily_limit=?,total_limit=?,max_students=?,price_ars_cents=?,pack_seconds=?,pilot_open=?,enroll_open=? WHERE id=1',[$budget,$daily,$total,$students,$price,$minutes*60,isset($in['pilot_open'])?1:0,isset($in['enroll_open'])?1:0]);
            $details=['before'=>$before,'after'=>tutor_settings()];
        }elseif($action==='member') {
            $uid=tutor_admin_int($in,'user_id',1,2147483647);$before=q_one('SELECT * FROM tutor_members WHERE user_id=?',[$uid]);
            if(!$before)throw new InvalidArgumentException('El estudiante no está inscripto.');
            $daily=($in['daily_limit']??'')===''?null:tutor_admin_int($in,'daily_limit',0,1000);
            $total=($in['total_limit']??'')===''?null:tutor_admin_int($in,'total_limit',0,10000);
            $s=tutor_settings();if(($daily??$s['daily_limit'])>($total??$s['total_limit']))throw new InvalidArgumentException('El límite diario no puede superar el total.');
            q('UPDATE tutor_members SET daily_limit=?,total_limit=?,blocked=? WHERE user_id=?',[$daily,$total,isset($in['blocked'])?1:0,$uid]);
            $details=['before'=>$before,'after'=>q_one('SELECT * FROM tutor_members WHERE user_id=?',[$uid])];
        }elseif($action==='record_payment') {
            $uid=tutor_admin_int($in,'user_id',1,2147483647);
            if(!q_one('SELECT user_id FROM tutor_members WHERE user_id=?',[$uid]))throw new InvalidArgumentException('Elegí un estudiante inscripto.');
            if(!is_string($in['reference']??null)||!is_string($in['evidence']??null))throw new InvalidArgumentException('Comprobante inválido.');
            $ref=trim($in['reference']);$evidence=trim($in['evidence']);
            if(!preg_match('/^[A-Za-z0-9_.:-]{4,100}$/D',$ref)||mb_strlen($evidence)<8||mb_strlen($evidence)>500)throw new InvalidArgumentException('Ingresá una referencia única de 4 a 100 caracteres y una descripción del comprobante de 8 a 500 caracteres.');
            if(q_one('SELECT id FROM tutor_payments WHERE reference=?',[$ref]))throw new InvalidArgumentException('Esa referencia ya está registrada; no se duplicó el pago.');
            $amount=tutor_admin_cents($in,'amount_ars','ARS');$minutes=tutor_admin_int($in,'minutes',1,600);
            if($amount<1||$amount>100000000)throw new InvalidArgumentException('El importe debe ser entre ARS 0,01 y ARS 1.000.000.');
            q("INSERT INTO tutor_payments (user_id,reference,amount_cents,currency,seconds,status,evidence,created_by,updated_by,created_at,updated_at) VALUES (?,?,?,'ARS',?,'reported',?,?,?,?,?)",[$uid,$ref,$amount,$minutes*60,$evidence,$adminId,$adminId,now_utc(),now_utc()]);
            $details=['payment_id'=>(int)$pdo->lastInsertId(),'user_id'=>$uid,'reference'=>$ref,'amount_cents'=>$amount,'currency'=>'ARS','seconds'=>$minutes*60];
        }elseif($action==='payment_status') {
            $id=tutor_admin_int($in,'payment_id',1,2147483647);$before=q_one('SELECT * FROM tutor_payments WHERE id=?',[$id]);
            $state=$in['status']??'';
            if(!$before||!in_array($state,['verified_manual','voided'],true)||$before['status']==='voided'||($state==='verified_manual'&&$before['status']!=='reported'))throw new InvalidArgumentException('La transición del pago no está permitida.');
            if(!isset($in['confirmed']))throw new InvalidArgumentException('Confirmá que revisaste el comprobante o la anulación en el medio de pago.');
            q('UPDATE tutor_payments SET status=?,updated_by=?,updated_at=? WHERE id=?',[$state,$adminId,now_utc(),$id]);
            $details=['before'=>$before,'after'=>q_one('SELECT * FROM tutor_payments WHERE id=?',[$id])];
        }else throw new InvalidArgumentException('Acción inválida.');
        q('INSERT INTO tutor_audit (admin_id,action,details,created_at) VALUES (?,?,?,?)',[$adminId,$action,json_encode($details,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),now_utc()]);
        $pdo->commit();
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
