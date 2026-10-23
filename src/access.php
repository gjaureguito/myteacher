<?php
declare(strict_types=1);

/*
 * Reglas de acceso:
 *  - Suscripción vigente (access_until en el futuro) o admin → acceso total.
 *  - Cuenta registrada sin suscripción → demo: primera unidad de cada nivel + test de nivel.
 */

function has_full_access(?array $u): bool
{
    if (!$u) return false;
    if (is_admin($u)) return true;
    return !empty($u['access_until']) && $u['access_until'] > now_utc();
}

function is_demo_unit(array $unit): bool
{
    return (int) $unit['position'] === 1;
}

function can_access_unit(?array $u, array $unit): bool
{
    if (!$u) return false;
    return has_full_access($u) || is_demo_unit($unit);
}

function sub_label(array $u): string
{
    if (is_admin($u)) return 'Administrador';
    if (has_full_access($u)) {
        return $u['sub_status'] === 'cancelled'
            ? 'Cancelada · acceso hasta ' . fmt_date($u['access_until'])
            : 'Activa hasta ' . fmt_date($u['access_until']);
    }
    return 'Demo gratuita';
}
