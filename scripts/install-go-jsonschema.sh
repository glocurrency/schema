#!/bin/bash

set -euo pipefail

VERSION="v0.18.0"
BIN_NAME="go-jsonschema"
REPO="omissis/go-jsonschema"
INSTALL_DIR="/usr/local/bin"

OS=$(uname -s)
ARCH=$(uname -m)

case "$OS" in
  Linux) OS="Linux" ;;
  Darwin) OS="Darwin" ;;
  *) echo "Unsupported OS: $OS" && exit 1 ;;
esac

case "$ARCH" in
  x86_64) ARCH="x86_64" ;;
  arm64|aarch64) ARCH="arm64" ;;
  i386|i686) ARCH="i386" ;;
  *) echo "Unsupported architecture: $ARCH" && exit 1 ;;
esac

TAR_NAME="${BIN_NAME}_${OS}_${ARCH}.tar.gz"
DOWNLOAD_URL="https://github.com/${REPO}/releases/download/${VERSION}/${TAR_NAME}"

TMP_DIR=$(mktemp -d)
echo "⬇️ Downloading $BIN_NAME ($OS/$ARCH) to $TMP_DIR"
curl -sSL "$DOWNLOAD_URL" -o "$TMP_DIR/$TAR_NAME"

tar -xzf "$TMP_DIR/$TAR_NAME" -C "$TMP_DIR"

echo "🚀 Installing $BIN_NAME to $INSTALL_DIR"
chmod +x "$TMP_DIR/$BIN_NAME"
sudo mv "$TMP_DIR/$BIN_NAME" "$INSTALL_DIR/$BIN_NAME"

rm -rf "$TMP_DIR"

echo "✅ $BIN_NAME installed to $INSTALL_DIR"