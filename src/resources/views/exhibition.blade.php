@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
<link rel="stylesheet" href="{{ asset('css/exhibition.css') }}" />
@endsection

@section('content')
<div class="container">
    <h1>商品の出品</h1>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell-form" novalidate>
        @csrf

        <div class="form-group">
            <h3 for="product_image" class="form-label">商品画像</h3>
            <div class="image-upload-button">
                <input type="file" name="item_image" id="item_image" class="form-control" hidden>
                <label for="item_image">画像を選択する</label>
            </div>
            @error('item_image') <span class="error">{{ $message }}</span> @enderror
        </div>

        <h2>商品の詳細</h2>

        <div class="form-group">
            <h3 for="category" class="form-label">カテゴリー</h3>
            <div class="categories">
                @foreach($categories as $id => $name)
                <input hidden type="checkbox" name="category[]" id="category_{{ $id }}" value="{{ $id }}" {{ old('category') && in_array($id, old('category')) ? 'checked' : '' }}>
                <label class="category-label" for="category_{{ $id }}">
                    <span>{{ $name }}</span>
                </label>
                @endforeach
            </div>
            @error('category') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <h3 for="condition" class="form-label">商品の状態</h3>
            <div class="select-wrapper">
                <select name="condition" id="condition" class="form-control form-controll-select">
                    <option value="" hidden selected>選択してください</option>
                    <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                    <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>使用感あり</option>
                    <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>損傷あり</option>
                </select>
            </div>
            @error('condition') <span class="error">{{ $message }}</span> @enderror
        </div>

        <h2>商品名と説明</h2>
        <div class="form-group">
            <h3 for="title" class="form-label">商品名</h3>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @error('title') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <h3 for="brand_name" class="form-label">ブランド名</h3>
            <input type="text" name="brand_name" id="brand_name" class="form-control" value="{{ old('brand_name') }}">
            @error('brand_name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <h3 for="description" class="form-label">商品の説明</h3>
            <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
            @error('description') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <h3 for="price" class="form-label">販売価格</h3>
            <div class="price-input">
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
            </div>
            @error('price') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="sending-form-button">出品する</button>
    </form>
</div>
@endsection