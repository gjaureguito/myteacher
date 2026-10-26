#!/bin/sh
set -eu
ollama serve &
for i in $(seq 1 90); do
  if ollama list >/dev/null 2>&1; then break; fi
  sleep 2
done
model="${MYTEACHER_CHAT_MODEL:-llama3.2:1b}"
if ! ollama show "$model" >/dev/null 2>&1; then
  ollama pull "$model"
fi
exec gunicorn --bind "0.0.0.0:${PORT:-8765}" --workers 1 --threads 4 --timeout 300 app:app
