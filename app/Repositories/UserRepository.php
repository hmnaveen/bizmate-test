<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\SumbUsers;
use DB;

class UserRepository implements UserRepositoryInterface 
{
    public function __construct(SumbUsers $userModel){
        $this->userModel = $userModel;
    }

    public function show($userId)
    {
        try{
            return $this->userModel::find($userId);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function findBasiqUserId($basiqUserId)
    {
        return $this->userModel::where('basiq_user_id', $basiqUserId)->first();
    }
}
