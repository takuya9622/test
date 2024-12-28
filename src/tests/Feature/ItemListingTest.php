<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;
use Tests\Helpers\CreatesTestData;


class ItemListingTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testTopPageShowsAllItems(): void
    {
        Item::factory()
        ->for(User::factory(), 'seller')
        ->count(5)
        ->create();

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSeeText($item->title, $item->item_image);
        }
    }

    public function testAlreadyPurchasedItemHasSoldStatus(): void
    {
        $soldItems = Item::factory()
            ->for(User::factory(), 'seller')
            ->count(5)
            ->create(['status' => Item::STATUS_SOLD]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);

        foreach ($soldItems as $item) {
            $response->assertSeeText('Sold');
        }
    }

    public function testUserCannotSeeOwnItemsInList(): void
    {
        $user = $this->createUserWithItems(5);
        $otherUser = $this->createUserWithItems(5);

        $response = $this->actingAs($user)->get(route('items.index'));
        $response->assertStatus(200);

        $this->assertItemsVisibility($response, $user->items, false);
        $this->assertItemsVisibility($response, $otherUser->items, true);
    }
}
