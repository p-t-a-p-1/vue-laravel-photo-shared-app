<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp;

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ファイルをアップロードできる()
    {
        // S3ではなくテスト用のストレージを使用する
        // → storage/framework/testing
        Storage::fake('s3')

        $response = $this->actingAs($this->user)->json('POST', route('photo.create'), [
            // ダミーファイルを作成して送信している
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        // レスポンスが201(CREATED)であるかチェック
        $response->assertStatus(201);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であるかチェック
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // DBに挿入されたファイル名のファイルがストレージに保存されているかチェック
        Storage::cloud()->assertExists($photo->filename);
    }


    /**
     * @test
     */
    public function should_データベースエラーの場合はファイルを保存しない()
    {
        // エラーを起こす
        Schema::drop('photos');

        // テスト用ストレージ
        Storage::fake('s3');

        $response = $this->actingAs($this->user)->json('POST', route('photo.create'), [
            // ダミーファイルを作成して送信している
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        // レスポンスがINTERNAL_SERVER_ERRORになるかチェック
        $response->assertStatus(500);

        // ストレージにファイルが保存されていないかチェック
        $this->assertEquals(0, count(Storage::cloud()->files()));
    }


    /**
     * @test
     */
    public function should_ファイル保存エラーの場合はDBへの挿入はしない()
    {

        // 保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')->once()->andReturnNull();

        $response = $this->actingAs($this->user)->json('POST', route('photo.create'), [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        // レスポンスがINTERNAL_SERVER_ERRORになるかチェック
        $response->assertStatus(500);

        // DBに何も入ってないことをチェック
        $this->assertEmpty(Photo::all());
    }
}
