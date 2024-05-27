<?php 

namespace App\Interfaces;

interface PaymentHistoryRepositoryInterface 
{
    public function destroy($invoiceId, $userId);
    public function store($paymentHistory);
}
?>