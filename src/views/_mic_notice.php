<?php
/* Aviso plegable sobre bloqueos del micrófono (antivirus, firewall, Windows). Se incluye una vez por página con micrófono. */
if (!empty($GLOBALS['__micNoticeShown'])) return;
$GLOBALS['__micNoticeShown'] = true;
?>
<details class="mic-notice">
  <summary><span aria-hidden="true">🎙️</span> ¿«Decilo vos» no te escucha? Puede ser tu antivirus</summary>
  <div class="mic-notice-body">
    <p>MyTeacher prueba solo todos los micrófonos de tu equipo. Si igual no te escucha, casi siempre es porque <strong>un programa de tu computadora no deja que el navegador use el micrófono</strong>. El sitio no puede cambiar esa configuración: hay que permitirlo una sola vez.</p>
    <ul>
      <li><strong>Antivirus o programa de seguridad.</strong> Kaspersky, ESET, Avast, AVG, Norton, Bitdefender y McAfee (sobre todo las versiones «Internet Security», «Total» o «Premium») tienen una <em>protección de micrófono</em> que puede bloquear al navegador. Abrí el antivirus, buscá «micrófono» y permití que <?= e(preg_match('/Edg\//', $_SERVER['HTTP_USER_AGENT'] ?? '') ? 'Edge' : (preg_match('/Firefox\//', $_SERVER['HTTP_USER_AGENT'] ?? '') ? 'Firefox' : 'Chrome')) ?> lo use.</li>
      <li><strong>¿Y el firewall?</strong> El firewall controla las conexiones a internet, no el micrófono. Si el resto de MyTeacher funciona, el firewall no es el problema: revisá la protección de micrófono del mismo programa de seguridad.</li>
      <li><strong>Privacidad de Windows.</strong> En Configuración › Privacidad y seguridad › Micrófono, activá «Acceso al micrófono» y «Permitir que las aplicaciones de escritorio accedan al micrófono». <a href="ms-settings:privacy-microphone">Abrir ese ajuste</a></li>
      <li><strong>Otro programa usando el micrófono.</strong> Cerrá Zoom, Teams, Meet o Discord y probá de nuevo.</li>
    </ul>
    <p>Cuando lo habilites, volvé a tocar «Decilo vos»: no hace falta recargar la página. Mientras tanto podés seguir practicando: escuchá la frase y repetila en voz alta.</p>
  </div>
</details>
