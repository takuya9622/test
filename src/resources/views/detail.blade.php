@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endsection

@section('content')
<div class="item_detail-container">
    <div class="item-image">
        <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
        @if (!$isAvailable)
        <p class="item-status">Sold</p>
        @endif
    </div>

    <div class="item-info">
        <h1 class="item-info_title">{{ $item->title }}</h1>

        <p class="brand_name">
            @if($item->brand_name)
            {{ $item->brand_name }}
            @endif
        </p>

        <p class="price">¥{{ number_format($item->price) }} <span>(税込)</span></p>

        <div class="actions">
            <form action="{{ route('likes.toggle', $item->id) }}" method="POST" class="like-form">
                @csrf
                <button type="submit" class="like-button">
                    <span class="material-icons like-icon">
                        {{ $isLiked ? 'star' : 'star_border' }}
                    </span>
                    <span class="like-count">{{ $item->likes_count }}</span>
                </button>
            </form>
            <a href="#comments" class="comment-count">
                <span class="material-icons comment-icon">chat_bubble_outline</span>
                {{ $item->comments_count }}
            </a>
        </div>

        @if ($isAvailable && !$isOwner)
        <a class="purchase-button procedure" href="{{ route('purchase.create', $item->id) }}">購入手続きへ</a>
        @elseif($isAvailable && $isOwner)
        <p class="purchase-button owner">出品中の商品</p>
        @else
        <p class="purchase-button sold">売り切れました</p>
        @endif

        <h2>商品説明</h2>
        <p>{{ $item->description }}</p>

        <h2>商品の情報</h2>

        <div>
            <div class="item-data-container">
                <h3 class="item-data">カテゴリー</h3>
                <p class="item-data-contents">
                    @foreach ($item->categories as $category)
                    <span class="item-data-contents-category">{{ $category->name }}</span>
                    @endforeach
                </p>
            </div>
            <div class="item-data-container">
                <h3 class="item-data">商品の状態</h3>
                <p class="item-data-contents condition">{{ $item->condition_label }}</p>
            </div>
        </div>

        <div class="comments-section">
            <h2>コメント ({{ $item->comments_count }})</h2>

            @foreach ($comments as $comment)
            <div class="comment">
                <div class="comment-author-container">
                    <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="{{ $comment->user->name }}">
                    <p class="comment-author">{{ $comment->user->name }}</p>
                </div>
                <div class="comment-content">
                    <p class="comment-body">{{ $comment->comment }}</p>
                </div>
            </div>
            @endforeach
            <div class="comment">
                <div class="comment-author-container">
                    <img src="{{ asset(auth()->check() && auth()->user()->profile_image ? 'storage/' . auth()->user()->profile_image : 'storage/users/default.png') }}" alt="{{ auth()->check() ? auth()->user()->name : 'ゲスト' }}">
                    <p class="comment-author">{{ auth()->check() ? auth()->user()->name : 'ゲスト' }}</p>
                </div>
                <div class="comment-content">
                    <p class="comment-body">こちらにコメントが入ります。</p>
                </div>
            </div>

            <h3 class="comments-header" id="comments">商品へのコメント</h3>
            @error('comment')
            <div class="error">{{ $message }}</div>
            @enderror
            <form action="{{ route('comments.store', ['itemId' => $item->id]) }}" method="POST" novalidate>
                @csrf
                <textarea name="comment" class="comment-text-area" rows="10" required></textarea>
                <button type="submit" class="comment-submit procedure">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection