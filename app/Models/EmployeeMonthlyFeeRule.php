<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyFeeRule extends Model
{
    protected $table = "employee_monthly_fee_rules";

    protected $primaryKey = null;

    protected $guarded = [];

    public $incrementing = false;

    protected $casts = [
        'rate_base' => 'float',
        'rate' => 'float',
        'tier_1_first_year' => 'float',
        'tier_2_first_year' => 'float',
        'tier_1_over_a_year' => 'float',
        'tier_2_over_a_year' => 'float',
    ];
}
