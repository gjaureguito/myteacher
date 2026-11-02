<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/src/bootstrap.php';
$s=tutor_settings();
$checks=[
    'schema_ready'=>(int)meta_get('schema_version')>=11,
    'provider_release_blocked'=>TUTOR_RELEASE_READY===false,
    'provider_effectively_disabled'=>tutor_enabled()===false,
    'runtime_switch_disabled'=>env('TUTOR_ENABLED','0')!=='1',
    'ars_final_price_pending'=>$s['price_ars_cents']===null,
    'pilot_paused'=>(int)$s['pilot_open']===0,
    'dictionary_preserved'=>is_file(ROOT.'/public/tutor.php')&&is_file(ROOT.'/public/assets/dictionary.js'),
    'new_professor_present'=>is_file(ROOT.'/public/profesor.php')&&is_file(ROOT.'/public/api/profesor.php')
];
echo json_encode(['checks'=>$checks,'budget_usd_cents'=>(int)$s['budget_cents'],'reserved_usd_cents'=>(int)$s['reserved_cents'],'provider_key_present'=>(bool)env('OPENAI_API_KEY'),'student_payments_count'=>(int)q_val('SELECT COUNT(*) FROM tutor_payments')],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
