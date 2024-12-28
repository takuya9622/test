<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LikeSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $items = Item::all();

        foreach ($users as $user) {
            $likedItems = $items->random(rand(0, 5));

            foreach ($likedItems as $item) {
                DB::table('likes')->insert([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
