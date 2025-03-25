#!/bin/bash

set -euo pipefail

VERSION=$(jq -r '.version' packages/typescript/package.json)
echo "🔖 Releasing version: v$VERSION"

echo "🌀 Running TypeScript codegen..."
pushd packages/typescript > /dev/null
npm run generate
popd > /dev/null

echo "⚙️ Running Go codegen..."
if ! command -v go-jsonschema >/dev/null 2>&1; then
  echo "🛠️ Installing go-jsonschema..."
  bash ./scripts/install-go-jsonschema.sh
fi
go-jsonschema -p schema -o packages/golang/generated.go --only-models schemas/*.json

echo "🐘 Running PHP codegen..."
pushd packages/php > /dev/null
php vendor/bin/s2c generate:fromspec
popd > /dev/null

echo "📦 Updating composer.json..."
jq ".version = \"$VERSION\"" packages/php/composer.json > temp.json && mv temp.json packages/php/composer.json

echo "✂️ Updating .gitignore to include generated files..."
if [[ "$OSTYPE" == "darwin"* ]]; then
  sed -i.bak '/# GENERATED FILES START/,/# GENERATED FILES END/d' .gitignore && rm .gitignore.bak
else
  sed -i '/# GENERATED FILES START/,/# GENERATED FILES END/d' .gitignore
fi

git add .
git commit -m "chore(release): v$VERSION packages"
git tag "v$VERSION"

echo "♻️ Restoring .gitignore..."
git checkout HEAD -- .gitignore

echo "✅ Release ready: v$VERSION"