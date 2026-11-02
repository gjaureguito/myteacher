<?php
declare(strict_types=1);

/* ---------- Modelos de OpenAI disponibles para el proyecto de la clave ---------- */
// Lista de modelos que la clave puede usar (GET /v1/models), guardada 6 horas.
function openai_models(bool $refresh = false): array {
    $cache = (is_writable(ROOT . '/storage') ? ROOT . '/storage' : sys_get_temp_dir()) . '/openai-models.json';
    if (!$refresh && is_file($cache) && filemtime($cache) > time() - 21600) {
        $ids = json_decode((string) file_get_contents($cache), true);
        if (is_array($ids) && $ids) return $ids;
    }
    $r = http_json('GET', rtrim((string) env('OPENAI_BASE_URL', 'https://api.openai.com'), '/') . '/v1/models', ['Authorization: Bearer ' . env('OPENAI_API_KEY')]);
    $ids = array_values(array_filter(array_map(static fn($m) => (string) ($m['id'] ?? ''), $r['data']['data'] ?? [])));
    if ($r['code'] !== 200) error_log('OpenAI /v1/models HTTP ' . $r['code'] . ' ' . substr((string) $r['raw'], 0, 300));
    if ($ids) @file_put_contents($cache, json_encode($ids));
    return $ids;
}
// Elige un modelo habilitado: primero el configurado, después el mejor candidato de la lista.
function openai_pick_model(string $kind, string $preferred, bool $refresh = false, array $exclude = []): ?string {
    $ids = array_values(array_diff(openai_models($refresh), $exclude));
    if (!$ids) return null;
    if (in_array($preferred, $ids, true)) return $preferred;
    if ($kind === 'stt') {
        foreach (['gpt-transcribe', 'gpt-4o-mini-transcribe', 'gpt-4o-transcribe', 'whisper-1'] as $m) if (in_array($m, $ids, true)) return $m;
        foreach ($ids as $m) if (str_contains($m, 'transcribe') && !preg_match('/diarize|live|realtime/', $m)) return $m;
        return null;
    }
    // Texto: descartar modelos de audio, imagen, embeddings, etc.
    $chat = array_values(array_filter($ids, static fn($m) => preg_match('/^(gpt|o\d|chatgpt)/', $m)
        && !preg_match('/audio|realtime|transcribe|tts|image|embedding|moderation|search|instruct|codex|-\d{4}-\d{2}-\d{2}$/', $m)));
    if (!$chat) return null;
    // Preferir los livianos (baratos y rápidos) para conversación.
    $score = static fn(string $m): int => preg_match('/mini|luna|lite/', $m) ? 0 : (preg_match('/nano/', $m) ? 1 : 2);
    usort($chat, static fn(string $a, string $b): int => ($score($a) <=> $score($b)) ?: strnatcmp($b, $a));
    return $chat[0];
}
// Recuerda qué modelo funcionó (y con qué opciones) para no repetir intentos fallidos.
function openai_working(string $kind, ?array $set = null): ?array {
    $file = (is_writable(ROOT . '/storage') ? ROOT . '/storage' : sys_get_temp_dir()) . '/openai-working.json';
    $all = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    if ($set === null) return is_array($all[$kind] ?? null) ? $all[$kind] : null;
    if (($all[$kind] ?? null) !== $set) { $all[$kind] = $set; @file_put_contents($file, json_encode($all)); }
    return $set;
}

function openai_url(string $path): string {
    return rtrim((string) env('OPENAI_BASE_URL', 'https://api.openai.com'), '/') . $path;
}

/**
 * Llama a /v1/responses con el modelo habilitado del proyecto.
 * Con $onDelta usa streaming y le pasa cada fragmento de texto a medida que llega.
 * Se adapta solo si el modelo no está habilitado, no acepta "reasoning" o no acepta salida JSON.
 * Devuelve ['ok','text','status','code','model','usage','partial'].
 */
