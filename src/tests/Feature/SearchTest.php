<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Tests\Helpers\CreatesTestData;

class SearchTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanSearchItemsByPartialName(): void
    {
        $seller = User::factory()->create();

        $searchText = 'Test';

        Item::factory(3)->for($seller, 'seller')->createMany([
            ['title' => 'Test Item'],
            ['title' => 'Example Item'],
            ['title' => 'Test Product'],
        ]);

        $response = $this->get(route('items.index', ['search' => $searchText]));
        $response->assertStatus(200);


        $response->assertSee('Test Item', false);
        $response->assertSee('Test Product', false);

        $response->assertDontSee('Example Item', false);
    }

    public function testUserCanSearchItemsByPartialNameInLikesTab(): void
    {
        $searcher = User::factory()->create();
        $seller = User::factory()->create();

        $testTarget = Item::factory()->for($seller, 'seller')->create(['title' => 'Test Item']);
        $otherItems = Item::factory()->for($seller, 'seller')->createMany([
            ['title' => 'Example Item'],
            ['title' => 'Test Product'],
        ]);

        DB::table('likes')->insert([
            'user_id' => $searcher->id,
            'item_id' => $testTarget->id,
            'created_at' => now(),
        ]);

        $searchText = 'Test';

        $response = $this->actingAs($searcher)
            ->get(route('items.index', ['tab' => 'mylist', 'search' => $searchText]));
        $response->assertStatus(200);

        $response->assertSee($testTarget->title, false);

        $otherItems->each(function ($item) use ($response) {
            $response->assertDontSee($item->title, false);
        });
    }
}