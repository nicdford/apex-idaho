#!/usr/bin/env bash
#
# Local dry-run of the SiteGround deploy. Mirrors .github/workflows/deploy.yml
# but adds --dry-run to every rsync and skips Composer/Slack.
#
# Required env vars (export them or put in scripts/.deploy.env):
#   SITEGROUND_HOST       hostname / IP
#   SITEGROUND_USER       SSH user
#   SITEGROUND_SSH_PORT   SSH port
#   SITEGROUND_PATH       absolute path to WP root on SiteGround (NO trailing slash)
#   SITEGROUND_SSH_KEY    path to private key file (defaults to ~/.ssh/id_rsa)
#
# Usage:
#   scripts/dry-run-deploy.sh
#
# Output lists every file that WOULD be created, updated, or deleted.
# Nothing is actually transferred.

set -euo pipefail

cd "$(dirname "$0")/.."

if [ -f scripts/.deploy.env ]; then
    set -a
    # shellcheck disable=SC1091
    source scripts/.deploy.env
    set +a
fi

: "${SITEGROUND_HOST:?Set SITEGROUND_HOST}"
: "${SITEGROUND_USER:?Set SITEGROUND_USER}"
: "${SITEGROUND_SSH_PORT:?Set SITEGROUND_SSH_PORT}"
: "${SITEGROUND_PATH:?Set SITEGROUND_PATH}"
SITEGROUND_SSH_KEY="${SITEGROUND_SSH_KEY:-$HOME/.ssh/id_rsa}"

SSH="ssh -i $SITEGROUND_SSH_KEY -p $SITEGROUND_SSH_PORT -o StrictHostKeyChecking=no"
REMOTE="$SITEGROUND_USER@$SITEGROUND_HOST:$SITEGROUND_PATH"

echo "=== DRY RUN: themes (with --delete) ==="
rsync -avzn --delete -e "$SSH" \
    apps/wordpress/wp-content/themes/ \
    "$REMOTE/wp-content/themes/"

echo
echo "=== DRY RUN: plugins (additive) ==="
rsync -avzn -e "$SSH" \
    apps/wordpress/wp-content/plugins/ \
    "$REMOTE/wp-content/plugins/"

echo
echo "=== DRY RUN: Pitwall plugin (additive) ==="
rsync -avzn -e "$SSH" \
    apps/pitwall-wp/ \
    "$REMOTE/wp-content/plugins/pitwall/"

echo
echo "Dry run complete. Nothing was transferred."
