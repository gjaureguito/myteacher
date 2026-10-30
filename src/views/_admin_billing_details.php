<?php $fiscal = admin_billing_data($x['billing_data'] ?? null); $missing = admin_billing_missing($fiscal); ?>
<?php if (!empty($x['billing_agreement_id'])): ?>
<details class="admin-fiscal-details"<?= ($billingExpanded ?? false) ? ' open' : '' ?>>
  <summary><strong><?= e($fiscal['name'] ?: $x['name']) ?></strong><br><span><?= e($fiscal['document'] !== '' ? $fiscal['document_type'].' '.$fiscal['document'] : 'Documento no informado') ?></span><br><small><?= $missing ? 'Datos por completar' : 'Datos declarados disponibles' ?></small></summary>
  <dl class="admin-fiscal-data">
    <dt>Nombre / razón social</dt><dd><?= e($fiscal['name'] ?: 'No informado') ?></dd>
    <dt>Documento</dt><dd><?= e($fiscal['document'] !== '' ? $fiscal['document_type'].' '.$fiscal['document'] : 'No informado') ?></dd>
    <dt>Condición frente al IVA</dt><dd><?= e(billing_conditions()[$fiscal['condition']] ?? 'No informada') ?></dd>
    <dt>Domicilio de facturación</dt><dd><?= e($fiscal['address'] ?: 'No informado') ?></dd>
    <dt>Correo de la cuenta</dt><dd><a href="mailto:<?= e($x['email']) ?>"><?= e($x['email']) ?></a></dd>
    <dt>Última declaración</dt><dd><?= e($x['billing_updated_at']) ?> UTC</dd>
  </dl>
  <?php if ($missing): ?><p class="fine">Sin informar: <?= e(implode(', ', $missing)) ?>.</p><?php endif; ?>
  <a href="/agreement.php?id=<?= (int)$x['billing_agreement_id'] ?>">Ver constancia original</a>
</details>
<?php else: ?>
<strong>Sin datos fiscales declarados</strong><p class="fine">Correo de contacto: <a href="mailto:<?= e($x['email']) ?>"><?= e($x['email']) ?></a></p>
<?php endif; ?>
