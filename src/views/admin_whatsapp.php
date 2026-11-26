<section class="wrap page">
  <h1>Conversaciones de WhatsApp</h1>
  <?php require __DIR__ . '/admin_nav.php'; ?>

  <p class="fine">Los mensajes y teléfonos se guardan en la base de MyTeacher para que el equipo administre las consultas. Solo administradores pueden verlos. Eliminá las conversaciones cuando ya no necesites conservarlas.</p>

  <form method="get" class="panel wa-filters">
    <label>Buscar por teléfono
      <input type="search" name="q" value="<?= e($search) ?>" inputmode="numeric" placeholder="Ej.: 5492646263385">
    </label>
    <label>Desde <input type="date" name="from" value="<?= e($from) ?>"></label>
    <label>Hasta <input type="date" name="to" value="<?= e($to) ?>"></label>
    <button class="btn" type="submit">Filtrar</button>
    <a class="btn btn-ghost" href="/admin/whatsapp.php">Limpiar</a>
  </form>

  <div class="stats">
    <?php foreach ($metrics as $label => $value): ?>
      <div class="stat"><strong><?= (int) $value ?></strong><span><?= e($label) ?></span></div>
    <?php endforeach; ?>
  </div>
  <p class="fine">Las cifras de mensajes respetan el rango de fechas; el filtro por teléfono limita la lista de conversaciones. Las fechas se muestran en UTC.</p>

  <div class="wa-grid">
    <section class="panel" aria-labelledby="wa-list-title">
      <h2 id="wa-list-title">Chats (máximo 100 resultados)</h2>
      <?php if (!$threads): ?>
        <p>Todavía no hay conversaciones guardadas para estos filtros.</p>
      <?php else: ?>
        <div class="wa-list">
          <?php foreach ($threads as $thread): ?>
            <?php
              $params = array_filter(['thread' => (int) $thread['id'], 'q' => $search, 'from' => $from, 'to' => $to], static fn($v) => $v !== '');
              $url = '/admin/whatsapp.php?' . http_build_query($params);
            ?>
            <a class="wa-item<?= $selectedThread && (int) $selectedThread['id'] === (int) $thread['id'] ? ' is-active' : '' ?>" href="<?= e($url) ?>">
              <span class="wa-item-top"><strong>+<?= e($thread['wa_id']) ?></strong><time><?= e($thread['last_message_at']) ?> UTC</time></span>
              <span><?= e(mb_strimwidth((string) $thread['last_body'], 0, 120, '…', 'UTF-8')) ?></span>
              <span class="fine"><?= (int) $thread['inbound_count'] ?> mensajes del contacto</span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="panel" aria-labelledby="wa-chat-title">
      <?php if ($selectedThread): ?>
        <h2 id="wa-chat-title">Chat con +<?= e($selectedThread['wa_id']) ?></h2>
        <p class="fine">Creado <?= e($selectedThread['created_at']) ?> UTC · Último mensaje <?= e($selectedThread['last_message_at']) ?> UTC</p>
        <div class="wa-transcript" aria-label="Transcripción">
          <?php foreach ($conversationMessages as $message): ?>
            <article class="wa-message <?= $message['direction'] === 'in' ? 'wa-in' : 'wa-out' ?>">
              <strong><?= $message['direction'] === 'in' ? 'Contacto' : 'MyTeacher' ?></strong>
              <p><?= nl2br(e($message['body'])) ?></p>
              <time><?= e($message['created_at']) ?> UTC</time>
            </article>
          <?php endforeach; ?>
        </div>
        <form method="post" onsubmit="return confirm('¿Eliminar permanentemente esta conversación y todos sus mensajes?');">
          <?= csrf_field() ?>
          <input type="hidden" name="thread_id" value="<?= (int) $selectedThread['id'] ?>">
          <button class="btn btn-ghost" name="action" value="delete" type="submit">Eliminar conversación</button>
        </form>
      <?php else: ?>
        <h2 id="wa-chat-title">Detalle del chat</h2>
        <p>Elegí una conversación de la lista para ver los mensajes.</p>
      <?php endif; ?>
    </section>
  </div>
</section>
<style>
.wa-filters{display:flex;flex-wrap:wrap;align-items:end;gap:12px;margin:18px 0}
.wa-filters label{display:grid;gap:5px;font-size:.9rem}
.wa-filters input{min-height:42px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px}
.wa-grid{display:grid;grid-template-columns:minmax(280px,.85fr) minmax(320px,1.15fr);gap:16px;align-items:start;margin-top:20px}
.wa-list{display:grid;gap:8px;max-height:70vh;overflow:auto}
.wa-item{display:grid;gap:5px;padding:12px;border:1px solid #dbe4ee;border-radius:10px;color:inherit;text-decoration:none;overflow-wrap:anywhere}
.wa-item:hover,.wa-item.is-active{border-color:#16865b;background:#f0faf5}
.wa-item-top{display:flex;justify-content:space-between;gap:8px;align-items:start}
.wa-item-top time,.wa-message time{font-size:.75rem;color:#64748b}
.wa-transcript{display:grid;gap:10px;max-height:60vh;overflow:auto;padding:10px 2px}
.wa-message{max-width:92%;padding:12px 14px;border-radius:12px;overflow-wrap:anywhere}
.wa-message p{margin:6px 0;white-space:pre-wrap}
.wa-in{justify-self:start;background:#f1f5f9}
.wa-out{justify-self:end;background:#e8f6ed}
@media(max-width:760px){.wa-grid{grid-template-columns:1fr}.wa-list{max-height:42vh}.wa-transcript{max-height:55vh}}
</style>
