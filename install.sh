#!/bin/bash

GITHUB_LAST_RELEASE_ENDPOINT="https://api.github.com/repos/Ph3nol/Docker-Arch/releases/tags/latest"
DESTINATION_BIN_PATH="/usr/local/bin/docker-arch"

echo "--- Docker-Arch - Installation script ---"
echo ""
echo "Looking for your available download package..."

if [ -x "$(command -v wget)" ]
then
    echo "> Going to use WGET to download PHAR package."
    LATEST_RELEASE_CONDITION=$(wget -S --spider "$GITHUB_LAST_RELEASE_ENDPOINT"  2>&1 | grep 'HTTP/1.1 200 OK')
    if [[ $LATEST_RELEASE_CONDITION ]]; then
        PHAR_DOWNLOAD_LINK=$(curl -s "$GITHUB_RELEASE_ENDPOINT" | grep browser_download_url | cut -d '"' -f 4)
    else
        PHAR_DOWNLOAD_LINK="https://github.com/Ph3nol/Docker-Arch/raw/master/dist/docker-arch.phar"
    fi
    echo "• Downloading..."
    wget --quiet --no-check-certificate -O /tmp/docker-arch "$PHAR_DOWNLOAD_LINK"
elif [ -x "$(command -v curl)" ]
then
    echo "> Going to use CURL to download PHAR package."
    LATEST_RELEASE_CONDITION=$(curl --write-out %{http_code} --silent --output /dev/null "$GITHUB_LAST_RELEASE_ENDPOINT")
    if [[ "$LATEST_RELEASE_CONDITION" == "200" ]]; then
        PHAR_DOWNLOAD_LINK=$(curl -s "$GITHUB_RELEASE_ENDPOINT" | grep browser_download_url | cut -d '"' -f 4)
    else
        PHAR_DOWNLOAD_LINK="https://github.com/Ph3nol/Docker-Arch/raw/master/dist/docker-arch.phar"
    fi
    echo "• Downloading..."
    curl -L -s -o /tmp/docker-arch "$PHAR_DOWNLOAD_LINK"
else
    echo "ERROR: no required `wget` or `curl` package has been found."
    exit
fi

echo "• Installing..."
chmod +x /tmp/docker-arch
mv /tmp/docker-arch "$DESTINATION_BIN_PATH"
echo "• Done."
echo ""
echo "Enjoy! 🐳"
