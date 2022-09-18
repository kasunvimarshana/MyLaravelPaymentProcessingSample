<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaypalService;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Obtain a payment details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pay(Request $request)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id']
        ];
        $request->validate($rules);

        $paymentPlatform = resolve(PaypalService::class);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval(Request $request){
        $paymentPlatform = resolve(PaypalService::class);

        return $paymentPlatform->handleApproval();
    }

    public function cancelled(Request $request){
        //
    }
}
