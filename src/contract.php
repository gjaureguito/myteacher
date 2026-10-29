<?php
declare(strict_types=1);

const PURCHASE_CONTRACT_VERSION = '2026-10-29.1';

function legal_provider(): array {
    return [
        'name'=>trim((string)env('LEGAL_OWNER','')),
        'cuit'=>trim((string)env('LEGAL_CUIT','23-23927605-9')),
        'address'=>trim((string)env('LEGAL_ADDRESS','Barrio Natania 6, Manzana A, Casa 7, CP 5400, Rivadavia, San Juan, Argentina')),
        'email'=>trim((string)env('CONTACT_EMAIL','')),
    ];
}
function legal_provider_ready(): bool {
    $p=legal_provider();
    return $p['name']!=='' && preg_match('/^\d{11}$/',preg_replace('/\D/','',$p['cuit']))===1
        && $p['address']!=='' && filter_var($p['email'],FILTER_VALIDATE_EMAIL)!==false;
}
function purchase_kind(string $kind): string {
    $mode=mp_mode();
    return $mode==='both' ? ($kind==='once'?'once':'subscription') : $mode;
}
function purchase_contract(array $user,string $kind): array {
    $p=legal_provider();$kind=purchase_kind($kind);$days=mp_once_days();$price=plan_price();
    $counts=q_one('SELECT (SELECT COUNT(*) FROM units WHERE is_published=1) AS units,(SELECT COUNT(*) FROM exercises e JOIN units u ON u.id=e.unit_id WHERE u.is_published=1) AS exercises');
    $sections=[
        '1. Partes y objeto'=>"Prestador: {$p['name']}. CUIT: {$p['cuit']}. Domicilio: {$p['address']}. Contacto: {$p['email']}. Marca del servicio: ".app_name().". Contratante: {$user['name']} ({$user['email']}). Contratás acceso personal a una plataforma de autoestudio de inglés; la cuenta no es transferible.",
        '2. Contenidos y actividades'=>"El catálogo publicado al revisar esta contratación contiene {$counts['units']} unidades y {$counts['exercises']} ejercicios de base, organizados en seis niveles A1–C2, además de talleres y situaciones multimedia. Incluye teoría, ejemplos, práctica, respuestas comentadas y seguimiento. Podés consultar el programa y la demo antes de pagar. Las mejoras posteriores pueden incorporar o corregir contenidos; no autorizan a reducir lo contratado sin respetar los derechos que correspondan.",
        '3. Alcance educativo y límites de las herramientas'=>"El servicio es de práctica autónoma: no incluye clases en vivo, tutorías individuales ni una certificación oficial de dominio del idioma. No promete fluidez, aprobación de exámenes, empleo ni resultados en un plazo fijo. El avance depende también del nivel inicial, la dedicación y el uso de los materiales. Los ejemplos y actividades son didácticos, incluso cuando tratan asuntos laborales, jurídicos, económicos o de salud: no constituyen asesoramiento profesional. El corrector básico detecta patrones limitados; puede omitir errores o sugerir cambios inadecuados. Compará sus sugerencias con el contexto antes de aplicarlas. La ausencia de observaciones no certifica que un texto sea correcto.",
        '4. Revisión del material'=>"Los materiales y las respuestas automáticas pueden requerir correcciones. Podés informar inconsistencias al contacto del prestador y solicitar revisión. Conocer estas limitaciones no implica aceptar defectos, renunciar a la calidad ofrecida ni perder el derecho a reclamar por incumplimientos.",
        '5. Precio, modalidad y acceso'=>$kind==='subscription'
            ? 'Precio final: '.money_ars($price).' en pesos argentinos por mes. Suscripción con renovación y débito automáticos mensuales mediante Mercado Pago hasta que la canceles. La pantalla de Mercado Pago debe coincidir con este importe y modalidad; si no coincide, no confirmes y contactanos. El acceso pago se habilita al confirmarse el cobro. El prestador emitirá el comprobante fiscal correspondiente. Los cambios de precio se comunicarán por email con al menos treinta días de anticipación, respetando las autorizaciones y derechos aplicables; esta aceptación no autoriza cargos distintos de los informados.'
            : 'Precio final: '.money_ars($price).' en pesos argentinos por '.$days.' días de acceso desde la confirmación del pago. Es un pago único, sin renovación ni débito automáticos. Los días se acreditan conforme al período contratado; si ya hay acceso vigente, se agregan al vencimiento. Mercado Pago procesa el cobro. No confirmes importes o modalidades distintos de los mostrados aquí.',
        '6. Cancelación y arrepentimiento'=>"Podés solicitar la baja por los medios electrónicos del sitio y, para suscripciones activas, desde Mi cuenta. La cancelación de futuras renovaciones permite usar el período ya pagado, sin afectar reintegros que correspondan por otros motivos. En contrataciones a distancia se reconoce el derecho de revocación dentro del plazo legal de diez días corridos, conforme al artículo 34 de la Ley 24.240 y al artículo 1110 del Código Civil y Comercial, con el cómputo y efectos que establezca la normativa aplicable. Podés ejercerlo desde el Botón de arrepentimiento, sin cuenta previa, o por correo a {$p['email']}. No se condiciona a explicar los motivos ni se cobra una penalidad por ejercer derechos legales. Dentro de las veinticuatro horas siguientes a la solicitud se informará su código y se adoptarán las medidas necesarias para efectivizar la baja o revocación que corresponda. La baja ordinaria no genera por sí sola la devolución del período abonado que continúe disponible, sin afectar reintegros legalmente exigibles. Si el vencimiento del plazo de arrepentimiento ocurre en día inhábil, se extiende al primer día hábil siguiente. Las excepciones legales o reglamentarias solo se aplicarán cuando correspondan al servicio y las circunstancias concretas; la mera creación de una cuenta o inicio de sesión no implica renuncia. Podrán realizarse verificaciones razonables de identidad y seguridad por medios habituales.",
        '7. Uso, acceso y disponibilidad'=>"Necesitás conexión a Internet y un navegador actualizado. Audio, micrófono y reconocimiento de voz dependen de compatibilidad, permisos y servicios del dispositivo o navegador. Los borradores que indiquen guardado local quedan en ese dispositivo; los que se indiquen temporales deben copiarse antes de salir. Conservá copias de los textos que necesites. Mantené tus credenciales protegidas y cerrá sesión en equipos compartidos. Las tareas de mantenimiento o fallas de terceros se atenderán razonablemente y no eliminan obligaciones legales del prestador ni los remedios que correspondan.",
        '8. Responsabilidad y derechos preservados'=>"La descripción del alcance educativo no es una exclusión general de responsabilidad. El prestador responde por sus obligaciones y por los daños en los términos de la normativa aplicable. No se excluye responsabilidad que legalmente no pueda limitarse, no se invierte la carga de la prueba ni se exige renunciar a reclamos, indemnizaciones, derechos de consumo o protección de datos. Las situaciones futuras se resolverán según sus circunstancias y la ley; esta conformidad no es un desistimiento anticipado. Una cláusula inválida no afecta los derechos imperativos ni el resto del contrato cuando pueda subsistir.",
        '9. Privacidad y constancia'=>"Se tratan los datos necesarios para la cuenta, el acceso, el progreso, el pago y la constancia de aceptación, según la Política de privacidad. No guardamos datos de tarjeta. Se conserva la fecha, el importe, la modalidad, la versión y el texto que aceptaste. Podés consultar y descargar tu copia desde Mi cuenta. La aceptación de este contrato no es una autorización para publicidad ni para compartir datos con fines ajenos al servicio.",
        '10. Consultas y normativa'=>"Podés presentar consultas o reclamos a {$p['email']}. Se aplica la legislación argentina y se mantienen las vías administrativas y judiciales y las reglas de competencia protectorias del consumidor. No se impone arbitraje obligatorio ni una jurisdicción que restrinja esos derechos. Declará contar con capacidad legal para contratar; cuando corresponda, la contratación debe realizarla quien ejerza la representación legal. La casilla de conformidad se refiere a las condiciones informadas y no sustituye derechos indisponibles.",
    ];
    $text='Contrato de prestación del servicio MyTeacher · Versión '.PURCHASE_CONTRACT_VERSION."\n\n";
    foreach($sections as $heading=>$body)$text.=$heading."\n".$body."\n\n";
    return ['version'=>PURCHASE_CONTRACT_VERSION,'sections'=>$sections,'text'=>$text,'hash'=>hash('sha256',$text),
        'kind'=>$kind,'amount'=>$price,'days'=>$kind==='once'?$days:0];
}
