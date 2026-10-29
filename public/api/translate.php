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
// Única fuente de traducción: Google Cloud Translation.
if($googleKey==='')json_out(['error'=>'La traducción no está configurada.'],503);
$key=hash('sha256','google:'.strtolower($source).'-'.strtolower($target).':'.mb_strtolower($term));
try {
 $pdo=db();
 $pdo->exec(db_is_sqlite()?
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key TEXT PRIMARY KEY,translation TEXT NOT NULL,created_at TEXT NOT NULL)':
 'CREATE TABLE IF NOT EXISTS translation_cache(term_key VARCHAR(220) PRIMARY KEY,translation TEXT NOT NULL,created_at DATETIME NOT NULL)');
 $cached=q_one('SELECT translation FROM translation_cache WHERE term_key=?',[$key]);
 if($cached && trim((string)$cached['translation'])!=='' && mb_strtolower(trim((string)$cached['translation']))!==mb_strtolower($term) && $direction!=='auto')json_out(['term'=>$term,'translation'=>$cached['translation'],'cached'=>true,'provider'=>'google','direction'=>strtolower($source).'-'.strtolower($target)]);
}catch(Throwable $e){error_log('Translation cache: '.$e->getMessage());json_out(['error'=>'El servicio de traducción no está disponible.'],503);}
// En modo automático Google detecta el idioma: español se traduce al inglés; el resto, al español.
$google=function(string $to,?string $from) use($googleKey,$term):array{
 $payload=['q'=>[$term],'target'=>$to,'format'=>'text'];
 if($from!==null)$payload['source']=$from;
 $ch=curl_init('https://translation.googleapis.com/language/translate/v2?key='.rawurlencode($googleKey));
 curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_HTTPHEADER=>['Content-Type: application/json; charset=utf-8'],CURLOPT_POSTFIELDS=>json_encode($payload,JSON_UNESCAPED_UNICODE),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false]);
 $body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
 $item=(json_decode((string)$body,true)['data']['translations'][0]??[]);
 $text=trim(html_entity_decode((string)($item['translatedText']??''),ENT_QUOTES|ENT_HTML5,'UTF-8'));
 return [$text,strtolower((string)($item['detectedSourceLanguage']??'')),$http];
};
if($direction==='auto'){
 [$translation,$detected,$http]=$google('es',null);
 if($http===200&&str_starts_with($detected,'es')){
  [$translation,,$http]=$google('en','es');$source='ES';$target='EN';
 }elseif($http===200){$source='EN';$target='ES';}
}else{
 [$translation,,$http]=$google(strtolower($target),strtolower($source));
}
if($http!==200||$translation===''){error_log('Google translation HTTP '.$http);json_out(['error'=>'Google Translate no respondió. Intentá nuevamente en unos minutos.'],503);}
if(mb_strtolower($translation)===mb_strtolower($term))json_out(['error'=>'No se pudo determinar el idioma. Elegí explícitamente Inglés → Español o Español → Inglés.'],422);
$translation=mb_substr($translation,0,800);
$key=hash('sha256','google:'.strtolower($source).'-'.strtolower($target).':'.mb_strtolower($term));
try{
 if(db_is_sqlite())q('INSERT OR IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,?)',[$key,$translation,gmdate('Y-m-d H:i:s')]);
 else q('INSERT IGNORE INTO translation_cache(term_key,translation,created_at) VALUES (?,?,UTC_TIMESTAMP())',[$key,$translation]);
}catch(Throwable $e){error_log('Translation cache insert: '.$e->getMessage());}
json_out(['term'=>$term,'translation'=>$translation,'cached'=>false,'provider'=>'google','direction'=>strtolower($source).'-'.strtolower($target)]);
