(() => {
  'use strict';

  document.querySelectorAll('.scene[data-scene]').forEach(scene => {
    const host = scene.querySelector('.scene-practice');
    const model = scene.dataset.modelEn || '';
    if (!host || !model) return;

    const parts = model.match(/\\s+|[^\\s]+/gu) || [];
    const indexes = [];
    parts.forEach((part, i) => {
      if (/^[\\p{L}][\\p{L}’'\\-]*[.,!?;:]?$/u.test(part) && part.replace(/[.,!?;:]$/u, '').length >= 3) indexes.push(i);
    });
    if (indexes.length < 4) return;

    const count = Math.min(6, Math.max(3, Math.floor(indexes.length / 12)));
    const selected = new Set();
    for (let n = 1; n <= count; n++) {
      selected.add(indexes[Math.round(n * (indexes.length - 1) / (count + 1))]);
    }

    const answers = [];
    const lab = document.createElement('section');
    lab.className = 'scene-writing-lab';
    const heading = document.createElement('h3');
    heading.textContent = '🔤 Completá las palabras del párrafo';
    const instruction = document.createElement('p');
    instruction.textContent = 'El texto ya está escrito. Leé el contexto y completá las palabras que faltan usando el banco.';
    const passage = document.createElement('p');
    passage.className = 'scene-cloze-passage';
    passage.lang = 'en';
    const bankLabel = document.createElement('strong');
    bankLabel.textContent = 'Banco de palabras: ';
    const bank = document.createElement('span');
    bank.className = 'scene-cloze-bank';
    const inputs = [];

    parts.forEach((part, i) => {
      if (!selected.has(i)) {
        passage.append(document.createTextNode(part));
        return;
      }
      const answer = part.replace(/^[.,!?;:]|[.,!?;:]$/gu, '');
      const prefix = part.startsWith('.') || part.startsWith(',') ? part[0] : '';
      const suffix = /[.,!?;:]$/u.test(part) ? part.slice(-1) : '';
      if (prefix) passage.append(document.createTextNode(prefix));
      const input = document.createElement('input');
      input.type = 'text';
      input.className = 'scene-cloze-gap';
      input.autocomplete = 'off';
      input.autocapitalize = 'off';
      input.spellcheck = false;
      input.setAttribute('aria-label', 'Palabra faltante ' + (inputs.length + 1));
      input.dataset.answerIndex = String(inputs.length);
      input.size = Math.max(5, Math.min(11, answer.length + 1));
      inputs.push(input);
      answers.push(answer);
      passage.append(input);
      if (suffix) passage.append(document.createTextNode(suffix));
      if (/\\s+$/u.test(parts[i + 1] || '')) passage.append(document.createTextNode(' '));
    });

    const bankWords = [...answers].sort(() => Math.random() - 0.5);
    bank.append(document.createTextNode(bankWords.join(' · ')));
    const actions = document.createElement('div');
    actions.className = 'sw-actions';
    const check = document.createElement('button');
    check.type = 'button';
    check.textContent = 'Comprobar';
    const show = document.createElement('button');
    show.type = 'button';
    show.textContent = 'Ver solución';
    const reset = document.createElement('button');
    reset.type = 'button';
    reset.textContent = 'Borrar respuestas';
    const feedback = document.createElement('p');
    feedback.className = 'sw-feedback';
    feedback.setAttribute('role', 'status');
    feedback.setAttribute('aria-live', 'polite');
    actions.append(check, show, reset);
    lab.append(heading, instruction, passage, bankLabel, bank, actions, feedback);
    host.appendChild(lab);

    const normalize = value => value.toLocaleLowerCase().replace(/[’']/gu, "'").replace(/[^\\p{L}\\p{N}'-]/gu, '').trim();
    check.addEventListener('click', () => {
      if (inputs.some(input => !input.value.trim())) {
        feedback.textContent = 'Completá todos los espacios antes de comprobar.';
        return;
      }
      let right = 0;
      inputs.forEach((input, i) => {
        const ok = normalize(input.value) === normalize(answers[i]);
        input.classList.toggle('is-ok', ok);
        input.classList.toggle('is-bad', !ok);
        if (ok) right++;
      });
      feedback.textContent = right === answers.length
        ? '¡Muy bien! Completaste todas las palabras.'
        : 'Acertaste ' + right + ' de ' + answers.length + ' palabras. Corregí los espacios marcados en rojo.';
    });
    show.addEventListener('click', () => {
      inputs.forEach((input, i) => { input.value = answers[i]; input.classList.remove('is-bad'); input.classList.add('is-ok'); });
      feedback.textContent = 'Solución completada.';
    });
    reset.addEventListener('click', () => {
      inputs.forEach(input => { input.value = ''; input.classList.remove('is-ok', 'is-bad'); });
      feedback.textContent = '';
    });

    if (!document.getElementById('scene-writing-styles')) {
      const style = document.createElement('style');
      style.id = 'scene-writing-styles';
      style.textContent = '.scene-writing-lab{border:1px solid #a7b9d1;border-radius:16px;padding:18px;margin-top:24px;background:var(--surface,#f7f9fc)}.scene-writing-lab h3{margin-top:0}.scene-cloze-passage{font-size:1.08rem;line-height:2.3}.scene-cloze-gap{width:auto;max-width:11ch;border:1px solid #839bb8;border-radius:6px;padding:5px 7px;background:var(--surface,#fff);color:inherit;font:inherit;text-align:center}.scene-cloze-gap.is-ok{border-color:#16834a;background:#e8f6ee}.scene-cloze-gap.is-bad{border-color:#c43d3d;background:#fff0f0}.scene-cloze-bank{display:block;margin-top:6px;font-weight:600}.sw-actions{display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin:12px 0}.sw-actions button{border:1px solid #7089a7;border-radius:9px;padding:10px;background:transparent;color:inherit;cursor:pointer}.sw-feedback{font-weight:600;line-height:1.6}';
      document.head.appendChild(style);
    }
  });
})();