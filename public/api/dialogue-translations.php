<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/src/bootstrap.php';
header('Cache-Control: private, no-store');
$u=current_user();
if(!$u)json_out(['error'=>'Iniciá sesión.'],401);
if(!is_post())json_out(['error'=>'Método no permitido.'],405);
csrf_check();
$key=trim((string)env('GOOGLE_TRANSLATE_API_KEY',''));
if($key==='')json_out(['error'=>'Google Cloud Translation todavía no está configurado.'],503);
$input=json_decode((string)file_get_contents('php://input'),true)?:[];
$slug=(string)($input['slug']??'');
if(!preg_match('/^[a-z][12]-[0-9]{1,3}$/',$slug))json_out(['error'=>'Diálogo inválido.'],422);
$record=q_one('SELECT content_json FROM life_dialogues WHERE slug=?',[$slug]);
if(!$record)json_out(['error'=>'Diálogo no encontrado.'],404);
$scene=json_decode((string)$record['content_json'],true);
if(!is_array($scene))json_out(['error'=>'Contenido inválido.'],500);
$sentences=[];
foreach($scene['steps']??[] as $step){
  if(isset($step['npc']))$sentences[]=(string)$step['npc'];
  foreach($step['choices']??[] as $choice){
    if(isset($choice['en']))$sentences[]=(string)$choice['en'];
    if(isset($choice['reply']['en']))$sentences[]=(string)$choice['reply']['en'];
  }
}
$sentences=array_values(array_unique(array_filter($sentences,static fn($v)=>$v!=='')));
if(count($sentences)>128 || array_sum(array_map('mb_strlen',$sentences))>15000)json_out(['error'=>'Contenido demasiado extenso.'],422);
$fingerprint=hash('sha256',implode("\n",$sentences));
$cacheKey=hash('sha256','google-v2:'.$slug.':'.$fingerprint);
try{
 $pdo=db();
 $pdo->exec(db_is_sqlite()
  ? 'CREATE TABLE IF NOT EXISTS dialogue_translation_cache (cache_key TEXT PRIMARY KEY, translations_json TEXT NOT NULL)'
  : 'CREATE TABLE IF NOT EXISTS dialogue_translation_cache (cache_key VARCHAR(64) PRIMARY KEY, translations_json LONGTEXT NOT NULL)');
 $row=q_one('SELECT translations_json FROM dialogue_translation_cache WHERE cache_key=?',[$cacheKey]);
 if($row){$cached=json_decode((string)$row['translations_json'],true);if(is_array($cached)&&count($cached)===count($sentences))json_out(['translations'=>array_combine($sentences,$cached),'provider'=>'google','cached'=>true]);}
}catch(Throwable $e){error_log('Dialogue translation cache: '.$e->getMessage());json_out(['error'=>'Error de caché de traducción.'],503);}
$ch=curl_init('https://translation.googleapis.com/language/translate/v2?key='.rawurlencode($key));
curl_setopt_array($ch,[
 CURLOPT_POST=>true,
 CURLOPT_HTTPHEADER=>['Content-Type: application/json; charset=utf-8'],
 CURLOPT_POSTFIELDS=>json_encode(['q'=>$sentences,'source'=>'en','target'=>'es','format'=>'text'],JSON_UNESCAPED_UNICODE),
 CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>25,CURLOPT_FOLLOWLOCATION=>false
]);
$body=curl_exec($ch);$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
$result=json_decode((string)$body,true);
$items=$result['data']['translations']??[];
if($http!==200||count($items)!==count($sentences)){error_log('Google Translation dialogue HTTP '.$http);json_out(['error'=>'Google Translation no respondió correctamente.'],503);}
$translated=array_map(static fn($v)=>trim(html_entity_decode((string)($v['translatedText']??''),ENT_QUOTES|ENT_HTML5,'UTF-8')),$items);
if(in_array('',$translated,true))json_out(['error'=>'La traducción devolvió frases vacías.'],503);
$json=json_encode($translated,JSON_UNESCAPED_UNICODE);
try {
 if(db_is_sqlite())q('INSERT OR REPLACE INTO dialogue_translation_cache (cache_key,translations_json) VALUES (?,?)',[$cacheKey,$json]);
 else q('INSERT INTO dialogue_translation_cache (cache_key,translations_json) VALUES (?,?) ON DUPLICATE KEY UPDATE translations_json=VALUES(translations_json)',[$cacheKey,$json]);
}catch(Throwable $e){error_log('Dialogue translation save: '.$e->getMessage());}
json_out(['translations'=>array_combine($sentences,$translated),'provider'=>'google','cached'=>false]);
