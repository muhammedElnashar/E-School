<?php

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (SignatureVerificationException $e) {
            Log::error("Stripe Webhook Signature Error: " . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // مثال للتعامل مع حدث نجاح الدفع
        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            $purchase = \App\Models\Purchase::where('stripe_payment_intent_id', $intent->id)->first();
            if ($purchase) {
                $purchase->status = 'completed';
                $purchase->activated_at = now();
                $purchase->remaining_credits = $purchase->marketplaceItem->lecture_credits ?? 0;
                $purchase->save();
            }
        }

        return response()->json(['status' => 'success']);
    }
}
