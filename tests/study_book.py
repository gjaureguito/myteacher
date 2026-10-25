"""Books: all chapters, protected access, published-only content and unchanged progress."""
from pathlib import Path
import http.cookiejar, json, os, re, socket, sqlite3, subprocess, tempfile, time
import urllib.request, urllib.parse, urllib.error

ROOT = Path(__file__).resolve().parents[1]
chapters = json.loads((ROOT / 'content/books/chapters.json').read_text(encoding='utf-8'))
visuals = json.loads((ROOT / 'content/books/visual.json').read_text(encoding='utf-8'))
assert set(visuals) == set(chapters)
for slug, visual in visuals.items():
    assert len(visual['foundation'].split()) >= 60, slug
    assert len(visual['steps']) == 3 and all(len(s) == 3 for s in visual['steps']), slug
    assert len(visual['error']) == 3 and visual['error'][0] != visual['error'][1], slug
assert len(chapters) == 30
for slug, chapter in chapters.items():
    assert len(chapter['explain'].split()) >= 55, slug
    assert len(chapter['contrast'].split()) >= 50, slug
    assert len(chapter['practice']) == 4, slug
    assert all(len(item) == 3 and all(item) for item in chapter['practice']), slug
    assert all(chapter[k] for k in ['goal', 'example', 'reason', 'task', 'model', 'speaking']), slug

with tempfile.TemporaryDirectory(prefix='myteacher-book-') as td:
    db_path = Path(td) / 'test.sqlite'
    with socket.socket() as sock:
        sock.bind(('127.0.0.1', 0))
        port = sock.getsockname()[1]
    base = f'http://127.0.0.1:{port}'
    env = dict(os.environ, DB_DRIVER='sqlite', SQLITE_PATH=str(db_path),
               ADMIN_EMAILS='', APP_URL=base)
    fixture = """<?php require 'src/bootstrap.php';
create_user('reader@example.test','Book Reader','Local-reader-123',['access_until'=>'2099-01-01 00:00:00','sub_status'=>'manual']);
create_user('demo@example.test','Demo Reader','Local-reader-123');
"""
    subprocess.run(['php'], input=fixture, text=True, cwd=ROOT, env=env,
                   check=True, capture_output=True)
    db = sqlite3.connect(db_path)
    units = db.execute('SELECT id,slug,level_code,position FROM units ORDER BY level_code,position').fetchall()
    assert len(units) == 30 and {u[1] for u in units} == set(chapters)
    before = db.execute('SELECT COUNT(*) FROM exercises').fetchone()[0]
    server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public'],
                              cwd=ROOT, env=env, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    def request(path, data=None):
        try:
            response = client.open(base + path, None if data is None else urllib.parse.urlencode(data).encode(), timeout=10)
        except urllib.error.HTTPError as error:
            response = error
        return response.status, response.read().decode(), response.geturl(), response.headers
    def login(email):
        _, html, _, _ = request('/login.php')
        token = re.search(r'name="csrf" value="([a-f0-9]+)"', html)[1]
        return request('/login.php', {'csrf':token, 'email':email, 'password':'Local-reader-123'})
    try:
        for _ in range(60):
            try:
                request('/login.php'); break
            except OSError:
                time.sleep(.1)
        else:
            raise AssertionError('PHP server did not start')
        assert request('/book.php?code=a1')[2].endswith('/login.php')
        assert request('/grammar.php')[2].endswith('/login.php')
        login('reader@example.test')
        for code in ['a1','a2','b1','b2','c1','c2']:
            status, html, _, headers = request('/book.php?code=' + code)
            assert status == 200 and 'Proyecto de cierre' in html and 'Qué vas a aprender' in html
            assert 'no-store' in headers['Cache-Control']
            assert 'data-picture-practice' in html and 'Comprobar interpretación' in html
        for uid, slug, code, position in units:
            status, html, _, _ = request(f'/book.php?code={code}&chapter={slug}')
            assert status == 200 and html.count('Ver solución comentada') == 4, slug
            assert 'Abrir práctica de este capítulo' in html and 'Vocabulario para usar' in html, slug
            assert chapters[slug]['goal'] in html, slug
            assert 'data-study-lab' in html and html.count('data-stage=') == 3, slug
            assert 'Comprobar corrección' in html and 'Relacioná forma y función' in html, slug
        for uid, slug, code, position in units:
            status, html, _, headers = request(f'/grammar.php?id={uid}')
            assert status == 200 and 'grammar-reference' in html and 'grammar-practice' in html, slug
            assert 'no-store' in headers['Cache-Control'], slug
        assert request('/grammar.php?id=invalid')[0] == 404
        assert request('/grammar.php?id[]=1')[0] == 404
        status, html, _, _ = request('/unit.php?id=' + str(units[0][0]))
        assert status == 200 and 'Desarrollo del libro y práctica guiada' in html
        assert request('/book.php?code=invalid')[0] == 404
        assert request('/book.php?code=a1&chapter=../../src/auth')[0] == 404
        assert request('/book.php?code[]=a1')[0] == 404
        assert request('/book.php?code=a2&chapter=a1-verb-to-be')[0] == 404
        # Remove publication from a real chapter; neither the index nor direct route may expose it.
        hidden = next(u for u in units if u[3] != 1)
        db.execute('UPDATE units SET is_published=0 WHERE id=?', [hidden[0]]); db.commit()
        assert request(f'/book.php?code={hidden[2]}&chapter={hidden[1]}')[0] == 404
        assert request(f'/grammar.php?id={hidden[0]}')[0] == 404
        assert hidden[1] not in request('/book.php?code=' + hidden[2])[1]
        db.execute('UPDATE units SET is_published=1 WHERE id=?', [hidden[0]]); db.commit()
        # Fresh session for a demo account.
        client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
        login('demo@example.test')
        for uid, slug, code, position in units:
            status, html, url, _ = request(f'/book.php?code={code}&chapter={slug}')
            grammar_status, grammar_html, grammar_url, _ = request(f'/grammar.php?id={uid}')
            if position == 1:
                assert grammar_status == 200 and 'grammar-reference' in grammar_html
                assert status == 200 and 'Práctica guiada y razonada' in html, slug
            else:
                assert grammar_url.endswith('/account.php')
                assert url.endswith('/account.php') and 'Práctica guiada y razonada' not in html, slug
        assert db.execute('SELECT COUNT(*) FROM exercises').fetchone()[0] == before
        assert db.execute('SELECT COUNT(*) FROM attempts').fetchone()[0] == 0
        assert db.execute('SELECT COUNT(*) FROM exercise_status').fetchone()[0] == 0
        print('PASS: 6 books and picture workshops, 30 chapters and visual labs, 120 explained paper activities; access and publication checks; unchanged progress')
    finally:
        server.terminate(); server.wait(timeout=10); db.close()
