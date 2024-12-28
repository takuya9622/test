<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public const STATUS_AVAILABLE = 1;
    public const STATUS_SOLD = 2;

    public const SALES_STATUSES = [
        self::STATUS_AVAILABLE => 'available',
        self::STATUS_SOLD => 'sold',
    ];

    public function getSalesStatusAttribute()
    {
        return self::SALES_STATUSES[$this->status] ?? '不明';
    }

    public function getConditionLabelAttribute() {
        $conditionLabels = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
        ];

        return $conditionLabels[$this->condition] ?? '不明';
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function isLikedBy($user) {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function likes() {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
