#!/usr/bin/env bash
set -euo pipefail

# Ensure the Pitwall plugin (which lives at apps/pitwall-wp/ as a sibling of
# apps/pitwall-bot/) is visible inside this WP install via a local symlink.
# The symlink is gitignored.
PLUGIN_LINK="wp-content/plugins/pitwall"
if [ ! -e "$PLUGIN_LINK" ]; then
    ln -s ../../../pitwall-wp "$PLUGIN_LINK"
    echo "Linked Pitwall plugin -> apps/pitwall-wp"
fi

# If PORT is already set in the environment or .env, use it.
# Otherwise, find a free port automatically.
if [ -z "${PORT:-}" ] && ! grep -q '^PORT=' .env 2>/dev/null; then
    PORT=$(python3 -c 'import socket; s=socket.socket(); s.bind(("",0)); print(s.getsockname()[1]); s.close()')
    export PORT
fi

echo "Starting WordPress on port ${PORT:-$(grep '^PORT=' .env | cut -d= -f2)}"
exec docker compose up --build
