#!/usr/bin/env bash
set -euo pipefail

# If PORT is already set in the environment or .env, use it.
# Otherwise, find a free port automatically.
if [ -z "${PORT:-}" ] && ! grep -q '^PORT=' .env 2>/dev/null; then
    PORT=$(python3 -c 'import socket; s=socket.socket(); s.bind(("",0)); print(s.getsockname()[1]); s.close()')
    export PORT
fi

echo "Starting WordPress on port ${PORT:-$(grep '^PORT=' .env | cut -d= -f2)}"
exec docker compose up --build
