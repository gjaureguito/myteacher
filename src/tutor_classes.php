<?php
declare(strict_types=1);

/*
 * Clases de conversación guiadas por el profesor con IA.
 * El profesor conduce cada clase, la cierra al llegar al último intercambio y después
 * genera una devolución. Todo queda guardado en el historial del alumno (tabla tutor_classes).
 */

const TUTOR_LEVELS = ['A1' => 'A1 · Inicial', 'A2' => 'A2 · Básico', 'B1' => 'B1 · Intermedio', 'B2' => 'B2 · Intermedio alto', 'C1' => 'C1 · Avanzado', 'C2' => 'C2 · Maestría'];
const TUTOR_LEVEL_TURNS = ['A1' => 5, 'A2' => 6, 'B1' => 6, 'B2' => 7, 'C1' => 7, 'C2' => 8];

/** Plan de clases en orden. scenario y targets van al profesor en inglés; title y goal se muestran al alumno. */
const TUTOR_LESSONS = [
 'A1' => [
  ['a1-saludos', 'Saludar y presentarte', 'Presentarte, decir de dónde sos y preguntar lo mismo.', 'meeting a new classmate on the first day of an English course', ['Hello / Hi', 'My name is…', "I'm from…", 'Nice to meet you', 'verb to be: I am / you are / are you…?']],
  ['a1-familia', 'Hablar de tu familia', 'Presentar a tu familia y usar posesivos.', 'showing family photos to a friend', ['This is my…', 'I have a brother / a sister', 'He is… / She is…', 'possessives: my, his, her']],
  ['a1-cafe', 'Pedir en una cafetería', 'Pedir comida y bebida y preguntar el precio.', 'ordering at a café; the teacher plays the waiter', ["I'd like…, please", 'Can I have…?', 'How much is it?', 'numbers and prices']],
  ['a1-rutina', 'Tu rutina diaria', 'Contar qué hacés en un día normal.', 'talking about a normal weekday', ['present simple: I get up, I work, I study', 'at + time (at seven)', 'every day', 'always / usually / never']],
  ['a1-direcciones', 'Pedir indicaciones', 'Preguntar dónde queda un lugar y entender la respuesta.', 'a tourist asking for directions in town; the student is the tourist', ['Where is…?', 'Go straight', 'Turn left / right', 'next to / opposite']],
 ],
 'A2' => [
  ['a2-finde', 'Contar tu fin de semana', 'Hablar del pasado con verbos regulares e irregulares.', 'chatting on Monday about the weekend', ['past simple: I went, I watched, I ate', 'What did you do?', 'last weekend / yesterday', 'It was fun / boring']],
  ['a2-planes', 'Hacer planes con un amigo', 'Proponer, aceptar y organizar un plan.', 'planning to meet a friend this week', ['going to: I am going to…', 'Are you free on…?', "Let's…", 'How about…?']],
  ['a2-ropa', 'Comprar ropa', 'Pedir talles, comparar y decidir.', 'shopping for clothes; the teacher is the shop assistant', ['Can I try it on?', 'sizes: small, medium, large', 'comparatives: bigger, cheaper', "It's too… / not … enough"]],
  ['a2-hotel', 'Reservar un hotel', 'Hacer una reserva y preguntar qué incluye.', 'a phone call to book a hotel room; the teacher is the receptionist', ["I'd like to book…", 'for two nights', 'Does it include…?', 'dates: on the 5th of May']],
  ['a2-medico', 'En el médico', 'Explicar un malestar leve y entender consejos.', "a visit to the doctor for a minor cold; the teacher is the doctor (no real diagnosis)", ["I've got a headache / a cold", 'How long have you…?', 'You should…', 'body parts']],
 ],
 'B1' => [
  ['b1-experiencias', 'Experiencias de vida', 'Usar present perfect para hablar de experiencias.', 'two people sharing travel and life experiences', ['Have you ever…?', "I've been to…", 'ever / never / already', 'present perfect vs past simple']],
  ['b1-trabajo', 'Tu trabajo o tus estudios', 'Describir tareas, tiempo y lo que te gusta.', 'a networking event conversation about work or studies', ["I'm in charge of…", "I've been working… for / since…", 'What I like most is…', 'responsibilities and routines']],
  ['b1-reclamo', 'Hacer un reclamo', 'Explicar un problema y pedir una solución con cortesía.', 'returning a faulty product to a store; the teacher is the clerk', ["I'm afraid there's a problem with…", 'Could you…?', "I'd like a refund / a replacement", 'polite requests']],
  ['b1-opinion', 'Dar tu opinión', 'Opinar, acordar, discrepar y justificar.', 'discussing whether it is better to live in a city or in the countryside', ['In my opinion…', 'I agree / disagree because…', 'On the other hand…', 'first conditional: If…, I will…']],
  ['b1-anecdota', 'Contar una anécdota', 'Narrar una historia con past continuous y conectores.', 'telling a funny or surprising story that happened to you', ['I was… when…', 'First / then / finally', 'Suddenly…', 'Really? What happened next?']],
 ],
 'B2' => [
  ['b2-entrevista', 'Entrevista de trabajo', 'Hablar de fortalezas y logros con seguridad.', 'a job interview; the teacher is the interviewer', ['One of my strengths is…', "I'd describe myself as…", 'In my previous role I…', 'I managed to…']],
  ['b2-hipotesis', 'Situaciones hipotéticas', 'Usar condicionales segundo y tercero.', 'imagining different life choices', ['If I were…, I would…', 'If I had known…, I would have…', 'I wish I…', 'What would you do if…?']],
  ['b2-negociar', 'Negociar condiciones', 'Proponer, ceder y llegar a un acuerdo.', 'negotiating price and delivery terms with a supplier; the teacher is the supplier', ['Would you be willing to…?', 'We could consider…', 'provided that…', 'meet halfway']],
  ['b2-noticias', 'Comentar una noticia', 'Usar estilo indirecto y voz pasiva.', 'discussing a recent technology news story', ['They said that…', 'According to…', 'It has been announced that…', 'passive voice']],
  ['b2-debate', 'Debatir un tema', 'Defender una postura y responder objeciones.', 'debating whether remote work is better than office work', ['I see your point, but…', "There's no doubt that…", 'It could be argued that…', 'whereas / however']],
 ],
 'C1' => [
  ['c1-reunion', 'Liderar una reunión', 'Conducir, pedir precisiones y cerrar acuerdos.', 'leading a project meeting with a colleague', ["Let's get down to business", 'Could you elaborate on…?', 'To sum up…', 'action points']],
  ['c1-tacto', 'Discrepar con tacto', 'Dar feedback difícil con matices.', 'giving tactful feedback to a colleague about a report', ["I'm not entirely convinced that…", 'With all due respect…', 'softening language', 'Not only… but also…']],
  ['c1-datos', 'Presentar datos', 'Describir tendencias y explicar causas.', "presenting a company's quarterly results", ['As the chart shows…', 'a sharp rise / a slight decline', 'This can be attributed to…', "It's worth noting that…"]],
  ['c1-idioms', 'Expresiones idiomáticas', 'Usar expresiones naturales en una charla informal.', 'chatting with a colleague after work about a busy week', ['a piece of cake', 'on the same page', 'cut corners', 'the ball is in your court']],
  ['c1-argumentar', 'Argumentar a favor y en contra', 'Presentar posturas y concluir con equilibrio.', 'discussing the use of AI in education', ['Proponents argue that…', 'Critics contend that…', 'That said…', 'On balance…']],
 ],
 'C2' => [
  ['c2-registro', 'Ironía, tono y registro', 'Detectar y usar ironía, atenuación y cambios de registro.', 'discussing a harsh film review over coffee', ['tongue-in-cheek', 'understatement', 'formal vs informal register', 'not exactly…']],
  ['c2-persuadir', 'Persuadir a una audiencia', 'Usar recursos retóricos para convencer.', 'pitching a project to an investor; the teacher is the investor', ['What if I told you…?', 'What really matters is…', 'rhetorical questions', 'emphatic structures']],
  ['c2-academico', 'Debate académico', 'Discutir evidencia con precisión y cautela.', 'debating a research claim with a fellow researcher', ['The evidence suggests…', 'This raises the question of…', 'a causal link', 'nuanced hedging']],
  ['c2-diplomacia', 'Lenguaje diplomático', 'Resolver un conflicto preservando la relación.', 'resolving a disagreement between business partners', ['I appreciate your concerns…', 'Perhaps we could explore…', 'It would be in both our interests…', 'face-saving language']],
  ['c2-explicar', 'Explicar ideas complejas', 'Reformular, usar analogías y simplificar.', 'explaining a complex topic of your choice to someone without background', ['In other words…', "It's a bit like…", 'To put it simply…', 'paraphrasing']],
 ],
];

