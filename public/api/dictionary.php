<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/src/bootstrap.php';
header('Cache-Control: no-store');
if(!is_post())json_out(['error'=>'Método no permitido.'],405);
if(!current_user())json_out(['error'=>'Iniciá sesión.'],401);
csrf_check();
$data=json_decode((string)file_get_contents('php://input'),true)?:[];
$term=trim(preg_replace('/\s+/u',' ',(string)($data['term']??'')));
if($term===''||mb_strlen($term)>220)json_out(['error'=>'Ingresá una palabra o frase corta de hasta 220 caracteres.'],422);
$url=rtrim((string)env('MYTEACHER_IA_URL',''),'/');
$token=(string)env('MYTEACHER_IA_TOKEN','');
if(!$url||!$token)json_out(['error'=>'El diccionario con IA no está configurado.'],503);
$ch=curl_init($url.'/definir');
curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>['term'=>$term],CURLOPT_HTTPHEADER=>['X-MyTeacher-Token: '.$token],CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>195,CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false]);
$result=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
$body=json_decode((string)$result,true);
if($http!==200||empty($body['definition']))json_out(['error'=>'El diccionario está iniciando o no pudo generar la definición. Intentá nuevamente.'],503);
json_out(['term'=>$term,'definition'=>mb_substr((string)$body['definition'],0,1800)]);
