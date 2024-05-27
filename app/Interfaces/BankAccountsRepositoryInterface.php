<?php 

namespace App\Interfaces;

interface BankAccountsRepositoryInterface 
{
    public function createOrUpdateBankAcount($basiqUserId, $account);
    public function show($basiqUserId, $accountId);
    public function showAll($userId);
    public function disableBankAccount($accountId, $basqUserId);
}
