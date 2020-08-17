<?php

namespace Tests\Feature;

use App\User;
use App\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPhotoListApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {

        // 5つの写真データを生成する
        factory(Photo::class, 5)->create();

        // 生成した写真データを作成日降順で取得
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.userIndex', [
            'user_id' => $photo->user_id,
        ]));
        $response->assertStatus(200);
    }
}
