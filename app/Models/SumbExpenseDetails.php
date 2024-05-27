<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbExpenseDetails extends Model
{
    use HasFactory;
    protected $fillable  = ['user_id','expense_number','client_name','expense_date','expense_due_date','tax_type','expense_total_amount','total_gst','total_amount','file_upload','file_upload_format','updated_at','status_paid','inactive_status']; //the field name inside the array is not mass-assignable

}
