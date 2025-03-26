#!/bin/bash

set -euo pipefail

if [ -z "${VERSION:-}" ]; then
  echo "❌ VERSION is not set. Please pass it via environment."
  exit 1
fi

echo "📦 Releasing version: v$VERSION"

echo "🧹 Cleaning up old generated files..."
find packages/typescript/generated -type f ! -name ".gitkeep" -delete
rm -f packages/go/generated.go
rm -rf packages/php/src

echo "🌀 Running TypeScript codegen..."
pushd packages/typescript > /dev/null
npm install
npm run build
popd > /dev/null

echo "📦 Updating package.json..."
jq ".version = \"$VERSION\"" packages/typescript/package.json > temp.json && mv temp.json packages/typescript/package.json

echo "⚙️ Running Go codegen..."
if ! command -v go-jsonschema >/dev/null 2>&1; then
  echo "🛠️ Installing go-jsonschema..."
  bash ./scripts/install-go-jsonschema.sh
fi
go-jsonschema -p schema -o packages/go/generated.go --only-models schemas/*.json

echo "🐘 Running PHP codegen..."
pushd packages/php > /dev/null
composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
php vendor/bin/s2c generate:fromspec
popd > /dev/null

echo "📦 Updating composer.json..."
jq ".version = \"$VERSION\"" packages/php/composer.json > temp.json && mv temp.json packages/php/composer.json

echo "📥 Adding and committing generated files..."
git add .
git commit -m "chore(release): v$VERSION packages"
git push

echo "✅ Release prepared and committed for v$VERSION"