# coachtech フリマ

## 環境構築

### Docker ビルド

以下を実行します

1. `git clone https://github.com/torch29/matasumoto-mogikadai1.git`
2. `docker-compose.yml`に、下記を追記/確認します。

   MailHog（後述）を使用するための設定です。

   ```
   mailhog:
      image: mailhog/mailhog
      ports:
         - "8025:8025"
         - "1025:1025"
      environment:
            MH_STORAGE: memory
   ```

3. docker desktop アプリを起動する
4. `docker-compose up -d --build`

### Laravel 環境構築

1. `docker-compose exec php bash`
2. `composer install` を実行
3. `cp .env.example .env` を実行し、.env.example を .env にコピーする。
4. .env ファイルを開き、
   - `DB_HOST=127.0.0.1` を `DB_HOST=mysql` に変更する。
   - DB_DATABASE, DB_USERNAME, DB_PASSWORD を任意に変更する。
     （例）
     ```
     DB_DATABASE=laravel_db
     DB_USERNAME=laravel_user
     DB_PASSWORD=laravel_pass
     ```
5. ```
   php artisan key:generate
   ```
6. マイグレーションの実行

   ```
   php artisan migrate
   ```

7. シーディングの実行でダミーデータが作られます

   ```
   php artisan db:seed
   ```

8. 下記コマンドにて、シンボリックリンクの生成をお願いします。

   public 下に storage ディレクトリが作成され参照します。

   ```
   php artisan storage:link
   ```

### Stripe の設定

Laravel Cashier を用いて Stripe での決済テスト を実装しています。

1. 環境構築の際の`composer install`にて、Laravel Cashier がインストールされます。

2. Stripe にアクセスし、[今すぐ始める]ボタンから、無料のアカウント登録/サインインをお願いします。（要メールアドレス。）

   https://stripe.com/jp

3. メールアドレス、氏名、国などの必要項目を入力し、アカウントを作成のうえサインインしてください。

4. ページ下部にある「開発者」をクリックし、開いたメニューの中からさらに「API キー」をクリックします。

5. 開いたページに、公開可能キーとシークレットキーが表示されています。

6. `.env` ファイルを開き、確認した公開キーとシークレットキーを以下の欄に設定します。
   ```
   STRIPE_KEY=公開可能キー（pk_test_...）
   STRIPE_SECRET=シークレットキー（sk_test_...）
   ```

### MailHog の設定

フリマアプリに会員登録する際にメールアドレス認証が必要となります。

認証用のメールを確認するメールサーバーとして実装しています。

1. `docker-compose.yml`に、下記が設定されていることを確認します。（Docker ビルドの 2 で設定済みの場合スキップ）

   ```
     mailhog:
      image: mailhog/mailhog
      ports:
         - "8025:8025"
         - "1025:1025"
      environment:
            MH_STORAGE: memory
   ```

2. もしも 1. の内容を修正した場合は Docker を再ビルドします。（MailHog のイメージをビルド）

   ```
   docker-compose up -d --build
   ```

3. `.env` ファイルを開き以下の項目を設定します。MAIL_FROM_ADDRESS 欄は、適当なもので OK です。
   ```
   MAIL_DRIVER=smtp
   MAIL_HOST=mailhog
   MAIL_PORT=1025
   MAIL_USERNAME=
   MAIL_PASSWORD=
   MAIL_ENCRYPTION=
   MAIL_FROM_ADDRESS=mailhog@mailhog.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```
4. http://localhost:8025 にアクセスして、送信されたメールを確認できます。

   アプリ内では、会員登録後の「メール認証誘導画面」にあるボタンをクリックでも遷移できます。

## 使用技術

- PHP 7.4.9
- Laravel 8.83.8
- MySQL 8.0.26
- MailHog （会員登録時のメール確認用に使用）
- Stripe（商品購入の決済テストに使用）
- PHPUnit

## ER 図

```
ER図は以下をご参照ください。
```

![ER図](ER.drawio.png)

## 使用方法

- トップページは、'/' です。出品された商品の全一覧が表示されます。
- トップページや商品詳細画面の閲覧は、未ログインユーザーでも可能です。
- 商品の出品や購入、コメント・いいね機能の利用には会員登録およびログインが必要です。ログインしていない場合、ログインもしくは登録を促す画面が表示されます。
- 会員登録をすると、以下の機能が利用できます。
  - 商品にいいねをつけることができ、トップページのマイリストから確認できます。
  - マイページから、自身が出品した商品・購入した商品の一覧が確認できます。
  - コメント機能を利用できます。
- シーディングにてダミーデータを作成すると、「テスト　ユーザー」という名前でログインすることが可能です。
  - テスト　ユーザーでログインすると、マイページから自身が出品した商品/購入した商品が確認できます。
  - 出品した商品「腕時計」には、自然な内容のコメントのやり取りのダミーデータを入れています。それ以外のコメントはランダムです。
  - カード決済での購入テストの際は「4242 4242 4242 4242」という番号でテストできます。参照：
    https://docs.stripe.com/testing?locale=ja-JP
  - テスト　ユーザーのログイン情報は以下の通りです。
  ```
  メールアドレス： test@example.com
  パスワード： 12345678
  ```

## URL

- フリマアプリのトップページ：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025
  （会員登録後のボタンクリックからも遷移できます）
