<?php
declare(strict_types=1);

/** Seven days from registration, in UTC. */
function trial_end(string $start): string
{
    return (new DateTimeImmutable($start, new DateTimeZone('UTC')))->modify('+7 days')->format('Y-m-d H:i:s');
}

function trial_active(?array $u): bool
{
    return $u && !empty($u['trial_ends_at']) && $u['trial_ends_at'] > now_utc();
}

function trial_notice(array $u): void
{
    if (is_admin($u) || empty($u['trial_started_at'])) return;
    $paid = !empty($u['access_until']) && $u['access_until'] > now_utc();
    if ($paid) return;
    echo '<aside class="panel" aria-label="Tu prueba gratuita"><h2>' . (trial_active($u) ? 'Tu prueba gratuita está activo' : 'Tu prueba gratuita terminó') . '</h2><p>';
    if (trial_active($u)) echo 'Tenés acceso completo hasta el ' . e(fmt_date($u['trial_ends_at'])) . '. ';
    echo 'No se realiza ningún cobro por terminar la prueba. Para continuar con acceso completo, elegí y pagá tu plan en Mercado Pago. Tu progreso se conserva.</p><a class="btn" href="/account.php">Ver precio y opciones de pago</a></aside>';
}
