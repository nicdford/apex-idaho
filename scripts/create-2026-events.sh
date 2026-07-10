#!/usr/bin/env bash
# Create the 4 APEX Idaho 2026 events via The Events Calendar REST API.
#
# Usage:
#   WP_URL=https://www.apex-idaho.com \
#   WP_USER=your-admin-username \
#   WP_APP_PASSWORD='xxxx xxxx xxxx xxxx xxxx xxxx' \
#   ./scripts/create-2026-events.sh
#
# WP_APP_PASSWORD is a WordPress Application Password
# (Users > Profile > Application Passwords). The user must have permission
# to create events in The Events Calendar.
#
# Pass --dry-run to print the payloads without POSTing.

set -euo pipefail

: "${WP_URL:?set WP_URL, e.g. https://www.apex-idaho.com}"
: "${WP_USER:?set WP_USER}"
: "${WP_APP_PASSWORD:?set WP_APP_PASSWORD}"

DRY_RUN=0
[[ "${1:-}" == "--dry-run" ]] && DRY_RUN=1

ENDPOINT="${WP_URL%/}/wp-json/tribe/events/v1/events"
TZ_NAME="America/Boise"

post_event() {
  local payload="$1"
  if [[ "$DRY_RUN" == "1" ]]; then
    echo "---"
    echo "$payload" | python3 -m json.tool
    return
  fi
  local title
  title=$(echo "$payload" | python3 -c 'import sys,json;print(json.load(sys.stdin)["title"])')
  echo "POST $title"
  local response http_code
  response=$(curl -sS -u "$WP_USER:$WP_APP_PASSWORD" \
    -H "Content-Type: application/json" \
    -w $'\n%{http_code}' \
    -X POST "$ENDPOINT" \
    -d "$payload")
  http_code=$(echo "$response" | tail -n1)
  body=$(echo "$response" | sed '$d')
  echo "  -> HTTP $http_code"
  echo "$body" | python3 -c 'import sys,json;d=json.loads(sys.stdin.read() or "{}");i=d.get("id");u=d.get("url");m=d.get("message");print("  id=",i,"url=",u) if i else print("  error:",m or d)' || echo "  (non-JSON body): $body"
}

build_event() {
  # args: title start end venue description
  python3 - "$@" <<'PY'
import json, sys
title, start, end, venue, description = sys.argv[1:6]
print(json.dumps({
    "title": title,
    "start_date": start,
    "end_date": end,
    "timezone": "America/Boise",
    "status": "publish",
    "description": description,
    "venue": {"venue": venue},
}))
PY
}

# 1. Diamond Cup KBD — Drift Competition 1
post_event "$(build_event \
  "Diamond Cup KBD — Drift Competition 1" \
  "2026-06-13 10:00:00" \
  "2026-06-13 18:00:00" \
  "Meridian Speedway" \
  "Limited to 16 drivers. Hot-Lap and Comp Layout.")"

# 2. Motorplex at the Mill (2 Day)
post_event "$(build_event \
  "Motorplex at the Mill" \
  "2026-06-26 15:00:00" \
  "2026-06-27 13:00:00" \
  "The Mill" \
  "2 Day — Night Drifting / Camping. Friday 3pm–10pm, Saturday 8am–1pm.")"

# 3. Eve of Destruction KBD — Drift Competition 2
post_event "$(build_event \
  "Eve of Destruction KBD — Drift Competition 2" \
  "2026-08-08 10:00:00" \
  "2026-08-08 22:00:00" \
  "Meridian Speedway" \
  "Limited to 16 drivers. Hot-Lap and Comp Layout.")"

# 4. Eve of Destruction KBD — Drift Competition 3
post_event "$(build_event \
  "Eve of Destruction KBD — Drift Competition 3" \
  "2026-08-29 10:00:00" \
  "2026-08-29 22:00:00" \
  "Magic Valley Speedway" \
  "Limited to 16 drivers. Hot-Lap and Comp Layout.")"

echo "Done."
