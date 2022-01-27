<?php

namespace App\Http\Requests\Billing;

use App\Http\Requests\BaseFormRequest;

class AjaxCreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required|max:50',
            'amount_description' => 'nullable|string|max:500',
            'billing_month' => 'required|date_format:Ym|size:6',
            'amount' => 'required|numeric|min:0|max:9999999.99',
            'currency' => 'required|string|size:3',
            'deposit_date' => 'required|date_format:Y-m-d',
            'remarks' => 'nullable|string|max:500'
        ];
    }
}