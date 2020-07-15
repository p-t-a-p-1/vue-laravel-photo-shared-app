# vue-laravel-photo-shared-app

[Vue + Vue Router + Vuex + Laravelで写真共有アプリ](https://www.hypertextcandy.com/vue-laravel-tutorial-introduction/)

まずはチュートリアル進めていって独自機能は後々考えます
DBはPostgreSQL

# 機能要件

* 写真一覧を表示
* 写真を投稿（登録ユーザーのみ）
* 写真にいいねをつける（登録ユーザーのみ）
* 写真のいいねを外す（登録ユーザーのみ）
* 写真に付けられたいいねの数を表示する
* 写真にコメントをつける（登録ユーザーのみ）
* 写真に付けられたコメントを表示する
* 会員登録
* ログイン（登録ユーザーのみ）
* ログアウト（登録ユーザーのみ）

# DB

## photosテーブル

|    列名    |    型    |    特徴    |    備考    |
|:------------------:|:------------------:|:------------------:|:-----------------:|
|    id    |    VARCHAR(255)   |    PRIMARY, UNIQUE, NOT NULL    |    ランダムな文字列    |
|    user_id    |    INTEGER    |    NOT NULL, foreign( users(id) )   |    usersテーブルのidと紐づく    |
|    filename    |    VARCHAR(255)    |    NOT NULL    |    画像ファイル名    |
|    created_at    |    TIMESTAMP    |    -    |    -    |
|    updated_at    |    TIMESTAMP    |    -    |    -    |

## commentsテーブル

SERIALは自動採番

|    列名    |    型    |    特徴    |    備考    |
|:------------------:|:------------------:|:------------------:|:-----------------:|
|    id    |    SERIAL   |    PRIMARY, UNIQUE, NOT NULL    |    ID    |
|    photo_id    |    VARCHAR(255)    |    NOT NULL, foreign( photos(id) )   |    コメントされた写真ID    |
|    user_id    |    INTEGER    |    NOT NULL, foreign( users(id) )    |    コメントしたユーザーID    |
|    content    |    TEXT    |    NOT NULL    |    コメント本文    |
|    created_at    |    TIMESTAMP    |    -    |    -    |
|    updated_at    |    TIMESTAMP    |    -    |    -    |


## likesテーブル

|    列名    |    型    |    特徴    |    備考    |
|:------------------:|:------------------:|:------------------:|:-----------------:|
|    id    |    SERIAL   |    PRIMARY, UNIQUE, NOT NULL    |    ID    |
|    photo_id    |    VARCHAR(255)    |    NOT NULL, foreign( photos(id) )   |    コメントされた写真ID    |
|    user_id    |    INTEGER    |    NOT NULL, foreign( users(id) )    |    コメントしたユーザーID    |
|    created_at    |    TIMESTAMP    |    -    |    -    |
|    updated_at    |    TIMESTAMP    |    -    |    -    |

## usersテーブル

|    列名    |    型    |    特徴    |    備考    |
|:------------------:|:------------------:|:------------------:|:-----------------:|
|    id    |    SERIAL   |    PRIMARY, UNIQUE, NOT NULL    |    ID    |
|    name    |    VARCHAR(255)    |    NOT NULL    |    ユーザー名    |
|    email    |    VARCHAR(255)    |    UNIQUE, NOT NULL    |    メアド    |
|    password    |    VARCHAR(255)    |    NOT NULL    |    パスワード    |
|    remenber_token    |    VARCHAR(100)    |    -    |    -    |
|    email_verified_at    |    TIMESTAMP    |    -    |    認証された時刻    |
|    created_at    |    TIMESTAMP    |    -    |    -    |
|    updated_at    |    TIMESTAMP    |    -    |    -    |


# URL設計

## API
|    URL    |    メソッド    |    認証    |    処理内容    |
|:------------------|:------------------|:------------------:|:-----------------|
|    /api/photos    |    GET   |    -    |    写真一覧取得    |
|    /api/photos    |    POST   |    🔑    |    写真投稿    |
|    /api/photos/{写真ID}    |    GET   |    -    |    写真詳細取得    |
|    /api/photos/{写真ID}/like    |    PUT   |    🔑    |    写真にいいね追加    |
|    /api/photos/{写真ID}/like    |    DELETE   |    🔑    |    写真にいいね削除    |
|    /api/photos/{写真ID}/comments    |    POST   |    🔑    |    写真にコメント追加    |
|    /api/register    |    POST   |    -    |    会員登録    |
|    /api/login    |    POST   |    -    |    ログイン    |
|    /api/logout    |    POST   |    🔑    |    ログアウト    |
|    /api/user    |    GET   |    🔑    |    認証ユーザー取得    |


## フロントエンド（Vue Routerでルーティング）

|    URL    |    メソッド    |    認証    |    処理内容    |
|:------------------|:------------------|:------------------:|:-----------------|
|    /    |    GET   |    -    |    写真一覧ページ    |
|    /photos/{写真ID}    |    GET   |    -    |    写真詳細ページ    |
|    /login    |    GET   |    -    |    ログインページ    |


## その他

|    URL    |    メソッド    |    認証    |    処理内容    |
|:------------------|:------------------|:------------------:|:-----------------|
|    /    |    GET   |    -    |    最初にHTML返却    |
|    /photos/{写真ID}/download    |    GET   |    -    |    写真のダウンロード    |

