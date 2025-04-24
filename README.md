# coachtech フリマ

## 環境構築

### Docker ビルド

以下を実行します

1. `git clone https://github.com/torch29/matasumoto-mogikadai1.git`
2. docker desktop アプリを起動する
3. `docker-compose up -d --build`

### Laravel 環境構築

1. `docker-compose exec php bash`
2. `composer install` を実行
3. `cp .env.example .env` を実行し、.env.example を .env にコピーする。
4. .env 内ファイルを開き、
   - DB_HOST=127.0.0.1 を DB_HOST=mysql に変更する。
   - DB_DATABASE, DB_USERNAME, DB_PASSWORD を任意に変更する。
     （例えば、以下のとおり）
     ```
     DB_DATABASE=laravel_db
     DB_USERNAME=laravel_user DB_PASSWORD=laravel_pass
     ```
     - SESSION_DRIVER を以下の設定に変更してください。
     ```
      SESSION_DRIVER=database
     ```
5. ```
   php artisan key:generate
   ```
6. セッション保存用のテーブルを作成したあと、
   マイグレーションの実行をお願いします。

   ```
   php artisan session:table
   ```

   ```
   php artisan migrate
   ```

7. ```
   php artisan db:seed
   ```

## 使用技術

- PHP 7.4.9
- Laravel 8.83.8
- MySQL 8.0.26

## ER 図

```
テーブルの説明を記入します
```

![ER図](ER.drawio.png)

## 使用方法

- トップページは、'/' です。出品された商品の全一覧が表示されます。
- 画面の説明など
- ここに記入します
  - 記入します

## URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
