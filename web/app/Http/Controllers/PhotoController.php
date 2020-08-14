<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Comment;
use App\Http\Requests\StorePhoto;
use App\Http\Requests\StoreComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要（写真一覧・ダウンロード・写真詳細APIは認証不要）
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }


    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名にする
        $photo->filename = $photo->id . '.' . $extension;

        // S3にファイルを保存
        // 第一引数はディレクトリの指定
        // 第二引数はファイル
        // 第三引数はファイル名
        // 第四引数の 'public' はファイルを公開状態で保存するため
        // Storage::cloud()のcloud()は
        // config/filesystems.php の cloud の設定にしたがって使用されるストレージがきまる
        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');

        // DBエラー時にファイル削除を行うため
        // トランザクション（ここからここまでの処理）を利用する
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        // リソースの新規作成なので
        // レスポンスコードはCREATEDの201を返却する
        return response($photo, 201);
    }

    /**
     * 写真一覧
     */
    public function index()
    {
        // withメソッド → リレーションを事前に読み込むことで、N+1問題を回避できる
        //  N+1 はじめの1回のSQLでModelを取得し、そのModelに対するデータ数分（N回）SQL叩く実行されること
        // paginateメソッド → ページ送り機能の追加
        //  JSONレスポンスで示したtotal（総ページ数）やcurrent_page（現在のページ）などの情報が自動追加
        //  2ページ目以降の写真一覧を取得したい場合はURLパラメータにpage=2を付ければok
        $photos = Photo::with(['owner'])->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        // コントローラーからモデルクラスのインスタンスをreturnすると、自動でJSONに変換されてレスポンスされる
        return $photos;
    }

    /**
     * 写真ダウンロード
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function download(Photo $photo)
    {
        // 写真の存在チェック
        if (! Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        // ダウンロードさせるために保存ダイアログを開くようにする
        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Dispostion' => $disposition,
        ];

        // S3から取得した画像ファイルをブラウザの保存ダイアログから開くようにしてダウンロードさせる
        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    /**
     * 写真詳細
     * @param string id
     * @return Photo
     */
    public function show(string $id)
    {
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author'])->first();

        // 写真データがない場合は404
        return $photo ?? abort(404);
    }

    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // authorリレーションをロードするためにコメントを取得し直す
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }


    /**
     * いいね機能
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        // 特定の写真およびログインユーザーに紐づくいいねを削除
        $photo->likes()->detach(Auth::user()->id);
        // 新たに特定の写真およびログインユーザーに紐づくいいねを追加
        $photo->likes()->attach(Auth::user()->id);

        return ['photo_id' => $id];
    }

    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ['photo_id' => $id];
    }
}
