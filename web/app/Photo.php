<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{

    // 主キーの型
    // 初期設定から変更する場合は $keyType を上書きする
    protected $keyType = 'string';

    /**
     * 今回は id, url, owner
     */


    // JSONに含める属性をカスタマイズ
    protected $appends = [
        'url',
    ];

    // JSONに含める属性
    protected $visible = [
        'id',
        'owner',
        'url',
    ];

    // JSONに含めない属性
    // protected $hidden = [
    //     'user_id',
    //     'filename',
    //     self::CREATED_AT,
    //     self::UPDATED_AT,
    // ];

    protected $perPage = 1;

    // IDの桁数
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }


    /**
     * ランダムなID値を生成する
     * @return string
     */
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    /**
     * アクセサ（インスタンス外からメソッドを利用してメンバ変数・属性を取得） - url
     * @return string
     */
    public function getUrlAttribute()
    {
        // クラウドストレージのurlメソッドはS3上のファイルの公開URLを返す
        // .envで定義したAWS_URL + 引数のファイル名
        return Storage::cloud()->url($this->attributes['filename']);
    }
}
