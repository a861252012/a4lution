<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => ['max:20'],
            'company_name' => ['required', 'max:30'],
            'address' => ['required', 'max:40'],
            'phone_number' => ['required', 'min:6' ,'max:40'],
            'email' => ['required', 'min:5', 'max:30'],
        ];
    }
}
