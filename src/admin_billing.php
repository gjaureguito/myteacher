<?php
declare(strict_types=1);
require_once __DIR__ . '/billing.php';

/** Most recent declared billing snapshot, one row per user. */
function admin_billing_join(): string
{
    return ' LEFT JOIN purchase_agreements ba ON ba.id=(SELECT MAX(pa.id) FROM purchase_agreements pa JOIN purchase_billing pb ON pb.nonce=pa.nonce AND pb.user_id=pa.user_id WHERE pa.user_id=u.id)
             LEFT JOIN purchase_billing b ON b.nonce=ba.nonce AND b.user_id=u.id ';
}

function admin_billing_fields(): string
{
    return 'ba.id AS billing_agreement_id, ba.accepted_at AS billing_updated_at, ba.amount AS billing_amount, ba.currency AS billing_currency, b.billing_data';
}

function admin_billing_data(?string $json): array
{
    $decoded = json_decode($json ?? '', true);
    $out = [];
    foreach (['condition','name','document_type','document','address'] as $key) {
        $out[$key] = is_array($decoded) && is_string($decoded[$key] ?? null) ? trim($decoded[$key]) : '';
    }
    return $out;
}

function admin_billing_missing(array $data): array
{
    $missing = [];
    foreach (['name'=>'Nombre o razón social', 'document'=>'Documento', 'address'=>'Domicilio'] as $key=>$label) {
        if ($data[$key] === '') $missing[] = $label;
    }
    if (!isset(billing_conditions()[$data['condition']])) $missing[] = 'Condición fiscal';
    return $missing;
}

function admin_subscription_label(string $status): string
{
    return ['active'=>'Activa','authorized'=>'Autorizada','pending'=>'Pendiente','cancelled'=>'Cancelada','paused'=>'Pausada','manual'=>'Acceso manual','none'=>'Sin suscripción'][$status] ?? $status;
}
