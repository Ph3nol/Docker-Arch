#!/bin/sh

GITHUB_LAST_RELEASE_ENDPOINT="https://api.github.com/repos/Ph3nol/Docker-Arch/releases/latest"
DESTINATION_BIN_PATH="/usr/local/bin/docker-arch"

echo "--- Docker-Arch - Installation script ---"

PHAR_DOWNLOAD_LINK=$(curl -s "$GITHUB_LAST_RELEASE_ENDPOINT" | grep browser_download_url | cut -d '"' -f 4)
echo "• Downloading from $PHAR_DOWNLOAD_LINK..."
curl -L -s -o /tmp/docker-arch "$PHAR_DOWNLOAD_LINK"

echo "• Installing..."
chmod +x /tmp/docker-arch
mv /tmp/docker-arch "$DESTINATION_BIN_PATH"

echo "• Done. Enjoy! 🐳"
