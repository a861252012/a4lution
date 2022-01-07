<?php

namespace App\Http\Requests\SkuCommission;

use App\Http\Requests\BaseFormRequest;

class ajaxUploadStoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required',
            'file' => 'required|mimes:xlsx,xls',
        ];
    }
}
