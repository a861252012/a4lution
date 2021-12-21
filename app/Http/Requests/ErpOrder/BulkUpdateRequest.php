<?php

namespace App\Http\Requests\ErpOrder;

use App\Http\Requests\BaseFormRequest;

class BulkUpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|max:5120|mimes:xlsx',
        ];
    }
}
