<?php

namespace App\Http\Requests\Customer;

use App\Constants\Commission;
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
            'basic_rate' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_BASIC_RATE,
            'tier_1_threshold' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_TIER,
            'tier_1_amount' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_TIER,
            'tier_1_rate' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_TIER,
            'tier_2_threshold' => 'nullable',
            'tier_2_amount' => 'nullable',
            'tier_2_rate' => 'nullable',
            'tier_3_threshold' => 'nullable',
            'tier_3_amount' => 'nullable',
            'tier_3_rate' => 'nullable',
            'tier_4_threshold' => 'nullable',
            'tier_4_amount' => 'nullable',
            'tier_4_rate' => 'nullable',
            'tier_top_amount' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_TIER,
            'tier_top_rate' => 'nullable|required_if:calculation_type,'.Commission::CALCULATION_TYPE_TIER,
            'percentage_of_promotion' => 'nullable|required_with:tier_promotion',
            'tier_promotion' => 'nullable|required_with:percentage_of_promotion',
        ];
    }

    public function messages(): array
    {
        return [
            'basic_rate.required_if' => 'The basic rate field is required when calculation type is [ Basic Rate ].',
            'tier_1_threshold.required_if' => 'The [ Amount Threshold 1 ] field is required when calculation type is [ Tier ].',
            'tier_1_amount.required_if' => 'The [ Commission Amount 1 ] field is required when calculation type is [ Tier ].',
            'tier_1_rate.required_if' => 'The [ Commission Rate 1 ] field is required when calculation type is [ Tier ].',
            'tier_top_amount.required_if' => 'The [ Commission Maximum Amount ] field is required when calculation type is [ Tier ].',
            'tier_top_rate.required_if' => 'The [ Commission Rate Maximum Amount ] field is required when calculation type is [ Tier ].',
            'percentage_of_promotion.required_with' => 'The [ Percentage Off Promotion ] field is required when [ Promo Commission Rate ] is present.',
            'tier_promotion.required_with' => 'The [ Promo Commission Rate ] field is required when [ Percentage Off Promotion ] is present.',
        ];
    }
}
