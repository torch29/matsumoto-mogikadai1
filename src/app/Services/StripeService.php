<?php

namespace App\Services;

use Stripe\Checkout\Session;

class StripeService
{
    public function createCheckoutSession(array $data)
    {
        return Session::create($data);
    }
}
