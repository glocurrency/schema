#!/bin/bash
set -e

# Navigate to the repo root if not already there
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPO_ROOT="$(dirname "$SCRIPT_DIR")"

echo "Generating TypeScript types..."
# npm run typescript build

echo "Generating Go structs..."
# go install github.com/atombender/go-jsonschema@v0.17.0
go-jsonschema -p schema -o generated.go --only-models "$REPO_ROOT/schemas/*.json" -o "$REPO_ROOT/packages/golang/generated.go"

echo "Generating PHP classes..."
# https://github.com/wol-soft/php-json-schema-model-generator
