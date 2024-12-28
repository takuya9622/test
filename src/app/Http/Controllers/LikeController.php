<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggle($itemId) {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userId = Auth::id();

        $exists = DB::table('likes')
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->exists();

        if ($exists) {
            DB::table('likes')
                ->where('user_id', $userId)
                ->where('item_id', $itemId)
                ->delete();
        } else {
            DB::table('likes')->insert([
                'user_id' => $userId,
                'item_id' => $itemId,
                'created_at' => now(),
            ]);
        }

        return back();
    }
}
