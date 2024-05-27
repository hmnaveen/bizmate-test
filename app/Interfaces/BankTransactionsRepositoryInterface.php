<?php

namespace App\Interfaces;

interface BankTransactionsRepositoryInterface
{
    public function createOrUpdateBankTransactions($accounts, $basiqUserId, $accountId, $nextUrl);
    public function getTransactionsByFilter($filter);
    public function showTransaction($bankTransactionId, $accountId);
    public function updateReconcileStatus($accountId, $bankTransactionId, $status);
}
?>
