<?php

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'marketplace_item_id' => 'required|exists:marketplace_items,id',
        ]);

        $student = auth()->user();
        $item = MarketplaceItem::find($request->marketplace_item_id);

        Stripe::setApiKey(config('stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $item->price * 100, // Stripe expects amount in cents
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $student->id,
                'marketplace_item_id' => $item->id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],

        ]);

        $purchase = Purchase::create([
            'user_id' => $student->id,
            'marketplace_item_id' => $item->id,
            'price' => $item->price,
            'status' => 'pending',
            'stripe_payment_intent_id' => $paymentIntent->id,
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }}
