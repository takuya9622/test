<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;

class LikeTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanAddItemToLike(): void
    {
        [$user, $item] = $this->createUserWithLikedItem();

        $response = $this->actingAs($user)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $this->assertLikeCount($item, 0);

        $this->toggleLike($user, $item);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertLikeCount($item, 1);
        $this->assertItemPageContainsLikeCount($item, 1);
    }

    public function testLikeIconChangesColorWhenPressed(): void
    {
        [$user, $item] = $this->createUserWithLikedItem(true);

        $response = $this->actingAs($user)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertSeeText('star');
        $response->assertDontSeeText('star_border');

        $this->toggleLike($user, $item);

        $response = $this->actingAs($user)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertSeeText('star_border');
    }

    public function testUserCanSubtractItemToLike(): void
    {
        [$user, $item] = $this->createUserWithLikedItem(true);

        $response = $this->actingAs($user)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $this->assertLikeCount($item, 1);

        $this->toggleLike($user, $item);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertLikeCount($item, 0);
        $this->assertItemPageContainsLikeCount($item, 0);
    }

    private function createUserWithLikedItem(bool $isLiked = false): array
    {
        $user = $this->createUserWithItems(1);
        $item = $user->items->first();

        if ($isLiked) {
            $item->likes()->attach($user->id);
            $this->assertDatabaseHas('likes', [
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }

        return [$user, $item];
    }

    private function toggleLike($user, $item): void
    {
        $this->actingAs($user)
            ->post(route('likes.toggle', ['itemId' => $item->id]))
            ->assertRedirect();
    }

    private function assertLikeCount($item, int $expectedCount): void
    {
        $currentCount = $item->likes()->count();
        $this->assertEquals($expectedCount, $currentCount);
    }

    private function assertItemPageContainsLikeCount($item, int $expectedCount): void
    {
        $response = $this->get(route('items.show', ['itemId' => $item->id]));
        $response->assertSee($expectedCount);
    }
}