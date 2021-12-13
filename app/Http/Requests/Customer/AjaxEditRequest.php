<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseFormRequest;

class AjaxEditRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'client_code' => 'required',
        ];
    }
}
