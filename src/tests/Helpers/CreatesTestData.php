<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\Item;
use Mockery;
use Stripe\StripeClient;

trait CreatesTestData
{
    public function createUserWithItems(int $itemCount, int $status = Item::STATUS_AVAILABLE): User
    {
        $user = User::factory()->create();
        Item::factory()
            ->for($user, 'seller')
            ->count($itemCount)
            ->state(['status' => $status])
            ->create();

        $user->load('items');

        return $user;
    }

    public function createSellerWithItem(int $itemCount): array
    {
        $seller = $this->createUserWithItems($itemCount);
        $item = $seller->items->first();
        return [$seller, $item];
    }

    public function assertItemsVisibility($response, $items, bool $shouldBeVisible): void
    {
        foreach ($items as $item) {
            if ($shouldBeVisible) {
                $response->assertSee('data-item-id="' . $item->id . '"', false);
            } else {
                $response->assertDontSee('data-item-id="' . $item->id . '"', false);
            }
        }
    }

    public function createBuyerIncludingAddress(): User
    {
        return User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区テスト町1-1-1',
            'building' => 'テストビル101',
        ]);
    }

    protected function mockStripeSession(): void
    {
        $mockStripeSession = Mockery::mock('overload:Stripe\Checkout\Session');

        $mockStripeSession->shouldReceive('retrieve')
            ->with('fake_session_id')
            ->andReturn((object)[
                'metadata' => (object)[
                    'item_id' => 1,
                    'buyer_id' => 1,
                    'postal_code' => '123-4567',
                    'address' => '東京都新宿区テスト町1-1-1',
                    'building' => 'テストビル101',
                ],
            ]);

        $mockStripeSession->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'id' => 'fake_session_id',
                'url' => 'https://fake-stripe-checkout-url.com',
            ]);

        app()->instance(StripeClient::class, $mockStripeSession);
    }

    public function performPurchase(User $buyer, $item, bool $hasShippingAddress = false): void
    {
        $response = $this->actingAs($buyer)->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        $response = $this->get(route('purchase.create', ['itemId' => $item->id, 'option' => 'card']));
        $response->assertStatus(200);

        $expectedShippingAddress = $hasShippingAddress
        ? session('shipping_address')
        : [];
        $response->assertViewHas('user', $buyer);
        $response->assertViewHas('shippingAddress', $expectedShippingAddress);

        $response = $this->post(route('order.store', ['itemId' => $item->id]), [
            'payment_method' => 'card',
            'postal_code' => $buyer->postal_code,
            'address' => $buyer->address,
            'building' => $buyer->building,
        ]);
        $response->assertRedirect('https://fake-stripe-checkout-url.com');

        $response = $this->get(route('checkout.success', ['session_id' => 'fake_session_id']));
        $response->assertRedirect(route('profile.index', ['tab' => 'buy']));
    }
}
