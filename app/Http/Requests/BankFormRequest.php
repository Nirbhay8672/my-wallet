<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:banks,name,' . $this->id,
        ];

        if ($this->id == null) {
            $rules['logo'] = 'required|image|max:2048';
        } else {
            $rules['logo'] = 'nullable|image|max:2048';
        }

        return $rules;
    }
}
