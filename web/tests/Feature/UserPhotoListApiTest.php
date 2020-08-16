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

        factory(Photo::class)->create();
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.userIndex', [
            'user_id' => $photo->user_id,
        ]));

        $response->assertStatus(200);
        // dd($response);
    }
}
