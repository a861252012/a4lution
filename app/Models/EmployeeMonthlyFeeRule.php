<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyFeeRule extends Model
{
    protected $table = "employee_monthly_fee_rules";

    protected $primaryKey = null;

    protected $guarded = [];

    public $incrementing = false;
}
