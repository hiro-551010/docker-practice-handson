# ステップ1 appコンテナ

最初のゴールは、PHP の `index.php` を `localhost:8080` で表示すること。

## 作成するファイル

```text
docker_practice_handson/
└── step1_app/
    ├── .dockerignore
    ├── docker/
    │   └── app/
    │       └── Dockerfile
    └── src/
        └── index.php
```

`.dockerignore` には、ビルドコンテキストに含めなくてよいローカルファイルを書く。

## Dockerfile

```dockerfile
# PHP 8.3 と Apache が入っている公式イメージを土台にする。
FROM php:8.3-apache

# Apache が公開するドキュメントルートを作業ディレクトリにする。
WORKDIR /var/www/html
```

`php:8.3-apache` は Apache 付きの PHP 公式イメージ。何も追加しなくても、コンテナ起動時に Apache が起動する。

## イメージをビルドする

```bash
cd step1_app
docker build -t handson-step1-app -f docker/app/Dockerfile .
```

`docker build` は Dockerfile からイメージを作る。

- `-t handson-step1-app`: 作成するイメージ名
- `-f docker/app/Dockerfile`: 使う Dockerfile の場所
- `.`: ビルドコンテキスト

## コンテナを起動する

```bash
docker run --rm \
  --name handson-step1-app \
  -e APP_MESSAGE="Hello from docker run environment" \
  -e APP_ENV=handson \
  -p 8080:80 \
  -v "$PWD/src:/var/www/html" \
  handson-step1-app
```

`docker run` はイメージからコンテナを起動する。

- `--rm`: 停止時にコンテナを削除する
- `--name handson-step1-app`: コンテナ名を付ける
- `-e APP_MESSAGE="Hello from docker run environment"`: コンテナに環境変数を渡す
- `-e APP_ENV=handson`: 実行環境を表す環境変数を渡す
- `-p 8080:80`: ホストの `8080` をコンテナの `80` に接続する
- `-v "$PWD/src:/var/www/html"`: ホストの `src` をコンテナに bind mount する

ホストの `8080` が他のアプリで使われている場合は、`-p 18080:80` のように左側だけ変えて回避する。確認 URL も `http://localhost:18080` に読み替える。コンテナ側の `80` は Apache の待ち受けポートなので変えない。

`./src:/var/www/html` により、ホスト側の `src` がコンテナ内の `/var/www/html` にマウントされる。

PHP ファイルを変更したら、ブラウザをリロードするだけで反映される。

## 環境変数を確認する

`src/index.php` では `getenv()` でコンテナ内の環境変数を読んでいる。

```php
$message = getenv('APP_MESSAGE') ?: 'Step 1: app container is running.';
$environment = getenv('APP_ENV') ?: 'local';
```

ブラウザで次のように表示されれば、`docker run -e` で渡した環境変数を PHP から読めている。

```text
Hello from docker run environment
Environment: handson
```

コンテナ内の環境変数をコマンドで確認する場合:

```bash
docker exec handson-step1-app env
```

特定の値だけ確認する場合:

```bash
docker exec handson-step1-app printenv APP_MESSAGE
docker exec handson-step1-app printenv APP_ENV
```

期待される出力:

```text
Hello from docker run environment
handson
```

PHP から `getenv()` で読めるか確認する場合:

```bash
docker exec handson-step1-app php -r 'echo getenv("APP_MESSAGE") . "\n" . getenv("APP_ENV") . "\n";'
```

期待される出力:

```text
Hello from docker run environment
handson
```

`-e` は Dockerfile ではなく、コンテナ起動時に渡す値。起動するたびに値を変えられる。

## 確認

```text
http://localhost:8080
```

## 状態確認

```bash
docker ps
```

## 停止

別のターミナルから止める場合:

```bash
docker stop handson-step1-app
```
