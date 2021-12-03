<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class View extends Model
{
    use HasFactory;

    protected $table = "views";

    ###################
    ## Relationships ##
    ###################

    public function subViews()
    {
        return $this->hasMany(View::class, 'module', 'module')
            ->where('level', 2);
    }

}
