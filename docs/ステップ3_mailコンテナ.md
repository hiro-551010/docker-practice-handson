# ステップ3 mailコンテナ

Step 3 のゴールは、`app` コンテナから `mail` コンテナの Mailpit に接続すること。

## 使うフォルダ

```bash
cd step3_app_db_mail
```

## 作成するファイル

```text
docker_practice_handson/
└── step3_app_db_mail/
    ├── .dockerignore
    ├── .env.example
    ├── docker-compose.yml
    ├── docker/
    │   ├── app/
    │   │   └── Dockerfile
    │   ├── db/
    │   │   └── init/
    │   │       └── 001_create_tables.sql
    │   └── mail/
    └── src/
        └── index.php
```

`.dockerignore` には、`.env` やログなどビルドコンテキストに含めなくてよいファイルを書く。

## 追加されるもの

- `mail` サービス
- Mailpit 公式イメージ
- SMTP 受信用ポート `1025`
- Web UI 用ポート `8025`
- `app` から `mail:1025` への接続

## Dockerfile

```dockerfile
# PHP 8.3 と Apache が入っている公式イメージを土台にする。
FROM php:8.3-apache

# PHP から MySQL に接続するための PDO MySQL 拡張をインストールする。
RUN docker-php-ext-install pdo_mysql

# Apache が公開するドキュメントルートを作業ディレクトリにする。
WORKDIR /var/www/html
```

Step 3 でも DB 接続を行うため、Step 2 と同じく `pdo_mysql` が必要。

## .env.example

```env
APP_PORT=8080

DB_PORT=3306
MYSQL_DATABASE=app_db
MYSQL_USER=app_user
MYSQL_PASSWORD=password
MYSQL_ROOT_PASSWORD=root_password

MAIL_WEB_PORT=8025
MAIL_SMTP_PORT=1025
```

`MAIL_WEB_PORT` はブラウザで Mailpit を見るためのポート。`MAIL_SMTP_PORT` は SMTP 接続用のポート。

## 起動

```bash
cp .env.example .env
docker compose config
docker compose up -d --build
```

`.env` はローカル環境用なので Git には含めない。公開するのは `.env.example`。

`docker compose config` で、`app`、`db`、`mail` の3サービスが解決されることを確認する。

ホストの `8080`、`3306`、`1025`、`8025` が他のアプリで使われている場合は、`.env` の `APP_PORT` / `DB_PORT` / `MAIL_SMTP_PORT` / `MAIL_WEB_PORT` を空いている値に書き換える。`MAIL_WEB_PORT` は app コンテナにも渡しているため、画面の「Mailpit Web UI」リンクも `.env` の値に追従する。コンテナ側のポート (`80` / `3306` / `1025` / `8025`) は変えない。

## 確認

App:

```text
http://localhost:8080
```

Mailpit:

```text
http://localhost:8025
```

App 画面に次が出れば、`app` から `mail` に接続できている。

```text
Mail status: mail:1025 is reachable
```

## 停止

```bash
docker compose down
```

Step 2 のコンテナが起動したままだと `8080` や `3306` が競合する。Step 3 を始める前に Step 2 を止める。

```bash
docker compose down
```
