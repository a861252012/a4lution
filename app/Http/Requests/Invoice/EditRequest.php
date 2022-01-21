<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\BaseFormRequest;

class EditRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'report_date' => 'required|date',
            'client_code' => 'required|string',
            'billing_statement_id' => 'required',
        ];
    }
}
