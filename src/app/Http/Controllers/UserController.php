<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

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

    }
