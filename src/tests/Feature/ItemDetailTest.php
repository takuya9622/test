<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testDetailPageShowsAllItemInformation(): void
    {
        $this->seed(CategorySeeder::class);

        [$item, $category, $comment, $buyer] = $this->createItemWithRelatedData();

        $response = $this->get(route('items.show', ['itemId' => $item->id]));

        $response->assertStatus(200);

        $expectedData = [
            $item->title,
            $item->brand_name,
            '¥' . number_format($item->price),
            $category->name,
            $item->likes()->count(),
            $buyer->name,
            $comment->comment,
            $comment->count(),
            $item->item_image,
            $item->condition,
            $item->description,
        ];

        foreach ($expectedData as $data) {
            $response->assertSee($data);
        }
    }

    private function createItemWithRelatedData(): array
    {
        $category = Category::first();

        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()
            ->for($seller, 'seller')
            ->create([
                'title' => 'Test Item',
                'brand_name' => 'Test Brand',
                'description' => 'This is a test item description.',
                'price' => 1000,
                'condition' => '1',
                'item_image' => 'test_image.jpg',
            ]);

        $item->categories()->attach($category->id);

        $comment = Comment::factory()
            ->for($item)
            ->for($buyer, 'user')
            ->create(['comment' => 'Test Comment']);

        $item->likes()->attach($buyer->id);

        return [$item, $category, $comment, $buyer];
    }

    public function testItemCanHaveMultipleCategoriesInItemDetail(): void
    {
        $this->seed(CategorySeeder::class);
        $categories = Category::take(5)->get();

        $seller = $this->createUserWithItems(1);
        $item = $seller->items->first();
        $item->categories()->attach($categories->pluck('id')->toArray());

        $response = $this->get(route('items.show', ['itemId' => $item->id]));
        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSeeText($category->name);
        }
    }
}