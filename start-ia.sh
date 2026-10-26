#!/bin/sh
set -eu
ollama serve &
for i in $(seq 1 90); do
  if ollama list >/dev/null 2>&1; then break; fi
  sleep 2
done
ollama pull "${MYTEACHER_MODEL:-llama3.2:3b}"
exec gunicorn --bind "0.0.0.0:${PORT:-8765}" --workers 1 --threads 4 --timeout 300 app:app
