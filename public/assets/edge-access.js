(() => {
  'use strict';
  const supported = /Windows/i.test(navigator.userAgent) && !/Edg\//i.test(navigator.userAgent);
  function edgeLink() {
    const link = document.createElement('a');
    link.className = 'btn btn-ghost btn-sm edge-open';
    link.href = 'microsoft-edge:' + location.href;
    link.textContent = 'Abrir en Microsoft Edge';
    link.title = 'Abre esta misma página en Edge. Puede pedirte iniciar sesión nuevamente.';
    return link;
  }
  window.ipEdgeHelpHTML = () => {
    if (!supported) return '';
    const help = document.createElement('span');
    help.className = 'edge-browser-help';
    const message = document.createElement('span');
    message.textContent = '¿No funciona el micrófono en Chrome? Probá esta misma página en Edge.';
    const note = document.createElement('small');
    note.className = 'fine';
    note.textContent = 'Necesitás Edge instalado en Windows. Si el navegador pregunta, permití abrir Microsoft Edge. Puede pedirte iniciar sesión otra vez; el progreso guardado sigue en tu cuenta. Las respuestas todavía sin comprobar no se transfieren.';
    help.append(message, edgeLink(), note);
    return help.outerHTML;
  };
  document.addEventListener('click', event => {
    const link = event.target.closest('a.edge-open');
    if (link) link.href = 'microsoft-edge:' + location.href;
  });
  document.querySelectorAll('[data-edge-shortcut]').forEach(slot => {
    if (supported) slot.append(edgeLink());
  });
  document.querySelectorAll('[data-edge-help]').forEach(slot => {
    slot.innerHTML = window.ipEdgeHelpHTML();
  });
})();
