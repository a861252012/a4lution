<?php

namespace App\Http\Requests\SkuCommission;

use App\Http\Requests\BaseFormRequest;

class IndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'nullable',
            'sku' => 'nullable',
        ];
    }
}
