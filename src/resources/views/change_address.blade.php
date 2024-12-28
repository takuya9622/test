@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
@endsection

@section('content')
<div>
    <div class="form-container">
        <h1>住所の変更</h1>
        <form method="POST" action="{{ route('address.update', ['itemId' => $item->id]) }}" novalidate>
            @csrf
            <div class="form-group">
                <div class="form-group_label-and-error">
                    <label for="postal_code">郵便番号</label>
                    @error('postal_code')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <input id="postal_code" type="text" name="postal_code" value="{{ $user->postal_code }}" required autofocus>
            </div>
            <div class="form-group">
                <div class="form-group_label-and-error">
                    <label for="address">住所</label>
                    @error('address')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <input id="address" type="text" name="address" value="{{ $user->address }}" required>
            </div>
            <div class="form-group">
                <div class="form-group_label-and-error">
                    <label for="building">建物名</label>
                    @error('building')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <input id="building" type="text" name="building" value="{{ $user->building }}" required>
            </div>
            <button type="submit" class="sending-form-button">更新する</button>
        </form>
    </div>
</div>
@endsection