<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseFormRequest;

class AjaxUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required',
            'company_name' => 'nullable',
            'company_contact' => 'nullable',
            'street1' => 'nullable',
            'street2' => 'nullable',
            'city' => 'nullable',
            'district' => 'nullable',
            'zip' => 'nullable',
            'country' => 'nullable',
            'sales_region' => 'required',
            'contract_date' => 'required',
            'active' => 'nullable',
            'staff_members' => 'nullable',
            'calculation_type' => 'nullable',
            'basic_rate' => 'nullable',
            'tier_1_threshold' => 'nullable',
            'tier_1_amount' => 'nullable',
            'tier_1_rate' => 'nullable',
            'tier_2_threshold' => 'nullable',
            'tier_2_amount' => 'nullable',
            'tier_2_rate' => 'nullable',
            'tier_3_threshold' => 'nullable',
            'tier_3_amount' => 'nullable',
            'tier_3_rate' => 'nullable',
            'tier_4_threshold' => 'nullable',
            'tier_4_amount' => 'nullable',
            'tier_4_rate' => 'nullable',
            'tier_top_amount' => 'nullable',
            'tier_top_rate' => 'nullable',
            'percentage_of_promotion' => 'nullable',
            'tier_promotion' => 'nullable',
        ];
    }
}
