<?php

namespace App\Repositories;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use App\Models\PaymentTransactionCollectionPayment;
use App\Models\TransactionCollectionPayment;
use DB;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $paymentModel){
        $this->paymentModel = $paymentModel;
    }

    public function create($referenceId, $payment, $invoiceExpensePayment)
    {
        try {
            $payment = $this->paymentModel::create($payment);
            if ($payment) {
//                if (!empty($invoiceExpensePayment)) {
                    $response = collect($invoiceExpensePayment)
                        ->map(function (array $transaction) use ($payment) {
                            $transaction['payment_id'] = $payment->id;
                            return $transaction;
                        })
                        ->each(function ($chunk) {
                            TransactionCollectionPayment::create($chunk);
                        });
                    return $payment;
//                }
            }
        }catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