function openai_respond(array $body, ?callable $onDelta = null): array {
    $pref = (string) env('OPENAI_MODEL', TUTOR_MODEL);
    if (!preg_match('/^[a-zA-Z0-9._:-]+$/', $pref)) $pref = TUTOR_MODEL;
    $known = openai_working('chat');
    if ($known && ($known['pref'] ?? '') !== $pref) $known = null;
    $body['model'] = $known ? (string) $known['model'] : (openai_pick_model('chat', $pref) ?? $pref);
    $body += ['store' => false, 'reasoning' => ['effort' => 'none']];
    if ($known && $known['model'] === $body['model'] && empty($known['reasoning'])) unset($body['reasoning']);
    if ($onDelta) $body['stream'] = true;

    $tried = [];
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $tried[] = $body['model'];
        $buffer = ''; $full = ''; $errorBody = ''; $got = false; $usage = null; $streamError = '';
        $ch = curl_init(openai_url('/v1/responses'));
        $opts = [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . env('OPENAI_API_KEY')],
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120,
        ];
        if ($onDelta) {
            $opts[CURLOPT_WRITEFUNCTION] = function ($ch, string $chunk) use (&$buffer, &$full, &$errorBody, &$got, &$usage, &$streamError, $onDelta): int {
                if ((int) curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) { $errorBody .= $chunk; return strlen($chunk); }
                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $block = substr($buffer, 0, $pos); $buffer = substr($buffer, $pos + 2);
                    foreach (explode("\n", $block) as $line) {
                        if (!str_starts_with($line, 'data:')) continue;
                        $ev = json_decode(trim(substr($line, 5)), true);
                        if (!is_array($ev)) continue;
                        $type = $ev['type'] ?? '';
                        if ($type === 'response.output_text.delta' && isset($ev['delta'])) { $full .= $ev['delta']; $got = true; $onDelta($ev['delta']); }
                        elseif ($type === 'response.completed') $usage = $ev['response']['usage'] ?? null;
                        elseif ($type === 'error' || $type === 'response.failed') $streamError = (string) ($ev['message'] ?? $ev['response']['error']['message'] ?? 'error');
                    }
                }
                return strlen($chunk);
            };
        } else {
            $opts[CURLOPT_RETURNTRANSFER] = true;
        }
        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$onDelta) {
            $data = is_string($raw) ? (json_decode($raw, true) ?: []) : [];
            if ($status === 200) {
                foreach ($data['output'] ?? [] as $item) foreach ($item['content'] ?? [] as $part) if (($part['type'] ?? '') === 'output_text') $full .= $part['text'];
                $usage = $data['usage'] ?? null; $got = $full !== '';
            } else $errorBody = (string) $raw;
        }
        if ($got) {
            openai_working('chat', ['pref' => $pref, 'model' => $body['model'], 'reasoning' => isset($body['reasoning'])]);
            if (is_array($usage)) error_log('OpenAI ' . $body['model'] . ' tokens in=' . ($usage['input_tokens'] ?? '?') . ' out=' . ($usage['output_tokens'] ?? '?'));
            return ['ok' => true, 'text' => $full, 'status' => 200, 'code' => '', 'model' => $body['model'], 'usage' => $usage, 'partial' => $streamError !== ''];
        }
        $err = json_decode($errorBody, true)['error'] ?? [];
        $msg = strtolower((string) ($err['message'] ?? $streamError));
        $param = (string) ($err['param'] ?? '');
        $code = (string) ($err['code'] ?? '');
        if ($code === 'model_not_found' || str_contains($msg, 'does not have access to model') || str_contains($msg, 'does not exist')) {
            $other = openai_pick_model('chat', '__ninguno__', $attempt === 0, $tried);
            if (!$other) break;
            error_log('OpenAI: ' . $body['model'] . ' no habilitado, uso ' . $other);
            $body['model'] = $other;
            $body['reasoning'] = ['effort' => 'none'];
        } elseif ($status === 400 && isset($body['reasoning']) && (str_contains($msg, 'reasoning') || str_starts_with($param, 'reasoning'))) {
            unset($body['reasoning']);
        } elseif ($status === 400 && isset($body['text']['format']) && (str_contains($msg, 'format') || str_starts_with($param, 'text'))) {
            unset($body['text']); // el modelo no acepta salida estructurada: se pide JSON en las instrucciones
        } else {
            break;
        }
    }
    error_log('OpenAI HTTP ' . $status . ' ' . substr($errorBody . $streamError, 0, 400));
    return ['ok' => false, 'text' => $full, 'status' => $status, 'code' => $code, 'model' => $body['model'], 'usage' => null, 'partial' => false];
}

/** Mensaje claro para el alumno según el error de OpenAI. */
function openai_error_message(array $r): string {
    return match (true) {
        $r['code'] === 'model_not_found' => 'Tu proyecto de OpenAI no tiene habilitado ningún modelo de texto. Habilitá modelos en platform.openai.com → Settings → Project → Limits.',
        $r['code'] === 'insufficient_quota' => 'La cuenta de OpenAI no tiene saldo de API. Cargá créditos en platform.openai.com.',
        $r['status'] === 401 => 'OpenAI rechazó la clave. Revisá OPENAI_API_KEY en Railway.',
        $r['status'] === 429 => 'OpenAI está limitando las solicitudes. Probá en unos segundos.',
        $r['status'] === 0 => 'No se pudo conectar con OpenAI.',
        default => 'OpenAI no respondió (HTTP ' . $r['status'] . ').',
    };
}
