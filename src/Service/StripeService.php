<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient($_ENV['STRIPE_SK']);
    }

    public function createCheckoutSession($event, $successUrl, $cancelUrl): Session
    {
        $priceInCents = (int) round($event->getPrice() * 100);

        return $this->stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $event->getTitle(),
                    ],
                    'unit_amount' => $priceInCents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

}
