# phalco

## コマンド

### 詳細は`vendor/bin/sail（以下sail）`で確認

サービス起動

```shell
sail up -d
```

サービス停止

```shell
sail down
```

Vite Dev サーバー起動

```shell
sail npm run dev
```

DBリセット＆動作チェック用データ投入

```shell
sail artisan migrate:fresh --seed
```

キューワーカーの実行
```shell
sail queue:work
```

Reverbの実行（キューワーカーを起動させた状態で実行）
```shell
sail reverb:start
```

通貨為替レートの更新（キューワーカーを起動させた状態で実行）
```shell
sail artisan app:update-currency-rates
```

---

## 環境構築

リポジトリをクローンする

クローン後に Laravel Sail を構築する

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

`.env.example` を `.env` としてコピーする

ドメイン設定してアクセスする場合は `APP_URL` を変更する

### Minioの設定

ブラウザで `http://localhost:8900` にログイン ( sail / password )

左メニュー `Buckets` → `Create Bucket` から `Bucket Name` を `images` で作成

`images` の `Access Policy` を `Public` に変更

左メニュー `Buckets` → `Create Bucket` から `Bucket Name` を `secure` で作成
