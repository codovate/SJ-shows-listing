#!/usr/bin/env bash
set -euo pipefail

# Always rebuild .env from the template for deterministic setups
cp .env.example .env

# Primary URL (e.g. https://farlodevtechnicaltest.ddev.site)
PRIMARY_URL="$(ddev describe -j | python3 -c 'import json,sys; print(json.load(sys.stdin)["raw"]["primary_url"])')"

# Standard DDEV DB values
DB_NAME="db"
DB_USER="db"
DB_PASSWORD="db"
DB_HOST="db"

# Local defaults
WP_ENV="development"
WP_IS_MULTISITE="false"
FARLO_DEPLOY_VERSION="local"

python3 - <<PY
from pathlib import Path

replacements = {
    "%DB_NAME%": "${DB_NAME}",
    "%DB_USER%": "${DB_USER}",
    "%DB_PASSWORD%": "${DB_PASSWORD}",
    "%DB_HOST%": "${DB_HOST}",
    "%WP_ENV%": "${WP_ENV}",
    "%WP_HOME%": "${PRIMARY_URL}",
    "%WP_IS_MULTISITE%": "${WP_IS_MULTISITE}",
    "%FARLO_DEPLOY_VERSION%": "${FARLO_DEPLOY_VERSION}",
}

p = Path(".env")
s = p.read_text(encoding="utf-8")
for k, v in replacements.items():
    s = s.replace(k, v)
p.write_text(s, encoding="utf-8")

print("Rendered .env (WP_HOME =", "${PRIMARY_URL}", ")")
PY