<?php

namespace App\Http\Controllers;

use App\Contracts\Payments\PaymentRepository;
use App\Contracts\Payments\PaymentService;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentCollection;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Response;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request, PaymentRepository $paymentRepository, PaymentService $paymentService)
    {
        $paymentId = $paymentService->pay(
            $request->amount,
            $request->message,
            $request->payee_id,
        );

        $payment = $paymentRepository->findOrFail($paymentId, ['payee', 'payer']);

        return response(new PaymentResource($payment), Response::HTTP_CREATED);
    }
}
