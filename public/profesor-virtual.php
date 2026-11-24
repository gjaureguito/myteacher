<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$u = require_login();
if (!has_full_access($u)) { http_response_code(403); exit('Requiere acceso completo a MyTeacher.'); }
$_SESSION['voice_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['voice_csrf'];
$enabled = env('VOICE_TEACHER_ENABLED', '0') === '1';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profesor virtual | MyTeacher</title>
<style>
:root{font-family:system-ui,-apple-system,sans-serif;color:#172238;background:#f4f7fc}
*{box-sizing:border-box}main{max-width:780px;margin:7vh auto;padding:24px}
section{background:white;border-radius:22px;padding:30px;box-shadow:0 10px 40px #11224415}
h1{margin:0 0 10px}p{line-height:1.6;color:#536077}
.controls{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin:22px 0}
button,select{padding:13px 18px;border-radius:11px;font-size:1rem}
button{border:0;background:#174dd4;color:white;cursor:pointer}
button:disabled{opacity:.5;cursor:not-allowed}
button.secondary{background:#e7ecf7;color:#18356d}
select{border:1px solid #bbc9da;background:white}
#status{font-weight:600;color:#19448b;min-height:26px}
small{color:#627087}a{color:#174dd4}
</style>
</head>
<body><main><section>
<p><a href="/dashboard.php">← Volver al panel</a></p>
<h1>🎙️ Profesor virtual de inglés</h1>
<p>Conversá con tu profesor sin mantener presionado ningún botón. Elegí un nivel, activá el micrófono y hablá en inglés. Para finalizar, cortá la llamada.</p>
<div class="controls">
<label for="level">Nivel</label>
<select id="level"><option>A1</option><option>A2</option><option>B1</option><option>B2</option><option>C1</option></select>
<button id="start" <?= $enabled ? '' : 'disabled' ?>>Iniciar conversación</button>
<button id="stop" class="secondary" disabled>Finalizar</button>
</div>
<p id="status" role="status" aria-live="polite"><?= $enabled ? 'Listo para conversar' : 'Función en preparación: falta habilitarla en Railway' ?></p>
<small>El navegador solicitará permiso para el micrófono. El audio se envía a OpenAI durante la sesión. Al finalizar se corta la conexión y el micrófono. Recomendamos sesiones breves para controlar gastos.</small>
<audio id="remote" autoplay playsinline></audio>
</section></main>
<script>
const csrf = <?= json_encode($csrf, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const start = document.getElementById('start'), stop = document.getElementById('stop');
const status = document.getElementById('status'), remote = document.getElementById('remote');
let pc = null, mic = null, channel = null, deadline = null;
function message(s){ status.textContent=s; }
function finish(){
  clearTimeout(deadline);deadline=null;
  if(channel){try{channel.close()}catch(_){} channel=null}
  if(pc){try{pc.close()}catch(_){} pc=null}
  if(mic){mic.getTracks().forEach(t=>t.stop());mic=null}
  remote.srcObject=null; stop.disabled=true; start.disabled=false;
  message('Conversación finalizada. Micrófono desconectado.');
}
stop.addEventListener('click',finish);
start.addEventListener('click',async()=>{
  start.disabled=true;stop.disabled=false;message('Conectando micrófono…');
  try{
    mic=await navigator.mediaDevices.getUserMedia({audio:{echoCancellation:true,noiseSuppression:true}});
    pc=new RTCPeerConnection();
    pc.addTrack(mic.getAudioTracks()[0],mic);
    pc.ontrack=e=>{remote.srcObject=e.streams[0];remote.play().catch(()=>{});};
    pc.onconnectionstatechange=()=>{if(pc && ['disconnected','failed','closed'].includes(pc.connectionState))finish();};
    channel=pc.createDataChannel('oai-events');
    channel.onopen=()=>message('Conectado. Podés comenzar a hablar en inglés.');
    channel.onmessage=e=>{try{const data=JSON.parse(e.data);if(data.type==='error')message('Error de sesión: '+(data.error?.message||'Intentá nuevamente'));}catch(_){}};
    const offer=await pc.createOffer();await pc.setLocalDescription(offer);
    const response=await fetch('/api/voice-session.php?level='+encodeURIComponent(document.getElementById('level').value),{
      method:'POST',headers:{'Content-Type':'application/sdp','X-CSRF-Token':csrf},body:offer.sdp
    });
    if(!response.ok)throw new Error(await response.text());
    const answer=await response.text();
    if(!pc) return;
    await pc.setRemoteDescription({type:'answer',sdp:answer});
    deadline=setTimeout(()=>{if(pc)finish()},10*60*1000);
  }catch(e){finish();message('No se pudo conectar: '+e.message);}
});
window.addEventListener('pagehide',finish);
</script></body></html>