function tutor_lesson(string $key): ?array {
    foreach (TUTOR_LESSONS as $level => $list) foreach ($list as $i => $l) {
        if ($l[0] === $key) return ['key' => $l[0], 'title' => $l[1], 'goal' => $l[2], 'scenario' => $l[3], 'targets' => $l[4],
            'level' => $level, 'number' => $i + 1, 'turns' => TUTOR_LEVEL_TURNS[$level]];
    }
    return null;
}

function tutor_classes_schema(): void {
    static $done = false;
    if ($done) return;
    $sqlite = db_is_sqlite();
    db()->exec($sqlite
        ? 'CREATE TABLE IF NOT EXISTS tutor_classes (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, lesson_key TEXT NOT NULL, level TEXT NOT NULL, status TEXT NOT NULL, turns_done INTEGER NOT NULL DEFAULT 0, transcript TEXT NOT NULL, feedback TEXT NULL, score INTEGER NULL, started_at TEXT NOT NULL, finished_at TEXT NULL)'
        : 'CREATE TABLE IF NOT EXISTS tutor_classes (id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NOT NULL, lesson_key VARCHAR(40) NOT NULL, level VARCHAR(2) NOT NULL, status VARCHAR(12) NOT NULL, turns_done INT NOT NULL DEFAULT 0, transcript MEDIUMTEXT NOT NULL, feedback MEDIUMTEXT NULL, score INT NULL, started_at DATETIME NOT NULL, finished_at DATETIME NULL, KEY tutor_classes_user (user_id, status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    db()->exec($sqlite
        ? 'CREATE TABLE IF NOT EXISTS tutor_dialog_usage (user_id INTEGER NOT NULL, usage_day TEXT NOT NULL, used INTEGER NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))'
        : 'CREATE TABLE IF NOT EXISTS tutor_dialog_usage (user_id BIGINT NOT NULL, usage_day DATE NOT NULL, used INT NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))');
    $done = true;
}

