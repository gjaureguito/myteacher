<?php
declare(strict_types=1);
require dirname(__DIR__,2).'/src/bootstrap.php';
header('Cache-Control: no-store');
$u=current_user();
if(!$u)json_out(['error'=>'Iniciá sesión.'],401);
try{
 $pdo=db();
 $pdo->exec(db_is_sqlite()
 ? 'CREATE TABLE IF NOT EXISTS user_glossary (id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER NOT NULL,term TEXT NOT NULL,translation TEXT NOT NULL,context TEXT NOT NULL DEFAULT \'\',category TEXT NOT NULL DEFAULT \'General\',created_at TEXT NOT NULL,UNIQUE(user_id,term))'
 : 'CREATE TABLE IF NOT EXISTS user_glossary (id BIGINT AUTO_INCREMENT PRIMARY KEY,user_id BIGINT NOT NULL,term VARCHAR(220) NOT NULL,translation TEXT NOT NULL,context TEXT NOT NULL,category VARCHAR(80) NOT NULL DEFAULT \'General\',created_at DATETIME NOT NULL,UNIQUE KEY unique_user_term(user_id,term))');
}catch(Throwable $e){error_log('Glossary init: '.$e->getMessage());json_out(['error'=>'No se pudo abrir el glosario.'],503);}
$id=(int)$u['id'];
if($_SERVER['REQUEST_METHOD']==='GET'){
 $rows=q_all('SELECT id,term AS word,translation,context,category AS tag,created_at AS created FROM user_glossary WHERE user_id=? ORDER BY LOWER(term),id',[$id]);
 json_out(['entries'=>$rows]);
}
if(!is_post())json_out(['error'=>'Método no permitido.'],405);
csrf_check();
$input=json_decode((string)file_get_contents('php://input'),true)?:[];
$action=(string)($input['action']??'save');
if($action==='delete'){
 $term=trim((string)($input['term']??''));
 if($term==='')json_out(['error'=>'Falta el término.'],422);
 q('DELETE FROM user_glossary WHERE user_id=? AND term=?',[$id,$term]);
 json_out(['ok'=>true]);
}
if($action!=='save')json_out(['error'=>'Acción inválida.'],422);
$term=trim(preg_replace('/\s+/u',' ',(string)($input['term']??'')));
$translation=trim((string)($input['translation']??''));
$context=trim((string)($input['context']??''));
$category=trim((string)($input['category']??'General'));
if(mb_strlen($term)<1||mb_strlen($term)>220||mb_strlen($translation)<1||mb_strlen($translation)>800||mb_strlen($context)>1000||mb_strlen($category)>80)json_out(['error'=>'Revisá la longitud de los campos.'],422);
if($category==='')$category='General';
try{
 if(db_is_sqlite())q('INSERT INTO user_glossary(user_id,term,translation,context,category,created_at) VALUES (?,?,?,?,?,?) ON CONFLICT(user_id,term) DO UPDATE SET translation=excluded.translation,context=excluded.context,category=excluded.category',[$id,$term,$translation,$context,$category,gmdate('Y-m-d H:i:s')]);
 else q('INSERT INTO user_glossary(user_id,term,translation,context,category,created_at) VALUES (?,?,?,?,?,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE translation=VALUES(translation),context=VALUES(context),category=VALUES(category)',[$id,$term,$translation,$context,$category]);
 json_out(['ok'=>true]);
}catch(Throwable $e){error_log('Glossary save: '.$e->getMessage());json_out(['error'=>'No se pudo guardar el término.'],503);}
