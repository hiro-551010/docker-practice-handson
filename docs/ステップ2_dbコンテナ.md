# ステップ2 dbコンテナ

Step 2 のゴールは、`app` コンテナから `db` コンテナの MySQL に接続すること。

## 使うフォルダ

```bash
cd step2_app_db
```

## 作成するファイル

```text
docker_practice_handson/
└── step2_app_db/
    ├── .dockerignore
    ├── .env.example
    ├── docker-compose.yml
    ├── docker/
    │   ├── app/
    │   │   └── Dockerfile
    │   └── db/
    │       └── init/
    │           └── 001_create_tables.sql
    └── src/
        └── index.php
```

`.dockerignore` には、`.env` やログなどビルドコンテキストに含めなくてよいファイルを書く。

## 追加されるもの

- `db` サービス
- MySQL 公式イメージ
- `db_data` volume
- 初期化 SQL
- `app` から `db:3306` への接続

## Dockerfile

```dockerfile
# PHP 8.3 と Apache が入っている公式イメージを土台にする。
FROM php:8.3-apache

# PHP から MySQL に接続するための PDO MySQL 拡張をインストールする。
RUN docker-php-ext-install pdo_mysql

# Apache が公開するドキュメントルートを作業ディレクトリにする。
WORKDIR /var/www/html
```

Step 2 では PHP から MySQL に接続するため、Step 1 にはなかった `pdo_mysql` を追加する。

## .env.example

```env
APP_PORT=8080

DB_PORT=3306
MYSQL_DATABASE=app_db
MYSQL_USER=app_user
MYSQL_PASSWORD=password
MYSQL_ROOT_PASSWORD=root_password
```

`docker-compose.yml` では `${MYSQL_DATABASE}` のように `.env` の値を参照する。

## 起動

```bash
cp .env.example .env
docker compose config
docker compose up -d --build
```

`.env` はローカル環境用なので Git には含めない。公開するのは `.env.example`。

`docker compose config` で、`.env` の値が Compose 設定に反映されているか確認する。

ホストの `8080` や `3306` が他のアプリで使われている場合は、`.env` の `APP_PORT` や `DB_PORT` を空いている値 (例: `APP_PORT=18080`、`DB_PORT=13306`) に書き換える。確認 URL も変えたポートに読み替える。コンテナ側のポート (`80` / `3306`) は変えない。

## 確認

```text
http://localhost:8080
```

画面に次が出れば、`app` から `db` に接続できている。

```text
DB status: Step 2: app connected to db
```

## 停止

```bash
docker compose down
```

DB の volume も消して初期状態からやり直す場合:

```bash
docker compose down -v
```

Step 1 のコンテナが起動したままだと `8080` が競合する。Step 2 を始める前に Step 1 を止める。

```bash
docker stop handson-step1-app
```

Step 1 を裏で動かし続けたい場合は、止める代わりに `.env` の `APP_PORT` をずらせば並走できる ([進め方.md](./進め方.md) の「並走モード」参照)。