/** Progreso del alumno: mejor puntaje y cantidad de veces completada cada clase. */
function tutor_class_progress(int $uid): array {
    tutor_classes_schema();
    $out = [];
    foreach (q_all("SELECT lesson_key, MAX(score) AS best, COUNT(*) AS times FROM tutor_classes WHERE user_id=? AND status='completed' GROUP BY lesson_key", [$uid]) as $r) {
        $out[$r['lesson_key']] = ['best' => $r['best'] === null ? null : (int) $r['best'], 'times' => (int) $r['times']];
    }
    return $out;
}

/** Siguiente clase recomendada: la primera sin completar, empezando por el nivel indicado. */
function tutor_next_lesson(array $progress, string $level): ?string {
    $levels = array_keys(TUTOR_LEVELS);
    foreach (array_slice($levels, (int) array_search($level, $levels, true)) as $lv) {
        foreach (TUTOR_LESSONS[$lv] as $l) if (!isset($progress[$l[0]])) return $l[0];
    }
    return null;
}

/** Instrucciones del profesor para un intercambio de la clase. $exchange: 0 = apertura, 1..turns = respuestas del alumno. */
function tutor_class_system(array $lesson, int $exchange): string {
    $n = $lesson['turns'];
    $short = in_array($lesson['level'], ['A1', 'A2'], true);
    $s = "You are MyTeacher, an English teacher giving a structured, progressive one-to-one conversation class to a Spanish speaker from Argentina. "
        . "Student CEFR level: {$lesson['level']}. Class {$lesson['number']}: \"{$lesson['title']}\". Situation: {$lesson['scenario']}. "
        . "Target language to practise: " . implode('; ', $lesson['targets']) . ". "
        . "YOU ALWAYS LEAD THE CLASS: you ask the questions, you introduce and model each target expression, and you guide the student step by step through the targets in order. "
        . "This is not a free chat: if the student goes off-topic or asks for something else, answer in one short sentence and steer back to the lesson. "
        . "The class has exactly $n student answers. "
        . "Each turn: react briefly to what the student said; if they made a mistake, model the correct form naturally (e.g. 'Oh, you went to the cinema!'); then ask ONE question or give ONE prompt that makes the student use the next target expression. "
        . 'Use English adapted to the level' . ($short ? ' (very short, simple sentences, max 35 words)' : ' (max 60 words)') . '. ';
    if ($exchange === 0) {
        $s .= "NOW: open the class. Greet the student, say in one simple sentence what you will practise today, set the situation and ask the first question. ";
    } elseif ($exchange >= $n) {
        $s .= "NOW: this is the student's LAST answer. React to it, then CLOSE THE CLASS: thank them, say in one or two sentences which expressions they practised, and say goodbye. Do NOT ask any new question. ";
    } else {
        $s .= "NOW: the student has given answer $exchange of $n. Continue the lesson with the next target. ";
    }
    return $s . "OUTPUT FORMAT (plain text, no markdown): first your reply in English; then a new line starting with [[ES]] followed by its Spanish translation (Rioplatense, voseo); "
        . "then a new line starting with [[FIX]] followed by ONE short correction in Rioplatense Spanish with the corrected English sentence, only if the student's last answer has a grammar or vocabulary mistake; otherwise nothing after [[FIX]].";
}

