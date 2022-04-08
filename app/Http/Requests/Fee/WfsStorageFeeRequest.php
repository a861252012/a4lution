<?php

namespace App\Http\Requests\Fee;

use App\Http\Requests\BaseFormRequest;

class WfsStorageFeeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'report_date' => 'nullable|date',
            'supplier' => 'nullable|string'
        ];
    }
}