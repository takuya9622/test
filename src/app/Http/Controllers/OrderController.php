<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;

class OrderController extends Controller
{
    public function create(Request $request, $itemId) {

        $option = $request->query('option', 'unselected');
        $paymentMethod = [];

        if ($option === 'konbini') {
            $paymentMethod = ['konbini', 'コンビニ払い'];
        } elseif ($option === 'card') {
            $paymentMethod = ['card', 'カード払い'];
        }

        $item = Item::findOrFail($itemId);

        $user = auth()->user();

        $shippingAddress = session('shipping_address', []);

        return view('purchase', compact('item', 'user', 'shippingAddress', 'option', 'paymentMethod'));
    }

    public function store(PurchaseRequest $request, $itemId)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $stripeSession = $this->createStripeCheckoutSession($itemId, $validated, $user);


        return redirect($stripeSession->url);
    }

    private function createStripeCheckoutSession($itemId, $validated, $user)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $item = Item::findOrFail($itemId);

        return StripeSession::create([
            'payment_method_types' => [$validated['payment_method']],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->title,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $user->email,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'item_id' => $item->id,
                'buyer_id' => auth()->id(),
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'],
            ],
        ]);
        session()->forget('shipping_address');
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $sessionId = $request->get('session_id');
            if (!$sessionId) {
            return redirect()->route('profile.index', ['tab' => 'buy']);
            }

        $session = StripeSession::retrieve($sessionId);

        $metadata = $session->metadata;
        $itemId = $metadata->item_id;
        $user = auth()->user();
        // $validated = session('shipping_address');

        DB::transaction(function () use ($itemId, $user, $metadata) {
            Order::create([
                'item_id' => $itemId,
                'buyer_id' => $user->id,
                'shipping_postal_code' => $metadata->postal_code ?? $user->postal_code,
                'shipping_address' => $metadata->address ?? $user->address,
                'shipping_building' => $metadata->building ?? $user->building,
            ]);

            item::where('id', $itemId)->update(['status' => 2]);
        });


        session()->forget('shipping_address');

        return redirect()->route('profile.index', ['tab' => 'buy'])
            ->with('status', '購入が完了しました！');
    }

    public function cancel()
    {
        return redirect()->route('profile.index', ['tab' => 'buy'])
            ->with('status', '購入がキャンセルされました。');
    }
}