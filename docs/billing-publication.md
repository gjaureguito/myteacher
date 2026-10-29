# Contrato y facturación antes del pago

El usuario revisa el contrato y sus datos fiscales en /checkout.php. El servidor exige aceptación, versión y precio vigentes, y una revisión no vencida antes de crear el pago. Cada aceptación y sus datos fiscales se conservan juntos; solo el titular o un administrador pueden consultar la copia.

La migración 5 agrega purchase_agreements, purchase_billing y consumer_requests sin modificar usuarios ni pagos existentes. El prestador puede reemplazar los datos de identificación mediante LEGAL_OWNER, LEGAL_CUIT, LEGAL_ADDRESS y CONTACT_EMAIL.

El panel /admin/billing.php muestra datos declarados, no facturas emitidas. ARCA no está integrada. Las solicitudes públicas de baja/arrepentimiento generan una constancia inmediata y deben gestionarse en /admin/consumer_requests.php dentro de los plazos legales; registrar una solicitud no ejecuta una devolución ni una baja en Mercado Pago.

Validación: php tests/billing.php y python tests/mercadopago.py (API simulada, sin pagos reales).
