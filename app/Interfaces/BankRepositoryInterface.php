<?php 

namespace App\Interfaces;

interface BankRepositoryInterface 
{
    public function createOrUpdateBankAcounts($accounts);
    public function getTransactionsByFilter($filter, $userId);
    public function createOrUpdateBankTransactions($transactions, $bankUserId, $userId);
    public function showBankTransactions($userId);
    public function showBankAccounts($userId);
    public function showTransaction($bankTransactionId, $accountId, $userId);
    public function archiveBankAccount($accountId, $userId);
}
?>