<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\StripeClient;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;

class ChangeAddressTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUpdatedShippingAddressIsDisplayedOnPurchasePage(): void
    {
        $buyer = $this->createBuyerIncludingAddress();
        [$seller, $item] = $this->createSellerWithItem(1);

        $updatedAddressData = [
            'postal_code' => '000-0000',
            'address' => '大阪府大阪市テスト区1-1-1',
            'building' => 'テストマンション101',
        ];

        $this->actingAs($buyer)
            ->get(route('purchase.create', ['itemId' => $item->id]))
            ->assertStatus(200);

        $this->get(route('address.edit', ['itemId' => $item->id]))
            ->assertStatus(200);

        $response = $this->post(route('address.update', ['itemId' => $item->id]), $updatedAddressData)
        ->assertRedirect(route('purchase.create', ['itemId' => $item->id]));

        $this->assertEquals(session('shipping_address'), $updatedAddressData);

        $response = $this->get(route('purchase.create', ['itemId' => $item->id]))
            ->assertStatus(200);
        foreach ($updatedAddressData as $value) {
            $response->assertSee($value, false);
        }
    }

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

    public function testPurchasedItemIsRegisteredWithUpdatedShippingAddress(): void
    {
        $buyer = $this->createBuyerIncludingAddress();
        [$seller, $item] = $this->createSellerWithItem(1);

        $updatedAddressData = [
            'postal_code' => '000-0000',
            'address' => '大阪府大阪市テスト区1-1-1',
            'building' => 'テストマンション101',
        ];

        $this->actingAs($buyer)
            ->get(route('purchase.create', ['itemId' => $item->id]))
            ->assertStatus(200);

        $this->get(route('address.edit', ['itemId' => $item->id]))
            ->assertStatus(200);

        $response = $this->post(route('address.update', ['itemId' => $item->id]), $updatedAddressData)
        ->assertRedirect(route('purchase.create', ['itemId' => $item->id]));

        $this->assertEquals(session('shipping_address'), $updatedAddressData);

        $response = $this->get(route('purchase.create', ['itemId' => $item->id]))
            ->assertStatus(200);
        foreach ($updatedAddressData as $value) {
            $response->assertSee($value, false);
        }

        $this->performPurchase($buyer, $item, true);

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'shipping_postal_code' => $buyer->postal_code,
            'shipping_address' => $buyer->address,
            'shipping_building' => $buyer->building,
        ]);
    }
}
