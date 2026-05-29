# PHP 8.3 と Apache が入っている公式イメージを土台にする。
FROM php:8.3-apache

# PHP から MySQL に接続するための PDO MySQL 拡張をインストールする。
RUN docker-php-ext-install pdo_mysql

# Apache が公開するドキュメントルートを作業ディレクトリにする。
WORKDIR /var/www/html
