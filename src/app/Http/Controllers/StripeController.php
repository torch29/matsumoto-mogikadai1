<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class StripeController extends Controller
{
    public function payment(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // 顧客情報をstripe側に登録
            $customer = Customer::create(
                array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken
                )
            );

            //dump($customer);
            //dump($customer->id);

            $charge = Charge::create(
                array(
                    'customer' => $customer->id,
                    'amount' => 100,
                    'currency' => 'jpy'
                )
            );

            //dump($charge);

            return redirect('/mypage?tab=buy');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
