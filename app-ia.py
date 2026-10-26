import os, json, hmac, tempfile, wave
from io import BytesIO
from pathlib import Path
from threading import Lock
from flask import Flask, request, jsonify, send_file
import requests
from faster_whisper import WhisperModel
from piper import PiperVoice

app=Flask(__name__)
app.config["MAX_CONTENT_LENGTH"]=12*1024*1024
lock=Lock()
whisper=None
piper=None
TOKEN=os.environ.get("MYTEACHER_IA_TOKEN","")
MODEL=os.environ.get("MYTEACHER_MODEL","llama3.2:3b")
LEVELS={
"A1":"Beginner: basic vocabulary, very short simple sentences.",
"A2":"Elementary: everyday situations, familiar grammar.",
"B1":"Intermediate: natural conversations and opinions.",
"B2":"Upper intermediate: work, culture, nuanced vocabulary.",
"C1":"Advanced: abstract ideas and idiomatic language.",
"C2":"Proficient: subtle distinctions and advanced expression."
}
def check():
    return bool(TOKEN) and hmac.compare_digest(request.headers.get("X-MyTeacher-Token",""),TOKEN)
@app.before_request
def authenticate():
    if request.path == "/healthz" and request.method == "GET":
        return None
    if not check(): return jsonify(error="Unauthorized"),401
@app.get("/healthz")
def readiness():
    try:
        response = requests.get("http://127.0.0.1:11434/api/tags", timeout=3)
        response.raise_for_status()
        ready = bool(TOKEN) and any(m.get("name") == MODEL for m in response.json().get("models", []))
        return jsonify(ok=ready), 200 if ready else 503
    except (requests.RequestException, ValueError):
        return jsonify(ok=False), 503
@app.get("/salud")
def health():
    return jsonify(ok=True,model=MODEL)
def get_whisper():
    global whisper
    if whisper is None: whisper=WhisperModel("base",device="cpu",compute_type="int8")
    return whisper
def get_piper():
    global piper
    if piper is None: piper=PiperVoice.load("/app/en_US-lessac-medium.onnx")
    return piper
@app.post("/conversar")
def conversar():
    try:
        question=(request.form.get("text") or "").strip()
        if "audio" in request.files:
            upload=request.files["audio"]
            suffix=Path(upload.filename or "audio.webm").suffix.lower()
            if suffix not in (".webm",".wav",".mp3",".m4a",".ogg",".mp4"): return jsonify(error="Invalid audio format"),400
            with tempfile.NamedTemporaryFile(delete=False,suffix=suffix) as temp:
                upload.save(temp)
                file=temp.name
            try:
                with lock:
                    parts,_=get_whisper().transcribe(file,beam_size=1,vad_filter=True)
                    question=" ".join(p.text.strip() for p in parts)
            finally: Path(file).unlink(missing_ok=True)
        if not question or len(question)>1500:return jsonify(error="Missing or invalid utterance"),400
        level=request.form.get("level","A1").upper()
        if level not in LEVELS:return jsonify(error="Invalid CEFR level"),400
        try:
            history=json.loads(request.form.get("history","[]"))
            if not isinstance(history,list):history=[]
            history=[{"role":m["role"],"content":str(m["content"])[:1000]} for m in history[-8:] if isinstance(m,dict) and m.get("role") in ("user","assistant") and "content" in m]
        except (TypeError,KeyError,ValueError):history=[]
        system=("You are MyTeacher, a patient English conversation tutor. Speak only English, including corrections. "
                "Never speak Spanish. Keep answers under 55 words, ask exactly one question at a time. "
                "Correct mistakes briefly and politely. Never adopt the student's identity. "
                "Use no Markdown or emoji. Student level "+level+": "+LEVELS[level])
        response=requests.post("http://127.0.0.1:11434/api/chat",json={
          "model":MODEL,"stream":False,
          "messages":[{"role":"system","content":system},*history,{"role":"user","content":question}],
          "options":{"num_predict":160,"temperature":0.6}},timeout=240)
        response.raise_for_status()
        answer=response.json().get("message",{}).get("content","").strip()
        if not answer:raise RuntimeError("Empty model reply")
        return jsonify(question=question,answer=answer)
    except Exception:
        app.logger.exception("Conversation failed")
        return jsonify(error="The model is not ready or a processing error occurred"),503
@app.post("/voz")
def voz():
    try:
        t=(request.get_json(silent=True) or {}).get("text","")
        if not isinstance(t,str) or not t.strip():return jsonify(error="Missing text"),400
        buf=BytesIO()
        with lock:
            with wave.open(buf,"wb") as w:get_piper().synthesize_wav(t[:2000],w)
        buf.seek(0)
        return send_file(buf,mimetype="audio/wav")
    except Exception:
        app.logger.exception("Speech failed")
        return jsonify(error="Speech generator unavailable"),503
