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
$direction=(string)($input['direction']??'auto');
if(!in_array($direction,['auto','en-es','es-en'],true))json_out(['error'=>'Dirección de traducción inválida.'],422);
$spanishHint=(bool)preg_match('/[áéíóúñü¿¡]/iu',$term);
$target=$direction==='es-en'||($direction==='auto'&&$spanishHint)?'EN':'ES';
$source=$target==='EN'?'ES':'EN';
$googleKey=trim((string)env('GOOGLE_TRANSLATE_API_KEY',''));
$apiKey=(string)env('DEEPL_API_KEY','');
$provider=$googleKey!==''?'google':($apiKey!==''?'deepl':'mymemory');
$key=hash('sha256',$provider.':'.strtolower($source).'-'.strtolower($target).':'.mb_strtolower($term));
try {
 $pdo=db();
 $pdo->exec(db_is_sqlite()?
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key TEXT PRIMARY KEY,translation TEXT NOT NULL,created_at TEXT NOT NULL)':
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key VARCHAR(220) PRIMARY KEY,translation TEXT NOT NULL,created_at DATETIME NOT NULL)');
 $cached=q_one('SELECT translation FROM translation_cache WHERE term_key=?',[$key]);
 if($cached && trim((string)$cached['translation'])!=='' && mb_strtolower(trim((string)$cached['translation']))!==mb_strtolower($term) && $direction!=='auto')json_out(['term'=>$term,'translation'=>$cached['translation'],'cached'=>true,'provider'=>$provider,'direction'=>strtolower($source).'-'.strtolower($target)]);
}catch(Throwable $e){error_log('Translation cache: '.$e->getMessage());json_out(['error'=>'El servicio de traducción no está disponible.'],503);}
$translation='';
$usedProvider=$provider;
if($googleKey!==''){
 $ch=curl_init('https://translation.googleapis.com/language/translate/v2?key='.rawurlencode($googleKey));
 curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json; charset=utf-8'],CURLOPT_POSTFIELDS=>json_encode(['q'=>[$term],'source'=>strtolower($source),'target'=>strtolower($target),'format'=>'text'],JSON_UNESCAPED_UNICODE),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false]);
 $body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 $result=json_decode((string)$body,true);
 $translation=trim(html_entity_decode((string)($result['data']['translations'][0]['translatedText']??''),ENT_QUOTES|ENT_HTML5,'UTF-8'));
 if($http!==200||$translation===''){error_log('Google translation HTTP '.$http);json_out(['error'=>'Google Translation no respondió. Revisá la API y la facturación del proyecto.'],503);}
 if(mb_strtolower($translation)===mb_strtolower($term))json_out(['error'=>'No se pudo determinar el idioma. Indicá la dirección de traducción.'],422);
 $translation=mb_substr($translation,0,800);
 try{
  if(db_is_sqlite())q('INSERT OR IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,?)',[$key,$translation,gmdate('Y-m-d H:i:s')]);
  else q('INSERT IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,UTC_TIMESTAMP())',[$key,$translation]);
 }catch(Throwable $e){error_log('Google translation cache insert: '.$e->getMessage());}
 json_out(['term'=>$term,'translation'=>$translation,'cached'=>false,'provider'=>'google','direction'=>strtolower($source).'-'.strtolower($target)]);
}
if($apiKey!==''){
 $host=str_ends_with($apiKey,':fx')?'https://api-free.deepl.com':'https://api.deepl.com';
 $ch=curl_init($host.'/v2/translate');
 curl_setopt_array($ch,[
  CURLOPT_POST=>true,
  CURLOPT_HTTPHEADER=>['Authorization: DeepL-Auth-Key '.$apiKey,'Content-Type: application/json'],
  CURLOPT_POSTFIELDS=>json_encode(['text'=>[$term],'source_lang'=>$source,'target_lang'=>$target],JSON_UNESCAPED_UNICODE),
  CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false
 ]);
 $body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 $response=json_decode((string)$body,true);
 if($http===200)$translation=trim((string)($response['translations'][0]['text']??''));
 if($direction==='auto'&&$translation!==''&&mb_strtolower($translation)===mb_strtolower($term)){
  $reverseTarget=$target==='ES'?'EN':'ES';
  $retry=curl_init($host.'/v2/translate');
  curl_setopt_array($retry,[CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Authorization: DeepL-Auth-Key '.$apiKey,'Content-Type: application/json'],CURLOPT_POSTFIELDS=>json_encode(['text'=>[$term],'source_lang'=>$target,'target_lang'=>$reverseTarget],JSON_UNESCAPED_UNICODE),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false]);
  $retryBody=curl_exec($retry);$retryHttp=(int)curl_getinfo($retry,CURLINFO_HTTP_CODE);curl_close($retry);
  $retryData=json_decode((string)$retryBody,true);
  $reversed=trim((string)($retryData['translations'][0]['text']??''));
  if($retryHttp===200&&$reversed!==''&&mb_strtolower($reversed)!==mb_strtolower($term)){$translation=$reversed;$source=$target;$target=$reverseTarget;$key=hash('sha256','deepl:'.strtolower($source).'-'.strtolower($target).':'.mb_strtolower($term));}
 }
 if($http!==200) error_log('DeepL translation HTTP '.$http);
}
if($translation===''){
 $usedProvider='mymemory';
 $key=hash('sha256','mymemory:'.strtolower($source).'-'.strtolower($target).':'.mb_strtolower($term));
 $cached=q_one('SELECT translation FROM translation_cache WHERE term_key=?',[$key]);
 if($cached && trim((string)$cached['translation'])!=='' && mb_strtolower(trim((string)$cached['translation']))!==mb_strtolower($term) && $direction!=='auto')json_out(['term'=>$term,'translation'=>$cached['translation'],'cached'=>true,'provider'=>'mymemory','direction'=>strtolower($source).'-'.strtolower($target)]);
 $url='https://api.mymemory.translated.net/get?q='.rawurlencode($term).'&langpair='.strtolower($source).'%7C'.strtolower($target);
 $email=(string)env('MYMEMORY_CONTACT_EMAIL','');
 if($email!==''&&filter_var($email,FILTER_VALIDATE_EMAIL))$url.='&de='.rawurlencode($email);
 $ch=curl_init($url);
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>12,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_USERAGENT=>'MyTeacher vocabulary lookup/1.0']);
 $body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 $response=json_decode((string)$body,true);
 $translation=trim(html_entity_decode((string)($response['responseData']['translatedText']??''),ENT_QUOTES|ENT_HTML5,'UTF-8'));
 if($http!==200||(int)($response['responseStatus']??0)!==200||$translation===''||mb_stripos($translation,'MYMEMORY WARNING')!==false)json_out(['error'=>'Los servicios de traducción no están disponibles. Intentá más tarde.'],503);
}
if(mb_strtolower($translation)===mb_strtolower($term))json_out(['error'=>'No se pudo determinar el idioma. Elegí explícitamente Inglés → Español o Español → Inglés.'],422);
$translation=mb_substr($translation,0,800);
try{
 if(db_is_sqlite())q('INSERT OR IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,?)',[$key,$translation,gmdate('Y-m-d H:i:s')]);
 else q('INSERT IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,UTC_TIMESTAMP())',[$key,$translation]);
}catch(Throwable $e){error_log('Translation cache insert: '.$e->getMessage());}
json_out(['term'=>$term,'translation'=>$translation,'cached'=>false,'provider'=>$usedProvider,'direction'=>strtolower($source).'-'.strtolower($target)]);
