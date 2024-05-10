<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|unique:clients,name,' . $this->id,
            'phone_number' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:clients,email,' . $this->id,
            'joining_date' => 'required|date|before_or_equal:'. today(),
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'joining_date.before_or_equal' => "The joining date field must be a date before or equal to ". today()->format('Y-m-d'),
        ];
    }

    public function action(): string
    {
        return is_null($this->id) ? 'created' : 'updated';
    }
}