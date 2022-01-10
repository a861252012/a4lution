<?php

namespace App\Http\Requests\SkuCommission;

use App\Http\Requests\BaseFormRequest;

class ExportRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required_without:sku',
            'sku' => 'required_without:client_code',
        ];
    }
}
