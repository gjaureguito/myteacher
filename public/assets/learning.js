(() => {
  'use strict';
  document.querySelectorAll('[data-reading-question]').forEach(question => {
    question.querySelector('[data-check-reading]').addEventListener('click', () => {
      const chosen = question.querySelector('input:checked');
      const result = question.querySelector('.reading-result');
      if (!chosen) {
        result.textContent = 'Elegí una opción antes de comprobar.';
        return;
      }
      result.textContent = chosen.dataset.correct === '1'
        ? 'Correcto. Ahora contrastá tu razonamiento con la explicación.'
        : 'Revisá la evidencia del texto. La explicación te ayuda a distinguir las opciones.';
      question.querySelector('.reading-explanation').open = true;
    });
  });
  document.querySelectorAll('.lab-writing').forEach(section => {
    const draft = section.querySelector('.writing-draft');
    const count = section.querySelector('.word-count');
    const button = section.querySelector('[data-copy-draft]');
    draft.addEventListener('input', () => {
      const words = draft.value.trim() ? draft.value.trim().split(/\s+/u).length : 0;
      count.textContent = `${words} ${words === 1 ? 'palabra' : 'palabras'}`;
      button.textContent = 'Copiar borrador';
    });
    button.addEventListener('click', async () => {
      if (!draft.value.trim()) { count.textContent = 'Escribí algo para copiar.'; return; }
      try {
        await navigator.clipboard.writeText(draft.value);
        count.textContent = 'Borrador copiado.';
      } catch {
        draft.focus();
        draft.select();
        count.textContent = 'Texto seleccionado. Usá la opción Copiar de tu dispositivo.';
      }
    });
  });
})();
