<?php


namespace App\Models;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;

class BatchJobs extends Model
{
    protected $table = "batch_jobs";

    protected $guarded = ['id'];

    protected $fillable = ['user_id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id', 'user_id');
    }
}
