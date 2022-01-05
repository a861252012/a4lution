<?php

namespace App\Http\Requests\BillingStatement;

use App\Http\Requests\BaseFormRequest;

class AjaxStoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'report_date' => 'required',
            'client_code' => 'required',
        ];
    }
}
