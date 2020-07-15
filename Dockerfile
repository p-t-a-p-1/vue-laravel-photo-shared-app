# ベースイメージ
FROM php:7.4.1-fpm

# composerインストールのためのシェルをコンテナ先にコピー
COPY install-composer.sh /

# コマンド実行
RUN apt-get update \
    && apt-get install -y wget git unzip libpq-dev \
    && : 'Install Node.js' \
    && curl -sL https://deb.nodesource.com/setup_12.x | bash - \
    && apt-get install -y nodejs \
    && : 'Install PHP Extensions' \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && : 'Install Composer' \
    && chmod 755 /install-composer.sh \
    && /install-composer.sh \
    && mv composer.phar /usr/local/bin/composer

# カレントディレクトリ移動
WORKDIR /var/www/html/vuesplash