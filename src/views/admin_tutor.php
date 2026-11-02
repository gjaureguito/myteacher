<link rel="stylesheet" href="/assets/profesor.css?v=1">
<section class="wrap page professor-page"><?php require ROOT.'/src/views/admin_nav.php'; ?>
<h1>Profesor con IA · administración</h1>
<p>Activación pendiente de completar y probar el servicio de voz y cobro. En esta publicación, el piloto no puede activarse desde el panel, aunque se guarden permisos y límites. La inscripción y los registros administrativos sí están disponibles.</p>
<p>Estado efectivo: <strong><?= tutor_enabled()?'habilitado':'apagado' ?></strong>. Clave del proveedor: <?= env('OPENAI_API_KEY')?'configurada':'pendiente' ?>. Permiso del servidor: <?= env('TUTOR_ENABLED','0')==='1'?'habilitado':'apagado' ?>.</p>
<p><?= count($members) ?> inscripciones · <?= (int)q_val("SELECT COUNT(DISTINCT user_id) FROM tutor_calls WHERE status='ok'") ?> estudiantes con respuesta · <?= (int)q_val('SELECT COUNT(*) FROM tutor_members WHERE recharge_requested_at IS NOT NULL') ?> interesados en recargar.</p>
<p><strong>Reserva consumida: USD <?= number_format($reserved/100,2) ?> / <?= number_format($settings['budget_cents']/100,2) ?></strong>. Disponible para nuevas reservas: USD <?= number_format(max(0,$settings['budget_cents']-$reserved)/100,2) ?>.</p>
<p>Costo estimado con tokens informados: USD <?= number_format($known/1000000,6) ?>. Intentos pendientes o sin costo confirmado: <?= $unknown ?>.</p>
<p>Reservamos USD 0,01 por intento, incluso ante errores. La reserva limita consumo; no representa el gasto facturado. El presupuesto no se renueva automáticamente. Bajarlo por debajo de lo reservado detiene los nuevos intentos.</p>
<h2>Presupuesto y piloto</h2>
<form method="post" class="coach-goal-card"><?= csrf_field() ?><input type="hidden" name="action" value="settings">
<label>Presupuesto global acumulado (USD)<input name="budget_usd" type="number" min="0" max="1000" step="0.01" required value="<?= number_format($settings['budget_cents']/100,2,'.','') ?>"></label>
<label>Intentos diarios por alumno (UTC)<input name="daily_limit" type="number" min="0" max="1000" required value="<?= (int)$settings['daily_limit'] ?>"></label>
<label>Intentos totales por alumno en el piloto<input name="total_limit" type="number" min="0" max="10000" required value="<?= (int)$settings['total_limit'] ?>"></label>
<label>Máximo de inscriptos (0 = sin límite de plazas)<input name="max_students" type="number" min="0" max="100000" required value="<?= (int)$settings['max_students'] ?>"></label>
<label><input type="checkbox" name="pilot_open" <?= $settings['pilot_open']?'checked':'' ?>> Permitir práctica cuando el servidor tenga clave y permiso</label>
<label><input type="checkbox" name="enroll_open" <?= $settings['enroll_open']?'checked':'' ?>> Aceptar nuevas inscripciones</label>
<h3>Oferta propuesta · venta deshabilitada</h3>
<label>Precio final del paquete en pesos argentinos (ARS), impuestos incluidos<input name="price_ars" type="number" min="0.01" max="1000000" step="0.01" value="<?= $settings['price_ars_cents']===null?'':number_format($settings['price_ars_cents']/100,2,'.','') ?>" placeholder="Pendiente de definir"></label>
<p>Ingresá el importe final que pagará el alumno, con impuestos incluidos. No se calculan tasas ni se agregan impuestos al importe ingresado. Vacío deja el precio pendiente. No hay conversión automática desde dólares. El presupuesto y los costos de OpenAI se mantienen en USD.</p>
<label>Minutos del paquete<input name="pack_minutes" type="number" min="1" max="60" required value="<?= (int)($settings['pack_seconds']/60) ?>"></label>
<p>Guardar no activa cobros ni cambia la cuota mensual. Aumentar el presupuesto permite más consumo de API; no compra créditos de OpenAI. No cambia reservas anteriores ni pagos ya registrados.</p><button class="btn" type="submit">Guardar controles y propuesta</button></form>
<h2>Límites por estudiante</h2><p>Vacío usa el límite general. Cero bloquea los nuevos intentos de ese período. El consumo no se reinicia al cambiar los límites.</p>
<?php foreach($members as $m): $member=q_one('SELECT * FROM tutor_members WHERE user_id=?',[$m['user_id']]);$limits=tutor_quota((int)$m['user_id']); ?>
<details class="coach-goal-card"><summary><?= e($m['email']) ?> · <?= (int)$m['calls'] ?>/<?= $limits['total_limit'] ?> intentos · <?= (int)$m['successes'] ?> respuestas</summary>
<p>Costo estimado USD <?= number_format($m['cost']/1000000,6) ?>. Saldo de voz registrado: <?= number_format(tutor_registered_seconds((int)$m['user_id'])/60,2) ?> minutos; consumo pago deshabilitado.</p>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="member"><input type="hidden" name="user_id" value="<?= (int)$m['user_id'] ?>">
<label>Límite diario particular<input name="daily_limit" type="number" min="0" max="1000" value="<?= e($member['daily_limit']===null?'':(string)$member['daily_limit']) ?>" placeholder="General: <?= (int)$settings['daily_limit'] ?>"></label>
<label>Límite total particular<input name="total_limit" type="number" min="0" max="10000" value="<?= e($member['total_limit']===null?'':(string)$member['total_limit']) ?>" placeholder="General: <?= (int)$settings['total_limit'] ?>"></label>
<label><input type="checkbox" name="blocked" <?= $member['blocked']?'checked':'' ?>> Pausar acceso de este alumno</label><button class="btn" type="submit">Guardar límites del alumno</button></form></details><?php endforeach; ?>
<h2>Recargas y pagos registrados</h2>
<p>Registro de comprobantes existentes, separado de la suscripción. No inicia cobros ni consulta Mercado Pago. Un pago informado no suma saldo; solo una verificación manual lo incorpora al saldo registrado. Este saldo todavía no puede consumirse. Anular un registro no devuelve dinero: gestioná cualquier devolución en el medio de pago.</p>
<form method="post" class="coach-goal-card"><?= csrf_field() ?><input type="hidden" name="action" value="record_payment">
<label>Alumno<select name="user_id" required><option value="">Elegir estudiante</option><?php foreach($members as $m): ?><option value="<?= (int)$m['user_id'] ?>"><?= e($m['email']) ?></option><?php endforeach; ?></select></label>
<label>Referencia única del comprobante<input name="reference" maxlength="100" required></label>
<label>Importe final comprobado en pesos argentinos (ARS), impuestos incluidos<input name="amount_ars" type="number" min="0.01" max="1000000" step="0.01" required></label>
<label>Minutos acordados de esta recarga<input name="minutes" type="number" min="1" max="600" required></label>
<label>Comprobante y medio de pago, sin datos bancarios sensibles<textarea name="evidence" minlength="8" maxlength="500" required></textarea></label>
<button class="btn btn-ghost" type="submit">Registrar comprobante pendiente de revisión</button></form>
<p>Últimos 100 registros. El saldo considera todos los pagos verificados.</p>
<?php foreach($payments as $p): ?><details class="coach-goal-card"><summary>#<?= (int)$p['id'] ?> · <?= e($p['email']) ?> · <?= e($p['currency']) ?> <?= number_format($p['amount_cents']/100,2,',','.') ?> · <?= (int)($p['seconds']/60) ?> min · <?= e(['reported'=>'informado','verified_manual'=>'verificado manualmente','voided'=>'anulado'][$p['status']]??$p['status']) ?></summary>
<p>Referencia: <?= e($p['reference']) ?> · <?= e($p['created_at']) ?> UTC</p><p><?= e($p['evidence']) ?></p>
<?php if($p['currency']==='USD'): ?><p>Registro histórico en USD: conserva su importe y moneda originales; no se convirtió a pesos ni se reinterpretó su tratamiento impositivo.</p><?php endif; ?>
<?php if($p['status']!=='voided'): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="payment_status"><input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>">
<label>Acción<select name="status"><?php if($p['status']==='reported'): ?><option value="verified_manual">Confirmar comprobación manual del pago</option><?php endif; ?><option value="voided">Anular registro y retirar saldo registrado</option></select></label>
<label><input type="checkbox" name="confirmed" required> Revisé el movimiento en el medio de pago; aquí solo se actualiza el registro.</label><button class="btn btn-ghost" type="submit">Actualizar registro</button></form><?php endif; ?></details><?php endforeach; ?>
<h2>Créditos de OpenAI</h2><p>Saldo y recarga automática del proveedor: <strong>estado no consultado desde esta página</strong>. Se configuran en tu cuenta de OpenAI. Este panel no cambia su facturación.</p><a class="btn btn-ghost" href="https://platform.openai.com/settings/organization/billing/overview" target="_blank" rel="noopener noreferrer">Abrir facturación de OpenAI ↗</a>
<h2>Últimos cambios administrativos</h2><ul><?php foreach($audit as $a): ?><li>#<?= (int)$a['id'] ?> · <?= e($a['created_at'].' UTC · '.$a['email'].' · '.(['settings'=>'Configuración general','member'=>'Límites de alumno','record_payment'=>'Comprobante registrado','payment_status'=>'Estado de pago'][$a['action']]??$a['action'])) ?></li><?php endforeach; ?></ul>
</section>
