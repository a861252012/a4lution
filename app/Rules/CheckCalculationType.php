<?php

namespace App\Rules;

use App\Constants\CommissionConstant;
use App\Models\CommissionSkuSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class CheckCalculationType implements Rule, DataAwareRule
{
    protected array $msg;

    public function passes($attribute, $value)
    {
        if ($value == CommissionConstant::CALCULATION_TYPE_SKU) {
            $this->msg[] = 'Once the SKU commission(s) is created, you are able to select the "SKU".
                Go to: Setting > SKU Commission > Upload SKU';

            return CommissionSkuSetting::where('client_code', $this->data['client_code'])->exists(); 
        }

        if ($value == CommissionConstant::CALCULATION_TYPE_TIER) {

            return $this->checkThresholdAmountRate() && $this->checkTopAmountRate();
        }

        return true;
    }

    public function message()
    {
        return implode(PHP_EOL ,$this->msg);
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    private function checkThresholdAmountRate()
    {
        $pass = 1;

        for ($i = 1; $i <= 4; $i++) { 
            $threshold = "tier_{$i}_threshold";
            $amount = "tier_{$i}_amount";
            $rate = "tier_{$i}_rate";

            if ($this->data[$threshold]) {
                // amount & rate 二擇一選擇
                if (!$this->data[$amount] && !$this->data[$rate]) {
                    $this->msg[] = "The [ Commission Amount {$i} ] or [ Commission Rate {$i} ] field is required when calculation type is [ Tier ] and [ Amount Threshold {$i} ] has value.";

                    $pass = 0;
                }
            }
        }
        
        return $pass;
    }

    private function checkTopAmountRate()
    {
        $pass = 1;

        if (!$this->data['tier_top_amount'] && !$this->data['tier_top_rate']) {
            $this->msg[] = "The [ Commission Maximum Amount ] or [ Commission Rate Maximum Amount ] field is required when calculation type is [ Tier ].";

            $pass = 0;
        }

        return $pass;
    }
}
