<?php

namespace Tests\Feature;

use App\Photo;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {
        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id,
        ]));

        // assertJsonFragmentでJSONのフォーマットをチェック
        $response->assertStatus(200)->assertJsonFragment([
            'id' => $photo->id,
            'url' => $photo->url,
            'owner' => [
                'name' => $photo->owner->name,
            ],
            'comments' => $photo->comments
                ->sortByDesc('id')
                ->map(function ($comment){
                    return [
                        'author' => [
                            'name' => $comment->author->name
                        ],
                        'content' => $comment->content,
                    ];
                })
                ->all(),
        ]);
    }
}
