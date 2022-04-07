<?php

namespace App\Http\Requests\ContinStorage;

use App\Http\Requests\BaseFormRequest;

class AjaxUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:App\Models\ContinStorageFee,id',
            'col' => 'required|string',
            'value' => 'required|numeric|gte:0', // amount
        ];
    }
}
