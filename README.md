# Docker Practice Handson

Docker と Docker Compose を使って、`app`, `db`, `mail` を少しずつ追加していくハンズオン用プロジェクト。

ステップごとに独立したフォルダを使う。

```text
step1_app
step2_app_db
step3_app_db_mail
```

最初は `step1_app` で Dockerfile だけを使い、`app` コンテナを起動する。

## Setup

```bash
cd step1_app
docker build -t handson-step1-app -f docker/app/Dockerfile .
docker run --rm --name handson-step1-app -e APP_MESSAGE="Hello from docker run environment" -e APP_ENV=handson -p 8080:80 -v "$PWD/src:/var/www/html" handson-step1-app
```

ブラウザで確認する。

```text
http://localhost:8080
```

## Docs

- [docs/進め方.md](docs/進め方.md)
- [docs/ハンズオン台本.md](docs/ハンズオン台本.md)
- [docs/ステップ1_appコンテナ.md](docs/ステップ1_appコンテナ.md)
- [docs/ステップ2_dbコンテナ.md](docs/ステップ2_dbコンテナ.md)
- [docs/ステップ3_mailコンテナ.md](docs/ステップ3_mailコンテナ.md)
- [docs/dockerコマンド集.md](docs/dockerコマンド集.md)
