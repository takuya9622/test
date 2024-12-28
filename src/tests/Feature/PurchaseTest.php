<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;
use Mockery;
use Stripe\StripeClient;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStripeSession();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testPurchaseButtonCompletesPurchase(): void
    {
        $buyer = $this->createBuyerIncludingAddress();
        [$seller, $item] = $this->createSellerWithItem(1);


        $this->performPurchase($buyer, $item);

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'shipping_postal_code' => $buyer->postal_code,
            'shipping_address' => $buyer->address,
            'shipping_building' => $buyer->building,
        ]);
    }

    public function testPurchasedItemAppearsInProfilePurchasesList(): void
    {
        $buyer = $this->createBuyerIncludingAddress();
        [$seller, $item] = $this->createSellerWithItem(1);

        $this->performPurchase($buyer, $item);

        $this->assertDatabaseHas('items', [
            'status' => Item::STATUS_SOLD,
        ]);

        $response = $this->get(route('items.index'));
        $response->assertSeeInOrder([
            'data-item-id="' . $item->id . '"',
            'Sold',
        ], false);
    }

    public function testPurchasedItemDisplaysAsSoldInItemList(): void
    {
        $buyer = $this->createBuyerIncludingAddress();
        [$seller, $item] = $this->createSellerWithItem(1);

        $this->performPurchase($buyer, $item);

        $response = $this->get(route('profile.index', ['tab' => 'buy']));
        $response->assertSee('data-item-id="' . $item->id . '"', false);
    }
}
