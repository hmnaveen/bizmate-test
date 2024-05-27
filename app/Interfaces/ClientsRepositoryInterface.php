<?php 

namespace App\Interfaces;

interface ClientsRepositoryInterface 
{
    public function getClients($userId);
    public function createOrUpdateClient($client, $userId);
    // public function getTransactionsByFilter($filters, $isFilterOn);
    // public function getTransactionCount($userId);
}
?>