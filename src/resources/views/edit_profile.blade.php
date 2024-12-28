@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/appearance_profile.css') }}" />
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
<link rel="stylesheet" href="{{ asset('css/edit_profile.css') }}" />
@endsection

@section('content')
<div>
    <div class="mypage-header-container">
        <div class="logo-container">
            <h1>プロフィール設定</h1>
        </div>
        <div class="profile-edit-container">
            <div class="profile-image">
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="プロフィール画像">
            </div>
            <div class="select-profile-image">
                <label for="profile_image" class="edit-profile-button select-image-button">画像を選択する</label>
                @error('profile_image')
                <div class="error error-image">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PATCH')

            <div class="form-group">
                <input type="file" name="profile_image" id="profile_image" hidden>
            </div>

            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input id="name" type="text" name="name" value="{{ $profile->name }}" required autofocus>
                @error('name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input id="postal_code" type="text" name="postal_code" value="{{ $profile->postal_code }}">
                @error('postal_code')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="address">住所</label>
                <input id="address" type="text" name="address" value="{{ $profile->address }}">
                @error('address')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="building">建物名</label>
                <input id="building" type="text" name="building" value="{{ $profile->building }}">
                @error('building')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="sending-form-button">更新する</button>
        </form>
    </div>
</div>
@endsection