<?php

namespace App\Rules;

use App\Constants\CommissionConstant;
use App\Models\CommissionSkuSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class CheckCalculationType implements Rule, DataAwareRule
{
    public function passes($attribute, $value)
    {
        if ($value == CommissionConstant::CALCULATION_TYPE_SKU) {
            return CommissionSkuSetting::where('client_code', $this->data['client_code'])->exists(); 
        }

        return true;
    }

    public function message()
    {
        return 'Once the SKU commission(s) is created, you are able to select the "SKU".
        Go to: Setting > SKU Commission > Upload SKU';
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
