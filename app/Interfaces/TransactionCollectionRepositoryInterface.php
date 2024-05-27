<?php 

namespace App\Interfaces;

interface TransactionCollectionRepositoryInterface 
{
    public function getTransaction($filters, $userId);
    public function createOrUpdateTransaction($invoice_details, $transactions, $userId, $invoiceId, $type);
    public function getTransactionsByFilter($filters, $isFilterOn);
    public function getTransactionCount($userId);
    public function updateInvoiceStatus($invoiceId, $userInfo, $status, $invoiceHistory);
    public function updateInvoicePaymentStatus($invoiceId, $userinfo, $invoiceStatus, $paymentHistory, $invoiceHistory, $isDeleted);
    public function getTransactionsByStatus($userId, $status, $bankAccountId);
    public function showReconcileCalculation($userId, $transactionId, $transactionAmount);
    public function destroy($userInfo, $transactionId, $status);
    public function reCallInvoice($userInfo, $transactionId);
    public function matchTransactions($filter, $userId);
    public function accountTransactions($userId, $filter);
    public function showReconcileTransaction($userId, $filter);
}
