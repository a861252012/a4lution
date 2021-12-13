<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseFormRequest;

class IndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'nullable',
            'active' => 'nullable',
            'sales_region' => 'nullable',
        ];
    }
}
