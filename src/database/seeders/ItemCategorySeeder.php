<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class ItemCategorySeeder extends Seeder
{
    public function run()
    {
        $items = Item::all();
        $categories = Category::all();

        foreach ($items as $item) {
            $item->categories()->attach(
                $categories->random(rand(1, 5))->pluck('id')->toArray()
            );
        }
    }
}