/** Convierte el registro guardado en mensajes para OpenAI. */
function tutor_class_input(array $transcript): array {
    $input = [];
    foreach ($transcript as $m) $input[] = ['role' => $m['role'] === 'teacher' ? 'assistant' : 'user', 'content' => mb_substr((string) $m['text'], 0, 2000)];
    if (!$input || $input[0]['role'] !== 'user') array_unshift($input, ['role' => 'user', 'content' => '(The student joins the class.)']);
    return $input;
}

/** Devolución final de la clase, en español. */
function tutor_class_feedback(array $lesson, array $transcript): ?array {
    $dialog = '';
    foreach ($transcript as $m) $dialog .= ($m['role'] === 'teacher' ? 'Teacher' : 'Student') . ': ' . $m['text'] . "\n";
    $schema = [
        'type' => 'object', 'additionalProperties' => false,
        'required' => ['score', 'resumen', 'aprendiste', 'fortalezas', 'mejorar', 'correcciones', 'proximo_paso'],
        'properties' => [
            'score' => ['type' => 'integer'],
            'resumen' => ['type' => 'string'],
            'aprendiste' => ['type' => 'array', 'items' => ['type' => 'string']],
            'fortalezas' => ['type' => 'array', 'items' => ['type' => 'string']],
            'mejorar' => ['type' => 'array', 'items' => ['type' => 'string']],
            'correcciones' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['dijiste', 'mejor', 'explicacion'],
                'properties' => ['dijiste' => ['type' => 'string'], 'mejor' => ['type' => 'string'], 'explicacion' => ['type' => 'string']]]],
            'proximo_paso' => ['type' => 'string'],
        ],
    ];
    $r = openai_respond([
        'instructions' => "You are an English teacher evaluating a short conversation class of a Spanish-speaking student (CEFR {$lesson['level']}). "
            . "Lesson: \"{$lesson['title']}\". Goal: {$lesson['goal']} Target language: " . implode('; ', $lesson['targets']) . ". "
            . 'Evaluate ONLY the Student lines. Write everything in Rioplatense Spanish (voseo), warm and encouraging but honest. Keep English expressions in English. '
            . 'Return JSON with: score (0-100, how well the student used the target language for their level); resumen (2-3 sentences); '
            . 'aprendiste (3-5 items: the English expressions or structures practised, each with a short Spanish note); fortalezas (1-3 items); mejorar (1-3 items); '
            . 'correcciones (up to 5 real mistakes the student made, with dijiste = exact student words, mejor = corrected English, explicacion = short reason; empty if none); '
            . 'proximo_paso (one concrete suggestion for next class). Return only the JSON.',
        'input' => [['role' => 'user', 'content' => "Class transcript:\n" . $dialog]],
        'max_output_tokens' => 1200,
        'text' => ['format' => ['type' => 'json_schema', 'name' => 'feedback', 'strict' => true, 'schema' => $schema]],
    ]);
    if (!$r['ok']) return null;
    $json = $r['text'];
    if (preg_match('/\{.*\}/s', $json, $m)) $json = $m[0];
    $fb = json_decode($json, true);
    if (!is_array($fb)) return null;
    $fb['score'] = max(0, min(100, (int) ($fb['score'] ?? 0)));
    foreach (['aprendiste', 'fortalezas', 'mejorar'] as $k) $fb[$k] = array_values(array_filter(array_map(static fn($x) => is_scalar($x) ? (string) $x : '', (array) ($fb[$k] ?? [])), static fn($x) => trim($x) !== ''));
    $fb['correcciones'] = array_values(array_filter((array) ($fb['correcciones'] ?? []), 'is_array'));
    foreach (['resumen', 'proximo_paso'] as $k) $fb[$k] = trim((string) ($fb[$k] ?? ''));
    return $fb;
}
