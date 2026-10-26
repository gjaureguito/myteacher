<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/src/bootstrap.php';
header('Cache-Control: no-store');
$u=current_user();if(!$u)json_out(['error'=>'Iniciá sesión.'],401);
if(!is_post())json_out(['error'=>'Método no permitido.'],405);
csrf_check();
$input=json_decode((string)file_get_contents('php://input'),true)?:[];
$term=trim(preg_replace('/\s+/u',' ',(string)($input['term']??'')));
if($term===''||mb_strlen($term)>220)json_out(['error'=>'Seleccioná una palabra o frase corta de hasta 220 caracteres.'],422);
$key=mb_strtolower($term);
try {
 $pdo=db();
 $pdo->exec(db_is_sqlite()?
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key TEXT PRIMARY KEY,translation TEXT NOT NULL,created_at TEXT NOT NULL)':
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key VARCHAR(220) PRIMARY KEY,translation TEXT NOT NULL,created_at DATETIME NOT NULL)');
 $cached=q_one('SELECT translation FROM translation_cache WHERE term_key=?',[$key]);
 if($cached && trim((string)$cached['translation'])!=='')json_out(['term'=>$term,'translation'=>$cached['translation'],'cached'=>true]);
}catch(Throwable $e){error_log('Translation cache: '.$e->getMessage());json_out(['error'=>'El servicio de traducción no está disponible.'],503);}
$url='https://api.mymemory.translated.net/get?q='.rawurlencode($term).'&langpair=en%7Ces';
$email=(string)env('MYMEMORY_CONTACT_EMAIL','');
if($email!==''&&filter_var($email,FILTER_VALIDATE_EMAIL))$url.='&de='.rawurlencode($email);
$ch=curl_init($url);
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>12,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_USERAGENT=>'MyTeacher vocabulary lookup/1.0']);
$body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
$data=json_decode((string)$body,true);
$translation=trim(html_entity_decode((string)($data['responseData']['translatedText']??''),ENT_QUOTES|ENT_HTML5,'UTF-8'));
if($http!==200||(int)($data['responseStatus']??0)!==200||$translation===''||mb_stripos($translation,'MYMEMORY WARNING')!==false)json_out(['error'=>'El traductor no pudo responder. Volvé a intentar o completá la traducción manualmente.'],503);
$translation=mb_substr($translation,0,800);
try{
 if(db_is_sqlite())q('INSERT OR IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,?)',[$key,$translation,gmdate('Y-m-d H:i:s')]);
 else q('INSERT IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,UTC_TIMESTAMP())',[$key,$translation]);
}catch(Throwable $e){error_log('Translation cache insert: '.$e->getMessage());}
json_out(['term'=>$term,'translation'=>$translation,'cached'=>false]);
