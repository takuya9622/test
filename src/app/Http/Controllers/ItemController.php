<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request) {
        $tab = $request->query('tab', 'recommend');
        $search = $request->query('search');
        $query = Item::query();
        $query->where('seller_id', '!=', auth()->id());

        if ($tab === 'mylist') {
            $query->whereIn('id', function ($query) {
                $query->select('item_id')
                    ->from('likes')
                    ->where('user_id', auth()->id());
            });
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $items = $query->get();
        return view('index', compact('items', 'tab', 'search'));
    }

    public function show($itemId) {
        $item = Item::with(['comments.user', 'categories', 'likes'])
            ->withCount('likes', 'comments')
            ->findOrFail($itemId);

        $isLiked = Auth::check() ? $item->isLikedBy(Auth::user()) : false;

        $isAvailable = $item->sales_status === 'available';

        $isOwner = Auth::check() && $item->seller_id === Auth::id();

        return view('detail', [
            'item' => $item,
            'comments' => $item->comments,
            'isLiked' => $isLiked,
            'isAvailable' => $isAvailable,
            'isOwner' => $isOwner,
            'user' => Auth::user(),
        ]);
    }

    }
