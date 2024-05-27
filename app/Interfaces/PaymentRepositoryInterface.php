<?php

namespace App\Interfaces;

interface PaymentRepositoryInterface
{
//    public function show($userId);
    public function  create($referenceId, $payment, $invoiceExpensePayment);

}
