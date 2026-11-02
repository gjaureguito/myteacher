<?php
require dirname(__DIR__,2).'/src/bootstrap.php';
header('Cache-Control: no-store');
if(!is_post())json_out(['error'=>'Método no permitido.'],405);
$user=current_user();if(!$user)json_out(['error'=>'Ingresá a tu cuenta.'],401);
csrf_check();
$raw=file_get_contents('php://input',false,null,0,4097);
if(strlen($raw)>4096)json_out(['error'=>'Solicitud demasiado larga.'],413);
$in=json_decode($raw,true);if(!is_array($in))json_out(['error'=>'Solicitud inválida.'],400);
$uid=(int)$user['id'];
if(($in['action']??'')==='recharge_interest') {
    if(!q_one('SELECT user_id FROM tutor_members WHERE user_id=?',[$uid]))json_out(['error'=>'Primero anotate en el piloto.'],409);
    q('UPDATE tutor_members SET recharge_requested_at=COALESCE(recharge_requested_at,?) WHERE user_id=?',[now_utc(),$uid]);
    json_out(['message'=>'Registramos tu interés. Las recargas todavía no están a la venta; no se realizó ningún cobro ni se agregó saldo.']);
}
if(($in['action']??'')==='enroll') {
    try{tutor_enroll($uid);}catch(RuntimeException $e){json_out(['error'=>$e->getMessage()],409);}
    json_out(['enrolled'=>true,'enabled'=>tutor_enabled(),'quota'=>tutor_quota($uid)]);
}
if(($in['action']??'')!=='turn')json_out(['error'=>'Acción inválida.'],400);
if(!tutor_enabled())json_out(['error'=>'Tu inscripción está guardada. La IA todavía no está habilitada.'],503);
$message=$in['message']??null;$key=$in['request_key']??null;
if(!is_string($message)||strlen(trim($message))<2||strlen($message)>2000||!mb_check_encoding($message,'UTF-8')||!is_string($key)||!preg_match('/^[a-zA-Z0-9-]{16,64}$/D',$key))json_out(['error'=>'Escribí entre 2 y 2000 bytes y volvé a enviar.'],422);
$payload=tutor_payload($uid,trim($message));
// At most 7000 UTF-8 bytes of content + fixed instructions; byte count is a
// conservative upper bound for text tokens. 400 output includes reasoning.
try {$call=tutor_reserve($uid,$key);}catch(RuntimeException $e){json_out(['error'=>$e->getMessage()],429);}
if($call['cached']) {
    if($call['status']==='ok')json_out(['reply'=>$call['reply'],'quota'=>tutor_quota($uid)]);
    json_out(['error'=>'Este intento ya fue registrado. No se volvió a cobrar ni enviar.'],409);
}
session_write_close();
try {
    // No automatic retry, even on timeout: OpenAI may already have billed it.
    $r=http_json('POST','https://api.openai.com/v1/responses',['Authorization: Bearer '.env('OPENAI_API_KEY')],$payload);
    if($r['code']!==200)throw new RuntimeException('provider');
    $data=$r['data'];$reply='';
    foreach($data['output']??[] as $item)foreach($item['content']??[] as $part)if(($part['type']??'')==='output_text')$reply.=$part['text'];
    if(trim($reply)==='')throw new RuntimeException('empty');
    $it=$data['usage']['input_tokens']??null;$ot=$data['usage']['output_tokens']??null;
    $cost=is_int($it)&&is_int($ot)?(int)ceil($it*0.1+$ot*0.5):null;
    q("UPDATE tutor_calls SET status='ok',reply=?,input_tokens=?,output_tokens=?,cost_microusd=? WHERE id=?",[$reply,$it,$ot,$cost,$call['id']]);
    json_out(['reply'=>$reply,'quota'=>tutor_quota($uid)]);
}catch(Throwable $e){
    q("UPDATE tutor_calls SET status='uncertain' WHERE id=?",[$call['id']]);
    json_out(['error'=>'No pudimos obtener una respuesta. Por seguridad este intento cuenta para el cupo; no se reintenta automáticamente.'],502);
}
