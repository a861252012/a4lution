<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class EmployeeCommissionRule extends Model
{
    protected $table = "employee_ops_commission_rules";

    protected $guarded = ['id'];

//    protected $fillable = ['user_id'];
//    public $timestamps = false;
}
