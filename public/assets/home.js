// Actividad de práctica de la portada: tres preguntas de muestra con explicación.
(() => {
  'use strict';
  const box = document.querySelector('[data-mh-practice]');
  if (!box) return;

  const questions = [
    { level: 'A1', before: 'My brother ', after: ' to work by bus.', options: ['go', 'goes', 'going'], ok: 1,
      why: 'Con he / she / it, el presente simple lleva -s: my brother goes.' },
    { level: 'A2', before: 'Yesterday I ', after: ' a great film.', options: ['see', 'saw', 'have seen'], ok: 1,
      why: '«Yesterday» marca un momento terminado del pasado, así que va pasado simple: saw.' },
    { level: 'B1', before: 'If it rains tomorrow, we ', after: ' at home.', options: ['stay', 'would stay', 'will stay'], ok: 2,
      why: 'Primer condicional: if + presente, will + verbo. Es una posibilidad real del futuro.' },
  ];

  const level = box.querySelector('[data-mh-level]');
  const sentence = box.querySelector('[data-mh-sentence]');
  const options = box.querySelector('[data-mh-options]');
  const feedback = box.querySelector('[data-mh-feedback]');
  const next = box.querySelector('[data-mh-next]');
  const dots = [...box.querySelectorAll('.mh-dots i')];
  let index = 0;

  const say = text => {
    try {
      if (!('speechSynthesis' in window)) return;
      if (localStorage.getItem('ip_sound') === '0') return;
      const u = new SpeechSynthesisUtterance(text);
      u.lang = 'en-US';
      speechSynthesis.cancel();
      speechSynthesis.speak(u);
    } catch (e) { /* el audio es opcional */ }
  };

  const setFeedback = (kind, title, text) => {
    feedback.className = 'mh-feedback' + (kind ? ' is-' + kind : '');
    feedback.replaceChildren();
    const strong = document.createElement('strong');
    strong.textContent = title;
    feedback.append(strong, ' ' + text);
  };

  const render = () => {
    const q = questions[index];
    level.textContent = 'Práctica · ' + q.level;
    const blank = document.createElement('span');
    blank.className = 'mh-blank';
    blank.textContent = '___';
    sentence.replaceChildren(q.before, blank, q.after);
    options.replaceChildren(...q.options.map((text, i) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'mh-opt';
      b.textContent = text;
      b.addEventListener('click', () => answer(i, b, blank));
      return b;
    }));
    dots.forEach((d, i) => d.classList.toggle('on', i <= index));
    setFeedback('', 'Elegí una respuesta.', 'Así se practica en MyTeacher: respondés y ves la explicación.');
    next.hidden = true;
  };

  const answer = (i, button, blank) => {
    const q = questions[index];
    if (i === q.ok) {
      options.querySelectorAll('.mh-opt').forEach(o => { o.disabled = true; });
      button.classList.add('is-ok');
      blank.textContent = q.options[i];
      blank.classList.add('is-ok');
      setFeedback('ok', '¡Correcto!', q.why);
      say(q.before + q.options[i] + q.after);
      next.textContent = index < questions.length - 1 ? 'Siguiente pregunta →' : 'Volver a empezar ↺';
      next.hidden = false;
    } else {
      button.classList.add('is-bad');
      button.disabled = true;
      setFeedback('bad', 'Casi. Probá otra vez.', '«' + q.options[i] + '» no concuerda con esta oración. Fijate en el sujeto y el tiempo verbal.');
    }
  };

  next.addEventListener('click', () => {
    index = (index + 1) % questions.length;
    render();
    options.querySelector('.mh-opt')?.focus();
  });

  render();
})();
