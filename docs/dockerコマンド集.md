# Dockerコマンド集

このハンズオンで使う Docker / Docker Compose コマンドのチートシート。手元に置いてリファレンスとして使う。

## 状況確認系

起動中のコンテナを一覧する。

```bash
docker ps
```

停止中も含めて全コンテナを一覧する。

```bash
docker ps -a
```

ローカルに保存されているイメージを一覧する。

```bash
docker images
```

このホスト上にある Compose プロジェクトを一覧する (停止中も含む)。

```bash
docker compose ls -a
```

カレントの Compose プロジェクトに属するサービスの状態を見る。

```bash
docker compose ps
```

各コンテナの CPU / メモリ使用状況をリアルタイムに見る。

```bash
docker stats
```

## イメージ操作

Dockerfile からイメージを作る。`-t` で名前、`-f` で Dockerfile の場所を指定する。

```bash
# step1 での実行例
docker build -t handson-step1-app -f docker/app/Dockerfile .
```

ローカルのイメージを一覧する。

```bash
docker image ls
```

イメージを削除する。

```bash
docker image rm handson-step1-app
```

どのコンテナからも参照されていない dangling イメージを削除する。

```bash
docker image prune
```

## コンテナ操作 (Compose 外)

`docker run` の主なオプション。

| オプション | 意味 |
| --- | --- |
| `--rm` | 停止時にコンテナを自動削除する |
| `-d` | バックグラウンドで起動する |
| `--name NAME` | コンテナに名前を付ける |
| `-e KEY=VALUE` | コンテナ内の環境変数を指定する |
| `-p HOST:CONTAINER` | ホストとコンテナのポートをつなぐ |
| `-v HOST:CONTAINER` | ホストのパスをコンテナに bind mount する |

起動中のコンテナを停止する。

```bash
docker stop handson-step1-app
```

停止中のコンテナを再開する。

```bash
docker start handson-step1-app
```

コンテナを再起動する。

```bash
docker restart handson-step1-app
```

停止済みのコンテナを削除する。

```bash
docker rm handson-step1-app
```

コンテナのログを末尾追従で見る。

```bash
docker logs -f handson-step1-app
```

コンテナ内に対話シェルで入る。`bash` が無いイメージでは `sh` を使う。

```bash
docker exec -it handson-step1-app bash
# bash が無い場合
docker exec -it handson-step1-app sh
```

コンテナ内の特定の環境変数を確認する。

```bash
docker exec handson-step1-app printenv APP_MESSAGE
```

## Compose 操作

`.env` を展開した最終的な設定を確認する。起動前のチェックに使う。

```bash
docker compose config
```

ビルドし直してバックグラウンドで起動する。Dockerfile や依存を変更したときはこちら。

```bash
docker compose up -d --build
```

既存イメージのままバックグラウンドで起動する。設定変更のみのときはこちら。

```bash
docker compose up -d
```

スタックを停止してコンテナとネットワークを削除する。volume は残る。

```bash
docker compose down
```

スタックを停止し、名前付き volume も削除する。DB のデータも消えるので注意。

```bash
docker compose down -v
```

サービスの状態を見る。

```bash
docker compose ps
```

全サービスまたは特定サービスのログを末尾追従で見る。

```bash
docker compose logs -f
docker compose logs -f app
```

サービスのコンテナに対話シェルで入る。

```bash
docker compose exec app bash
```

特定サービスだけビルドし直す。

```bash
docker compose build app
```

特定サービスを再起動する。

```bash
docker compose restart app
```

## このハンズオン固有の便利コマンド

step2 / step3 の MySQL に入る。

```bash
docker compose exec db mysql -u app_user -ppassword app_db
```

app コンテナから db への疎通を PHP の PDO で確認する。`1` が返れば接続できている。

```bash
docker compose exec app php -r 'echo (new PDO("mysql:host=db;dbname=app_db", "app_user", "password"))->query("SELECT 1")->fetchColumn() . "\n";'
```

step3 の Mailpit Web UI に HTTP で到達できるか確認する。`MAIL_WEB_PORT` は `.env` の値を使う。`200` が返れば OK。

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:${MAIL_WEB_PORT}
```

step3 で app から mail への SMTP (1025) 疎通を確認する。`ok` が出れば接続できている。

```bash
docker compose exec app php -r '$s=fsockopen("mail",1025,$e,$m,3); echo $s?"ok\n":"$m\n";'
```

## volume / network

ローカルの volume を一覧する。

```bash
docker volume ls
```

volume を削除する。中身のデータも消える。

```bash
docker volume rm step2_app_db_db_data
```

ローカルのネットワークを一覧する。

```bash
docker network ls
```

ネットワークに繋がっているコンテナや subnet を確認する。

```bash
docker network inspect step3_app_db_mail_app_network
```

## 後片付け

カレントスタックを停止する。普段はこれで十分。

```bash
docker compose down
```

カレントスタックを停止し、名前付き volume も消す。DB データも消えるため、戻したくないときだけ使う。

```bash
docker compose down -v
```

ホスト全体の未使用リソース (停止コンテナ、dangling イメージ、未使用ネットワーク) を削除する。他プロジェクトにも影響するので注意。

```bash
docker system prune
```

未使用イメージと volume まで含めて削除する。最も破壊的。実行前に何が消えるかを必ず確認する。

```bash
docker system prune -a --volumes
```

## このハンズオンでの並走運用メモ

全 step を同時に起動して比べたい場合は、`.env` でホスト側ポートを step ごとにずらしておく運用にしている。例として次のような割り当て。

| step | APP | DB | SMTP | MAIL Web |
| --- | --- | --- | --- | --- |
| step1 | 18080 | - | - | - |
| step2 | 28080 | 23306 | - | - |
| step3 | 38080 | 33306 | 31025 | 38025 |

step1 を裏で動かしたい場合は `--rm` を外して `-d` で起動する。停止したあとは `docker rm` で明示的に削除する。

```bash
docker run -d --name handson-step1-app -e APP_MESSAGE="Hello" -e APP_ENV=handson -p 18080:80 -v "$PWD/src:/var/www/html" handson-step1-app
docker stop handson-step1-app
docker rm handson-step1-app
```

step2 / step3 の Compose スタックは、それぞれのディレクトリで `docker compose down` を打てばまとめて止められる。

```bash
cd step2_app_db && docker compose down
cd step3_app_db_mail && docker compose down
```
