<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    }
