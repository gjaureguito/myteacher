<section class="wrap page legal">
 <p class="crumbs"><a href="/account.php">Mi cuenta</a> / Antes de pagar</p>
 <h1>Revisá tu contratación</h1>
 <p>Leé el alcance del servicio y las condiciones antes de continuar a Mercado Pago. Todavía no se realiza ningún cobro.</p>
 <div class="panel"><h2><?= money_ars($contract['amount']) ?> <?= $contract['kind']==='once'?'por '.$contract['days'].' días':'por mes' ?></h2><p><?= $contract['kind']==='once'?'Pago único, sin renovación automática.':'Suscripción mensual con débito automático hasta la cancelación.' ?></p><a href="/program.php" target="_blank" rel="noopener">Consultar programa y actividades</a> · <a href="/dashboard.php" target="_blank" rel="noopener">Explorar la demo</a></div>
 <article class="purchase-document" aria-label="Contrato de prestación del servicio">
  <p class="fine">Versión <?= e($contract['version']) ?></p>
  <?php foreach($contract['sections'] as $heading=>$body): ?><section><h2><?= e($heading) ?></h2><p><?= e($body) ?></p></section><?php endforeach; ?>
 </article>
 <p><a href="/privacy.php" target="_blank" rel="noopener">Política de privacidad</a> · <a href="/consumer-request.php">Arrepentimiento y baja</a></p>
 <?php if(!legal_provider_ready()): ?><p class="flash flash-info">El prestador debe completar sus datos de identificación y contacto antes de habilitar nuevas contrataciones.</p><?php elseif(!mp_enabled()): ?><p>El pago online no está disponible en este momento.</p><?php else: ?>
 <form method="post" action="/subscribe.php" class="panel form">
  <?= csrf_field() ?><input type="hidden" name="kind" value="<?= e($contract['kind']) ?>"><input type="hidden" name="review_nonce" value="<?= e($nonce) ?>"><input type="hidden" name="document_hash" value="<?= e($contract['hash']) ?>">
  <fieldset><legend>Datos de facturación</legend>
   <p>Usaremos estos datos para gestionar tu comprobante fiscal. Revisalos antes de continuar.</p>
   <label for="billing-condition">Condición frente al IVA</label>
   <select id="billing-condition" name="billing_condition" required><?php foreach(billing_conditions() as $value=>$label): ?><option value="<?= e($value) ?>" <?= ($billing['billing_condition']??'')===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select>
   <label for="billing-name">Nombre completo o razón social</label><input id="billing-name" name="billing_name" value="<?= e($billing['billing_name']??'') ?>" maxlength="190" autocomplete="billing name" required>
   <label for="billing-type">Tipo de documento</label><select id="billing-type" name="billing_document_type"><?php foreach(['DNI','CUIL','CUIT'] as $type): ?><option <?= ($billing['billing_document_type']??'')===$type?'selected':'' ?>><?= e($type) ?></option><?php endforeach; ?></select>
   <label for="billing-document">Número de documento</label><input id="billing-document" name="billing_document" value="<?= e($billing['billing_document']??'') ?>" inputmode="numeric" maxlength="15" aria-describedby="billing-help">
   <p id="billing-help" class="fine">Responsables inscriptos, monotributistas y exentos: CUIT y domicilio obligatorios. Consumidor final: documento opcional salvo que corresponda identificarte por el importe. Si solicitás identificar el comprobante para una deducción en Ganancias, informá tu CUIT. Informarlo no garantiza que el gasto sea deducible.</p>
   <label for="billing-address">Domicilio de facturación</label><input id="billing-address" name="billing_address" value="<?= e($billing['billing_address']??'') ?>" maxlength="250" autocomplete="billing street-address">
  </fieldset>
  <script>
  (()=>{const condition=document.getElementById('billing-condition'),type=document.getElementById('billing-type'),doc=document.getElementById('billing-document'),address=document.getElementById('billing-address');const sync=()=>{const business=condition.value!=='consumer';for(const option of type.options)option.disabled=business&&option.value!=='CUIT';if(business)type.value='CUIT';doc.required=business||<?= $contract['amount']>=10000000?'true':'false' ?>;address.required=business;};condition.addEventListener('change',sync);sync();})();
  </script>
  <label class="check"><input type="checkbox" name="agreement" value="1" required><span>Leí y acepto este contrato, conozco el alcance y las limitaciones informadas y la política de privacidad. Esta aceptación no implica renunciar a mis derechos como consumidor.</span></label>
  <button class="btn btn-mp">Aceptar y continuar a Mercado Pago</button><p class="fine">La autorización de pago se confirma en Mercado Pago. Guardaremos una copia de estas condiciones en tu cuenta.</p>
 </form><?php endif; ?>
</section>
