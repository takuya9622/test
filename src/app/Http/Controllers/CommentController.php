<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        Comment::create([
            'comment' => $request->input('comment'),
            'item_id' => $itemId,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('status', 'コメントを投稿しました！');
    }
}
