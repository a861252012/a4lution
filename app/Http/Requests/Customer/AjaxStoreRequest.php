<?php

namespace App\Http\Requests\Customer;

use App\Rules\CheckCalculationType;
use App\Constants\CommissionConstant;
use App\Http\Requests\BaseFormRequest;

class AjaxStoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required|unique:App\Models\Customer,client_code',
            'company_name' => 'nullable',
            'company_contact' => 'nullable',
            'street1' => 'nullable',
            'street2' => 'nullable',
            'city' => 'nullable',
            'district' => 'nullable',
            'zip' => 'nullable',
            'country' => 'nullable',
            'region' => 'nullable',
            'contract_period_start' => 'nullable',
            'contract_period_end' => 'nullable',
            'commission_deduct_refund_cxl_order' => 'required',
            'active' => 'nullable',
            'staff_members' => 'nullable',
            'calculation_type' => ['nullable', new CheckCalculationType],
            'basic_rate' => 'nullable|required_if:calculation_type,'.CommissionConstant::CALCULATION_TYPE_BASIC_RATE,
            'tier_1_threshold' => 'nullable|integer|min:0|required_if:calculation_type,'.CommissionConstant::CALCULATION_TYPE_TIER,
            'tier_2_threshold' => 'nullable|integer|min:0|gt:tier_1_threshold',
            'tier_3_threshold' => 'nullable|integer|min:0|gt:tier_2_threshold',
            'tier_4_threshold' => 'nullable|integer|min:0|gt:tier_3_threshold',
            'tier_1_amount' => 'nullable|numeric|min:0',
            'tier_2_amount' => 'nullable|numeric|min:0',
            'tier_3_amount' => 'nullable|numeric|min:0',
            'tier_4_amount' => 'nullable|numeric|min:0',
            'tier_top_amount' => 'nullable|numeric|min:0',

            'tier_1_rate' => 'nullable|integer|between:0,100',
            'tier_2_rate' => 'nullable|integer|between:0,100',
            'tier_3_rate' => 'nullable|integer|between:0,100',
            'tier_4_rate' => 'nullable|integer|between:0,100',
            'tier_top_rate' => 'nullable|integer|between:0,100',

            'percentage_off_promotion' => 'nullable|integer|between:0,100|required_with:tier_promotion',
            'tier_promotion' => 'nullable|integer|between:0,100|required_with:percentage_off_promotion',
        ];
    }

    public function messages(): array
    {
        return [
            'basic_rate.required_if' => 'The basic rate field is required when calculation type is [ Basic Rate ].',
            'tier_1_threshold.integer' => 'The [ Amount Threshold 1 ] must be an integer.',
            'tier_1_threshold.min' => 'The [ Amount Threshold 1 ] must be at least :min',
            'tier_1_threshold.required_if' => 'The [ Amount Threshold 1 ] field is required when calculation type is [ Tier ].',
            'tier_2_threshold.integer' => 'The [ Amount Threshold 2 ] must be an integer.',
            'tier_2_threshold.min' => 'The [ Amount Threshold 2 ] must be at least :min',
            'tier_2_threshold.gt' => 'The [ Amount Threshold 2 ] amount must be greater than [ Amount Threshold 1 ].',
            'tier_3_threshold.integer' => 'The [ Amount Threshold 3 ] must be an integer.',
            'tier_3_threshold.min' => 'The [ Amount Threshold 3 ] must be at least :min',
            'tier_3_threshold.gt' => 'The [ Amount Threshold 3 ] amount must be greater than [ Amount Threshold 2 ].',
            'tier_4_threshold.integer' => 'The [ Amount Threshold 4 ] must be an integer.',
            'tier_4_threshold.min' => 'The [ Amount Threshold 4 ] must be at least :min',
            'tier_4_threshold.gt' => 'The [ Amount Threshold 4 ] amount must be greater than [ Amount Threshold 3 ].',

            'tier_1_amount.min' => 'The [ Commission Amount 1 ] must be at least :min',
            'tier_2_amount.min' => 'The [ Commission Amount 2 ] must be at least :min',
            'tier_3_amount.min' => 'The [ Commission Amount 3 ] must be at least :min',
            'tier_4_amount.min' => 'The [ Commission Amount 4 ] must be at least :min',
            'tier_top_amount.min' => 'The [ Commission Maximum Amount ] must be at least :min',

            'tier_1_rate.integer' => 'The [ Commission Rate 1 ] must be an integer.',
            'tier_1_rate.between' => 'The [ Commission Rate 1 ] must be between :min - :max.',
            'tier_2_rate.integer' => 'The [ Commission Rate 2 ] must be an integer.',
            'tier_2_rate.between' => 'The [ Commission Rate 2 ] must be between :min - :max.',
            'tier_3_rate.integer' => 'The [ Commission Rate 3 ] must be an integer.',
            'tier_3_rate.between' => 'The [ Commission Rate 3 ] must be between :min - :max.',
            'tier_4_rate.integer' => 'The [ Commission Rate 4 ] must be an integer.',
            'tier_4_rate.between' => 'The [ Commission Rate 4 ] must be between :min - :max.',
            'tier_top_rate.integer' => 'The [ Commission Rate Maximum Amount ] must be an integer.',
            'tier_top_rate.between' => 'The [ Commission Rate Maximum Amount ] must be between :min - :max.',

            'percentage_off_promotion.required_with' => 'The [ Percentage Off Promotion ] field is required when [ Promo Commission Rate ] is present.',
            'percentage_off_promotion.integer' => 'The [ Percentage Off Promotion ] must be an integer.',
            'percentage_off_promotion.between' => 'The [ Percentage Off Promotion ] must be between :min - :max.',
            'tier_promotion.required_with' => 'The [ Promo Commission Rate ] field is required when [ Percentage Off Promotion ] is present.',
            'tier_promotion.integer' => 'The [ Promo Commission Rate ] must be an integer.',
            'tier_promotion.between' => 'The [ Promo Commission Rate ] must be between :min - :max.',
        ];
    }
}