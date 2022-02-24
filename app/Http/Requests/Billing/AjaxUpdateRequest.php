<?php

namespace App\Http\Requests\Billing;

use App\Http\Requests\BaseFormRequest;

class AjaxUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'amount_description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0|max:9999999.99',
            'currency' => 'required|string|size:3',
            'deposit_date' => 'required|date_format:Y-m-d',
            'remarks' => 'nullable|string|max:500'
        ];
    }
}
