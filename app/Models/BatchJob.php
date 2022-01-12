<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchJob extends Model
{
    protected $table = "batch_jobs";

    protected $guarded = ['id'];

    protected $fillable = ['user_id'];

    public $timestamps = false;

    public function users(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
