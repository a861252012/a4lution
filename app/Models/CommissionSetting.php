<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $table = "commission_settings";

    protected $primaryKey = null;

    protected $guarded = [];

    public $incrementing = false;

    ############
    ## Others ##
    ############
    public function isSku(): bool
    {
        return $this->is_sku_level_commission === 'T';
    }

    public function isTier(): bool
    {
        return $this->tier === 'T';
    }
}
