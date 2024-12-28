<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testProfileDisplaysRequiredData(): void
    {
        $testUser = $this->createUserWithItems(3);
        $exhibitedItems = $testUser->items;

        $otherUser = $this->createUserWithItems(3, Item::STATUS_SOLD);
        $purchasedItems = $otherUser->items;

        foreach ($purchasedItems as $item) {
            DB::table('orders')->insert([
                'item_id' => $item->id,
                'buyer_id' => $testUser->id,
                'shipping_postal_code' => '123-4567',
                'shipping_address' => '東京都新宿区テスト町1-1-1',
                'shipping_building' => 'テストビル101',
                'created_at' => now(),
            ]);
        }

        $this->checkTabVisibility(
            route('profile.index', ['tab' => 'sell']),
            $testUser,
            $exhibitedItems,
            $purchasedItems,
            'sell'
        );

        $this->checkTabVisibility(
            route('profile.index', ['tab' => 'buy']),
            $testUser,
            $purchasedItems,
            $exhibitedItems,
            'buy'
        );
    }

    private function checkTabVisibility(
        string $url,
        User $user,
        $visibleItems,
        $invisibleItems,
        string $tab
    ): void {
        $response = $this->actingAs($user)->get($url);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->profile_image);

        $this->assertItemsVisibility($response, $visibleItems, true);
        $this->assertItemsVisibility($response, $invisibleItems, false);
    }


    public function testProfileChangeInitialValues(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'profile_image' => 'test_image.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区テスト町1-1-1',
            'building' => 'テストビル101',
        ]);
        $expectedStrings = [
            $user->name,
            $user->profile_image,
            $user->postal_code,
            $user->address,
            $user->building,
        ];

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        foreach ($expectedStrings as $string) {
            $response->assertSee($string);
        }
    }
}
