<?php 

namespace App\Interfaces;

interface InvoiceItemRepositoryInterface 
{
    public function index($userId);
    public function create($item);
    public function show($userId, $id);
}
?>