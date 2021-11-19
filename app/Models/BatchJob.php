<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchJob extends Model
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
