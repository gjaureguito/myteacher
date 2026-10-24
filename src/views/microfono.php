<?php
/** Guía de solución del micrófono: diagnóstico automático + comandos exactos por problema y sistema. */
$hk = 'HKCU:\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone';
$hm = 'HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone';
$site = app_url('/');
$cmd = function (string $code, string $where, string $what) {
    ?>
    <div class="cmd">
      <div class="cmd-head"><span class="cmd-where"><?= e($where) ?></span><button type="button" class="btn btn-ghost btn-sm" data-copy-text="<?= e($code) ?>">Copiar</button></div>
      <pre><code><?= e($code) ?></code></pre>
      <p class="cmd-what"><?= $what ?></p>
    </div>
    <?php
};
?>
<section class="wrap page mic-page">
  <p class="eyebrow">SOPORTE / MICRÓFONO</p>
  <h1>Solucionar el micrófono</h1>
  <div data-edge-help></div>
  <p class="lead">Para "Decilo vos" y los ejercicios de speaking, el navegador necesita usar tu micrófono. Empezá por el diagnóstico: te dice qué pasa y te lleva al comando exacto.</p>

  <div class="mic-diag" id="mic-diag">
    <div><strong>Diagnóstico automático</strong><p class="muted">Prueba tu micrófono en este navegador. Si te pregunta, tocá <b>Permitir</b>.</p></div>
    <button type="button" class="btn" data-mic-diag>Diagnosticar mi micrófono</button>
    <div class="mh-status" data-diag-out hidden></div>
    <div class="mh-meter" data-diag-meter hidden><i></i></div>
  </div>

  <nav class="mic-toc" aria-label="Problemas">
    <a href="#win-privacidad">Windows: permiso de privacidad</a>
    <a href="#win-audio">Windows: el micrófono no arranca</a>
    <a href="#win-ocupado">Windows: otro programa lo usa</a>
    <a href="#win-deshabilitado">Windows: micrófono deshabilitado</a>
    <a href="#win-driver">Windows: driver de audio</a>
    <a href="#navegador">Permiso del sitio en el navegador</a>
    <a href="#mac">macOS</a>
    <a href="#android">Android y iPhone</a>
    <a href="#linux">Linux</a>
  </nav>

  <h2 id="win-diagnostico">Windows · Diagnóstico completo (solo lectura)</h2>
  <p>Muestra el estado del audio de Windows, de tus micrófonos y de los permisos. No cambia nada. Abrí la Terminal con <b>Win + X → Terminal</b>, pegá y apretá Enter.</p>
  <?php $cmd("Get-Service Audiosrv, AudioEndpointBuilder | Format-Table Name, Status; Get-PnpDevice -Class AudioEndpoint | Where-Object FriendlyName -match 'Micr' | Format-Table Status, FriendlyName; Get-ItemProperty '$hk', '$hk\\NonPackaged' -Name Value -ErrorAction SilentlyContinue | Format-Table PSChildName, Value", 'PowerShell · usuario', 'Lo normal: los dos servicios en <b>Running</b>, el micrófono en <b>OK</b> y los permisos en <b>Allow</b>. Lo que no esté así te indica la sección a seguir.'); ?>

  <h2 id="win-privacidad">Windows · Permiso de privacidad bloqueado</h2>
  <p><b>Síntoma:</b> el navegador dice que el sistema le negó el permiso ("Permission denied by system").</p>
  <?php $cmd("\$p = '$hk'; foreach (\$k in @(\$p, \"\$p\\NonPackaged\")) { if (-not (Test-Path \$k)) { New-Item -Path \$k | Out-Null }; Set-ItemProperty -Path \$k -Name Value -Value Allow }", 'PowerShell · usuario', 'Activa "Acceso al micrófono" y "Permitir que las aplicaciones de escritorio accedan al micrófono" para tu usuario.'); ?>
  <?php $cmd("Set-ItemProperty -Path '$hm' -Name Value -Value Allow", 'PowerShell · administrador', 'Activa el acceso al micrófono para todo el equipo (Win + X → Terminal (administrador)).'); ?>
  <?php $cmd('start ms-settings:privacy-microphone', 'Win + R o Terminal', 'Abre la pantalla de privacidad del micrófono para verificar los interruptores.'); ?>
  <p class="fine">Si el micrófono lo controla tu empresa o institución, estos ajustes no se pueden cambiar: pedíselo a sistemas.</p>

  <h2 id="win-audio">Windows · El micrófono está conectado pero no arranca</h2>
  <p><b>Síntoma:</b> "Could not start audio source" o "No se encontró ningún micrófono" aunque esté conectado. Es lo más común: el servicio de audio de Windows quedó trabado.</p>
  <?php $cmd('Restart-Service -Name AudioEndpointBuilder -Force; Start-Service -Name Audiosrv', 'PowerShell · administrador', 'Reinicia el servicio de audio de Windows. El sonido se corta unos segundos. Después cerrá el navegador por completo y volvé a abrirlo.'); ?>
  <?php $cmd('control mmsys.cpl,,1', 'Win + R o Terminal', 'Abre la pestaña <b>Grabar</b> de Sonido. Hacé doble clic en tu micrófono → <b>Opciones avanzadas</b> → desmarcá <b>"Permitir que las aplicaciones tomen control exclusivo de este dispositivo"</b> → Aceptar. Si la barra verde no se mueve al hablar, el problema es del micrófono o del driver.'); ?>

  <h2 id="win-ocupado">Windows · Otro programa está usando el micrófono</h2>
  <?php $cmd("Get-ChildItem '$hk\\NonPackaged' | Where-Object { (Get-ItemProperty \$_.PSPath).LastUsedTimeStop -eq 0 } | ForEach-Object { \$_.PSChildName -replace '#', '\\' }", 'PowerShell · usuario', 'Lista los programas que están usando el micrófono en este momento. Cerrá los que no sean tu navegador.'); ?>
  <?php $cmd('Get-Process Zoom, Teams, ms-teams, Discord, WhatsApp, Skype, obs64 -ErrorAction SilentlyContinue | Stop-Process', 'PowerShell · usuario', '<b>Cierra</b> Zoom, Teams, Discord, WhatsApp, Skype y OBS si están abiertos. Guardá antes lo que estés haciendo en esos programas.'); ?>

  <h2 id="win-deshabilitado">Windows · Micrófono deshabilitado</h2>
  <?php $cmd("Get-PnpDevice -Class AudioEndpoint | Where-Object FriendlyName -match 'Micr' | Format-Table Status, FriendlyName", 'PowerShell · usuario', 'Muestra tus micrófonos. Si alguno no dice <b>OK</b>, usá el comando siguiente.'); ?>
  <?php $cmd("Get-PnpDevice -Class AudioEndpoint | Where-Object { \$_.FriendlyName -match 'Micr' -and \$_.Status -ne 'OK' } | Enable-PnpDevice -Confirm:\$false", 'PowerShell · administrador', 'Vuelve a habilitar los micrófonos deshabilitados.'); ?>

  <h2 id="win-driver">Windows · Driver de audio (Realtek u otro)</h2>
  <?php $cmd("Get-PnpDevice -Class MEDIA -PresentOnly | Where-Object FriendlyName -match 'Audio' | ForEach-Object { pnputil /restart-device \$_.InstanceId }", 'PowerShell · administrador', 'Reinicia el driver de la placa de audio sin reiniciar la computadora (Windows 11).'); ?>
  <?php $cmd('pnputil /scan-devices', 'PowerShell · administrador', 'Vuelve a detectar el hardware. Si nada de esto funciona, actualizá el driver desde el sitio del fabricante de tu computadora o con <b>devmgmt.msc</b> → Entradas y salidas de audio.'); ?>

  <h2 id="navegador">Permiso del sitio en el navegador</h2>
  <p><b>Síntoma:</b> el navegador dice que el sitio tiene el micrófono bloqueado. Tocá el 🔒 a la izquierda de la dirección → <b>Micrófono → Permitir</b> y recargá. O pegá una de estas direcciones en una pestaña nueva (por seguridad, el navegador no deja abrirlas desde un enlace):</p>
  <?php $cmd('chrome://settings/content/siteDetails?site=' . rawurlencode(rtrim($site, '/')), 'Barra de direcciones de Chrome', 'Ajustes de este sitio en Chrome: poné <b>Micrófono</b> en <b>Permitir</b>.'); ?>
  <?php $cmd('edge://settings/content/siteDetails?site=' . rawurlencode(rtrim($site, '/')), 'Barra de direcciones de Edge', 'Lo mismo en Microsoft Edge.'); ?>
  <?php $cmd('chrome://settings/content/microphone', 'Barra de direcciones de Chrome', 'Elegí el micrófono correcto en la lista de arriba (por ejemplo "Micrófono (Realtek(R) Audio)") y revisá que el sitio no esté en "No pueden usar el micrófono".'); ?>

  <h2 id="mac">macOS</h2>
  <?php $cmd('tccutil reset Microphone com.google.Chrome', 'Terminal', 'Borra la decisión de permiso de Chrome. Al volver a abrir Chrome y tocar "Decilo vos", macOS pregunta de nuevo: elegí <b>Permitir</b>. Para Edge: <code>com.microsoft.edgemac</code>.'); ?>
  <?php $cmd('sudo killall coreaudiod', 'Terminal (pide tu contraseña)', 'Reinicia el audio de macOS si el micrófono no arranca. El sonido se corta un segundo.'); ?>
  <?php $cmd('open "x-apple.systempreferences:com.apple.preference.security?Privacy_Microphone"', 'Terminal', 'Abre Privacidad → Micrófono para verificar que Chrome esté activado.'); ?>

  <h2 id="android">Android y iPhone</h2>
  <p><b>Android:</b> Ajustes → Apps → Chrome → Permisos → Micrófono → <b>Permitir solo mientras se usa la app</b>. En Android 12 o más nuevo, revisá también que el acceso al micrófono esté activado en el panel de ajustes rápidos.</p>
  <p><b>iPhone y iPad:</b> Safari no tiene reconocimiento de voz para sitios web. Podés hacer los ejercicios de speaking con <b>"Lo dije en voz alta"</b>, o usar una computadora o un Android con Chrome.</p>

  <h2 id="linux">Linux</h2>
  <?php $cmd('pactl list short sources', 'Terminal', 'Lista los micrófonos que ve el sistema de audio.'); ?>
  <?php $cmd('systemctl --user restart pipewire pipewire-pulse wireplumber', 'Terminal', 'Reinicia el audio (PipeWire). En sistemas con PulseAudio: <code>pulseaudio -k</code>.'); ?>
</section>
