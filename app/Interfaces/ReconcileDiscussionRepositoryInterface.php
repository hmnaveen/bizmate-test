<?php

namespace App\Interfaces;

interface ReconcileDiscussionRepositoryInterface
{
    public function create($discussions, $userId);
    public function index($userId, $paymentId);
    public function getDiscussionIds($userId, $transactionIds);

}
