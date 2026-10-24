<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$catalog = [];
$user = current_user();
foreach (scenes() as $scene) {
    $unit = q_one('SELECT * FROM units WHERE slug=? AND is_published=1',[$scene['unit']]);
    if (!$unit) continue;
    $scene['unit_id']=(int)$unit['id'];
    $scene['free']=is_demo_unit($unit);
    $scene['access']=can_access_unit($user,$unit);
    $scene['solved']=$user ? (int)array_sum(array_column(scene_progress((int)$user['id'],$scene['id']),'solved')) : null;
    $catalog[]=$scene;
}
render('scene_catalog',compact('catalog','user'),'Inglés para situaciones reales');
