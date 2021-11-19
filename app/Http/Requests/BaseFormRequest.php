<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{

    abstract public function rules(): array;


    public function authorize()
    {
        return Auth::check();
    }
}
