<?php

namespace App\Http\Controllers;

use App\Contracts\Payments\PaymentService;
use App\Http\Resources\PaymentCollection;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaymentService $paymentService)
    {
        $payments = $paymentService->getMySentAndReceivedPayments();

        return PaymentCollection::make($payments);
    }
}
