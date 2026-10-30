<?php
require dirname(__DIR__,2).'/src/bootstrap.php';
require_admin();
require ROOT.'/src/admin_billing.php';
header('Cache-Control: private, no-store');
$search=is_string($_GET['s']??null)?mb_substr(trim($_GET['s']),0,190):'';
$filter=is_string($_GET['status']??null)?$_GET['status']:'all';
if (!in_array($filter,['all','active','pending','missing'],true)) $filter='all';
$page=max(1,min(100000,(int)(is_scalar($_GET['page']??null)?$_GET['page']:1)));
$where=" WHERE (ba.id IS NOT NULL OR u.sub_status IN ('active','authorized','pending','paused','cancelled'))";
$params=[];
if ($search!=='') {
    $where.=' AND (u.email LIKE ? OR u.name LIKE ? OR b.billing_data LIKE ? OR b.billing_data LIKE ?)';
    $digits=preg_replace('/[^0-9]/','',$search);
    $params=["%$search%","%$search%","%$search%",'%'.($digits!==''?$digits:$search).'%'];
}
if ($filter==='active') { $where.=" AND u.sub_status='active' AND u.access_until>?"; $params[]=now_utc(); }
if ($filter==='pending') $where.=" AND u.sub_status='pending'";
if ($filter==='missing') $where.=' AND ba.id IS NULL';
$from=' FROM users u '.admin_billing_join().$where;
$total=(int)q_val('SELECT COUNT(*)'.$from,$params);
$pages=max(1,(int)ceil($total/25));$page=min($page,$pages);
$rows=q_all('SELECT u.*,'.admin_billing_fields().$from.' ORDER BY u.id DESC LIMIT 25 OFFSET '.(($page-1)*25),$params);
render('admin_billing',compact('rows','search','filter','page','pages','total'),'Datos para facturación');
