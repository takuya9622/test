@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
<nav class="items-tab">
    <ul class="tabs">
        <li class="{{ $tab === 'recommend' ? 'active-tab' : '' }}">
            <a href="{{ route('items.index', ['tab' => 'recommend', 'search' => $search]) }}">
                おすすめ
            </a>
        </li>
        <li class="{{ $tab === 'mylist' ? 'active-tab' : '' }}">
            <a href="{{ route('items.index', ['tab' => 'mylist', 'search' => $search]) }}">
                マイリスト
            </a>
        </li>
    </ul>
</nav>

<div class="items-container">
    @forelse ($items as $item)
    <div class="item" data-item-id="{{ $item->id }}">
        <a href="{{ route('items.show', $item->id) }}" class="item-link">
            <div class="item-image">
                <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->title }}">
            </div>
            <p class="item-name">{{ $item->title }}</p>
            @if ($item->sales_status == 'sold')
            <p class="item-status">Sold</p>
            @endif
        </a>
    </div>
    @empty
    <p>表示する商品がありません。</p>
    @endforelse
</div>
@endsection