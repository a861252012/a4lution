<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class EmployeeCommission extends Model
{
    protected $table = "employee_commissions";

    protected $guarded = ['id'];

//    protected $fillable = ['user_id'];
//    public $timestamps = false;
}
