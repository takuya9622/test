<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request) {
        $tab = $request->query('tab', 'sell');

        $query = Item::query();

        if ($tab === 'sell') {
            $query->whereIn('id', function ($query) {
                $query->select('id')
                    ->from('items')
                    ->where('seller_id', auth()->id());
            });
        }

        if ($tab === 'buy') {
            $query->whereIn('id', function ($query) {
                $query->select('item_id')
                    ->from('orders')
                    ->where('buyer_id', auth()->id());
            });
        }

        $items = $query->get();

        return view('profile', compact('items', 'tab'));
    }

    public function editProfile() {
        $profile = auth()->user();

        return view('edit_profile', compact('profile'));
    }

    public function updateProfile(ProfileRequest $request) {
        $user = auth()->user();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && $user->profile_image !== 'users/default.png') {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('users', 'public');
            $user->profile_image = $path;
        }

        $user->name = $request->input('name');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'プロフィールを更新しました！');
    }
}
