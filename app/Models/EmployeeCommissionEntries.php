<?php

namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class EmployeeCommissionEntries extends Model
{
    protected $table = "employee_commission_entries";

    protected $guarded = ['id'];
}
