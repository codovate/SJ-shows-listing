#!/bin/bash

## Description: Run composer install
## Usage: install
## Example: "ddev install"
set -o errexit
set -o pipefail
set -o nounset

# Render .env from .env.example using current DDEV project settings
bash .ddev/scripts/render-env.sh

# Run composer install using composer-secrets.json
COMPOSER=composer.json composer install