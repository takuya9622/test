<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function testExhibitedItemFieldsAreStoredCorrectly(): void
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create();
        $categories = Category::take(3)->pluck('id')->toArray();

        $itemData = $this->getItemData($categories);

        $this->actingAs($user)
        ->get(route('items.create'))
        ->assertStatus(200);

        $this->actingAs($user)
        ->post(route('items.store'), $itemData, ['Content-Type' => 'multipart/form-data'])
        ->assertRedirect(route('items.index'));


        $this->assertItemStoredInDatabase($user->id, $itemData);
        $this->assertCategoriesLinkedToItem($categories, $itemData['title']);
    }

    private function getItemData(array $categories): array
    {
        return [
            'title' => 'Test Item',
            'brand_name' => 'Test Brand',
            'description' => 'Test Text',
            'price' => '55555',
            'condition' => '2',
            'category' => $categories,
            'item_image' => new UploadedFile(
                base_path('tests/Fixtures/test_image.jpg'),
                'test_image.jpg',
                'image/jpeg',
                null,
                true
            ),
        ];
    }

    private function assertItemStoredInDatabase(int $sellerId, array $itemData): void
    {
        $this->assertDatabaseHas('items', [
            'seller_id' => $sellerId,
            'title' => $itemData['title'],
            'brand_name' => $itemData['brand_name'],
            'description' => $itemData['description'],
            'price' => $itemData['price'],
            'condition' => $itemData['condition'],
            'item_image' => Item::where('title', $itemData['title'])->value('item_image'),
        ]);
    }

    private function assertCategoriesLinkedToItem(array $categories, string $itemTitle): void
    {
        $itemId = Item::where('title', $itemTitle)->value('id');
        foreach ($categories as $categoryId) {
            $this->assertDatabaseHas('category_item', [
                'category_id' => $categoryId,
                'item_id' => $itemId,
            ]);
        }
    }
}