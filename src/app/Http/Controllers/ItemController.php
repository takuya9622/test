<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
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

    public function create()
    {
        $categories = Category::pluck('name', 'id')->toArray();

        return view('exhibition', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $item = new Item();
        $item->title = $request->input('title');
        $item->description = $request->input('description');
        $item->price = $request->input('price');
        $item->condition = $request->input('condition');
        $item->brand_name = $request->input('brand_name');
        $item->status = Item::STATUS_AVAILABLE;
        $item->seller_id = Auth::id();

        if ($request->hasFile('item_image')) {
            $item->item_image = $request->file('item_image')->store('items', 'public');
        }

        $item->save();

        $categories = $request->input('category');
        if ($categories) {
            $item->categories()->attach($categories);
        }


        return redirect()->route('items.index');
    }
}
