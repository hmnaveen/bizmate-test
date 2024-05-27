<?php 

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function show($userId);
    public function  findBasiqUserId($basiqUserId);
    
}
