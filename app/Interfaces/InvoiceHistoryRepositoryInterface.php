<?php 

namespace App\Interfaces;

interface InvoiceHistoryRepositoryInterface 
{
    public function index($userId, $transactionId);
    public function store($invoiceHistory);
    
}
?>