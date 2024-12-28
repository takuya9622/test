<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;
use Illuminate\Support\Facades\DB;

class MyListTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testLikesTabShowsOnlyLikedItems(): void
    {
        $user = $this->createUserWithItems(5);
        $otherUser = $this->createUserWithItems(5);

        foreach ($otherUser->items as $item) {
            DB::table('likes')->insert([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'created_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));
        $response->assertStatus(200);

        $this->assertItemsVisibility($response, $otherUser->items, true);
        $this->assertItemsVisibility($response, $user->items, false);
    }

    public function testAlreadyPurchasedItemHasSoldStatusInLikesTab(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $otherUser = $this->createUserWithItems(5, Item::STATUS_SOLD);

        foreach ($otherUser->items as $item) {
            DB::table('likes')->insert([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'created_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));
        $response->assertStatus(200);

        $this->assertItemsVisibility($response, $otherUser->items, true);
        foreach ($otherUser->items as $item) {
            $response->assertSeeText('Sold');
        }
    }

    public function testUserCannotSeeOwnItemsInMyList(): void
    {
        $ownUser = $this->createUserWithItems(5);
        $otherUser = $this->createUserWithItems(5);

        $allItems = $ownUser->items->concat($otherUser->items);

        foreach ($allItems as $item) {
            DB::table('likes')->insert([
                'user_id' => $ownUser->id,
                'item_id' => $item->id,
                'created_at' => now(),
            ]);
        }

        $response = $this->actingAs($ownUser)->get(route('items.index', ['tab' => 'mylist']));
        $response->assertStatus(200);

        $this->assertItemsVisibility($response, $ownUser->items, false);
        $this->assertItemsVisibility($response, $otherUser->items, true);
    }

    public function testFavoritesTabShowsNothingForGuests(): void
    {
        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertStatus(200);

        $response->assertDontSee('data-item-id=', false);
        $response->assertSee('表示する商品がありません。');
    }
}
