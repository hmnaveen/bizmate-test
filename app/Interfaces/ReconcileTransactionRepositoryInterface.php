<?php 

namespace App\Interfaces;

interface ReconcileTransactionRepositoryInterface 
{
    public function reconcileTransaction($request, $userinfo);
    public function show($userId, $transactionId);
    public function unReconcileTransaction($request, $userinfo);
}
?>