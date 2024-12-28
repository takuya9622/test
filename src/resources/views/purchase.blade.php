@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <form action="{{ route('order.store', ['itemId' => $item->id]) }}" method="POST" novalidate>
        @csrf
        <div class="purchase-details">
            <div class="product-info">
                <div class="product-image">
                    <img src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
                </div>
                <div class="product-details">
                    <h2>{{ $item->title }}</h2>
                    <p class="product-price">¥{{ $item->price }}</p>
                </div>
            </div>

            <div class="payment-method">
                <h3>支払い方法</h3>

                <div class="custom-select-container" tabindex="0">
                    <div class="custom-select-label">{{ $paymentMethod[1] ?? '選択してください' }}</div>

                    <div class="custom-select-options">
                        <li class="custom-option">
                            <a href="{{ route('purchase.create', ['itemId' => $item->id, 'option' => 'konbini']) }}">
                                コンビニ払い
                            </a>
                        </li>
                        <li class="custom-option">
                            <a href="{{ route('purchase.create', ['itemId' => $item->id, 'option' => 'card']) }}">
                                カード払い
                            </a>
                        </li>
                    </div>
                </div>

                @error('payment_method')
                <div class="error for-payment">{{ $message }}</div>
                @enderror
            </div>

            <div class="shipping-info-container">
                <h3>配送先</h3>
                <a href="{{ route('address.edit', ['itemId' => $item->id]) }}" class="change-address">変更する</a>
                <div class="shipping-info">
                    <div class="postal-code">
                        <span>〒</span>
                        <input class="input-as-text"
                            type="text"
                            name="postal_code"
                            value="{{ session('shipping_address.postal_code') ?? $user->postal_code }}"
                            placeholder="未設定"
                            readonly>
                    </div>
                    @error('postal_code')
                    <div class="error">{{ $message }}</div>
                    @enderror

                    <input class="input-as-text"
                        type="text"
                        name="address"
                        value="{{ session('shipping_address.address') ?? $user->address }}"
                        placeholder="未設定"
                        readonly>
                    @error('address')
                    <div class="error">{{ $message }}</div>
                    @enderror

                    <input class="input-as-text"
                        type="text"
                        name="building"
                        value="{{ session('shipping_address.building') ?? $user->building }}"
                        placeholder="未設定"
                        readonly>
                    @error('building')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="purchase-summary">
            <table>
                <tr>
                    <td>商品代金</td>
                    <td>¥{{ $item->price }}</td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-payment-method">
                        <input type="text" name="payment_method" value="{{ $paymentMethod[0] ?? '' }}" hidden required>
                        {{ $paymentMethod[1] ?? '選択してください' }}
                    </td>
                </tr>
            </table>
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </form>
</div>
@endsection