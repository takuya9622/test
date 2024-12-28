<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    public function definition()
    {
        $presetItem = [
            [
                'title' => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => '15000',
                'condition' => '1',
                'item_image' => 'items/1.jpg',
            ],
            [
                'title' => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => '5000',
                'condition' => '2',
                'item_image' => 'items/2.jpg',
            ],
            [
                'title' => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => '300',
                'condition' => '3',
                'item_image' => 'items/3.jpg',
            ],
            [
                'title' => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'price' => '4000',
                'condition' => '4',
                'item_image' => 'items/4.jpg',
            ],
            [
                'title' => 'ノートPC',
                'description' => '高性能なノートパソコン',
                'price' => '45000',
                'condition' => '1',
                'item_image' => 'items/5.jpg',
            ],
            [
                'title' => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'price' => '8000',
                'condition' => '2',
                'item_image' => 'items/6.jpg',
            ],
            [
                'title' => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'price' => '3500',
                'condition' => '3',
                'item_image' => 'items/7.jpg',
            ],
            [
                'title' => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'price' => '500',
                'condition' => '4',
                'item_image' => 'items/8.jpg',
            ],
            [
                'title' => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'price' => '4000',
                'condition' => '1',
                'item_image' => 'items/9.jpg',
            ],
            [
                'title' => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'price' => '2500',
                'condition' => '2',
                'item_image' => 'items/10.jpg',
            ],
        ];

        $selected = $this->faker->randomElement($presetItem);

        return array_merge($selected, [
            'seller_id' => User::inRandomOrder()->value('id'),
            'brand_name' => $this->faker->optional()->company,
            'status' => $this->faker->randomElement([1, 2]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
