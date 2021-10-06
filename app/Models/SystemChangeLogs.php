<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemChangeLogs extends Model
{
    protected $table = "system_changelogs";

    protected $primaryKey = 'log_id';

    protected $guarded = ['id'];
}
