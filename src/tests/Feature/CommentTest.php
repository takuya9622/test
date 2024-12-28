<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testAuthenticatedUserCanSendComment(): void
    {
        $user = $this->createUserWithItems(1);
        $item = $user->items->first();
        $commentText = 'test comment';

        $response = $this->actingAs($user)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $initialCommentCount = $item->comments()->count();
        $this->assertEquals(0, $initialCommentCount);

        $this->post(route('comments.store', ['itemId' => $item->id]), [
            'comment' => $commentText,
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);

        $response = $this->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $newCommentCount = $item->comments()->count();
        $this->assertEquals($initialCommentCount + 1, $newCommentCount);

        $response->assertSeeInOrder([
            '<a href="#comments" class="comment-count">',
            $newCommentCount,
        ], false);

        $response->assertSeeInOrder([
            '<h2>コメント (',
            $newCommentCount,
            ')</h2>',
        ], false);

        $response->assertSee($commentText);
    }

    public function testGuestCanNotSendCommentTest(): void
    {
        User::factory()->create();
        $item = Item::factory()
            ->create();
        $commentText = 'test comment';

        $response = $this->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $this->post(route('comments.store', ['itemId' => $item->id]), [
            'comment' => $commentText,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);
    }

    public function testSendFailWhenCommentIsEmpty(): void
    {
        $user = $this->createUserWithItems(1);
        $item = $user->items->first();
        $commentText = '';

        $response = $this->actingAs($user)
            ->from(route('items.show', ['itemId' => $item->id]))
            ->post(route('comments.store', ['itemId' => $item->id]), [
                'comment' => $commentText,
            ]);

        $response->assertRedirect(route('items.show', ['itemId' => $item->id]));

        $response->assertSessionHasErrors(['comment' => 'コメントを入力してください']);

        $this->get(route('items.show', ['itemId' => $item->id]))
            ->assertSeeText('コメントを入力してください');
    }

    public function testSendFailWhenCommentIsTooManyCharacters(): void
    {
        $user = $this->createUserWithItems(1);
        $item = $user->items->first();
        $commentText = str_repeat('a', 256);;

        $response = $this->actingAs($user)
            ->from(route('items.show', ['itemId' => $item->id]))
            ->post(route('comments.store', ['itemId' => $item->id]), [
                'comment' => $commentText,
            ]);

        $response->assertRedirect(route('items.show', ['itemId' => $item->id]));

        $response->assertSessionHasErrors(['comment' => '255文字以内で入力してください']);

        $this->get(route('items.show', ['itemId' => $item->id]))
            ->assertSeeText('255文字以内で入力してください');
    }
}