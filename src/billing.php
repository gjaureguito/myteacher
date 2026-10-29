<?php
declare(strict_types=1);

function billing_conditions(): array {
    return ['consumer'=>'Consumidor final','registered'=>'Responsable inscripto','monotributo'=>'Monotributista','exempt'=>'Exento en IVA'];
}

function billing_validate(array $input, float $amount): array {
    $data=[];
    foreach (['condition','name','document_type','document','address'] as $key) {
        $value=$input['billing_'.$key]??'';
        if (!is_string($value) || strlen($value)>500) throw new RuntimeException('Revisá los datos de facturación.');
        $data[$key]=trim($value);
    }
    if (!isset(billing_conditions()[$data['condition']])) throw new RuntimeException('Seleccioná tu condición frente al IVA.');
    if ($data['name']==='' || strlen($data['name'])>190) throw new RuntimeException('Ingresá tu nombre completo o razón social (hasta 190 caracteres).');
    if (strlen($data['address'])>250) throw new RuntimeException('El domicilio admite hasta 250 caracteres.');
    if (!in_array($data['document_type'],['DNI','CUIL','CUIT'],true)) throw new RuntimeException('Seleccioná DNI, CUIL o CUIT.');
    $data['document']=str_replace(['-',' '],'',$data['document']);
    $business=$data['condition']!=='consumer';
    if ($business && ($data['document_type']!=='CUIT' || $data['address']==='')) throw new RuntimeException('Para esa condición fiscal, ingresá CUIT y domicilio de facturación.');
    if ($data['document']==='') {
        if ($business || $amount>=10000000) throw new RuntimeException('Ingresá tu documento para esta facturación.');
    } elseif ($data['document_type']==='DNI') {
        if (!preg_match('/^[0-9]{7,8}$/D',$data['document'])) throw new RuntimeException('Revisá el DNI: debe tener 7 u 8 dígitos.');
    } else {
        if (!preg_match('/^[0-9]{11}$/D',$data['document']) || preg_match('/^([0-9])\1{10}$/D',$data['document'])) throw new RuntimeException('Revisá el CUIT o CUIL: debe tener 11 dígitos válidos.');
        $sum=0;
        foreach ([5,4,3,2,7,6,5,4,3,2] as $i=>$weight) $sum+=(int)$data['document'][$i]*$weight;
        $check=11-($sum%11);
        if ($check===11) $check=0;
        if ($check===10 || $check!==(int)$data['document'][10]) throw new RuntimeException('El CUIT o CUIL no tiene un dígito verificador válido.');
    }
    return $data;
}

function billing_summary(array $data): string {
    return "Datos de facturación declarados\nCondición frente al IVA: ".(billing_conditions()[$data['condition']]??$data['condition'])."\nNombre o razón social: ".$data['name']."\nDocumento: ".($data['document']!==''?$data['document_type'].' '.$data['document']:'No informado')."\nDomicilio: ".($data['address']?:'No informado')."\nEstos datos no constituyen una factura ni acreditan el pago.\n";
}
