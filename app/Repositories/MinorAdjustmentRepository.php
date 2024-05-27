<?php

namespace App\Repositories;

use App\Interfaces\MinorAdjustmentRepositoryInterface;
use App\Models\MinorAdjustments;
use DB;

class MinorAdjustmentRepository implements MinorAdjustmentRepositoryInterface 
{
    public function __construct(MinorAdjustments $minorAdjustmentModel){
        $this->minorAdjustmentModel = $minorAdjustmentModel;
    }

    public function create($minorAdjustments)
    {
        try{
            return $this->minorAdjustmentModel::create($minorAdjustments);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }
}
?>