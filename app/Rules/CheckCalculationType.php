<?php

namespace App\Rules;

use App\Constants\CommissionConstant;
use App\Models\CommissionSkuSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class CheckCalculationType implements Rule, DataAwareRule
{
    protected $msg;

    public function passes($attribute, $value)
    {
        if ($value == CommissionConstant::CALCULATION_TYPE_SKU) {
            $this->msg = 'Once the SKU commission(s) is created, you are able to select the "SKU".
                Go to: Setting > SKU Commission > Upload SKU';

            return CommissionSkuSetting::where('client_code', $this->data['client_code'])->exists(); 
        }

        if ($value == CommissionConstant::CALCULATION_TYPE_TIER) {
            $this->msg = 'The [ Commission Amount 1 ] or [ Commission Rate 1 ] field is required when calculation type is [ Tier ].';

            // 至少一個欄位有值
            return $this->data['tier_1_amount'] || $this->data['tier_1_rate'];
        }

        return true;
    }

    public function message()
    {
        return $this->msg;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
