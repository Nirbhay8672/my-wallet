<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|numeric',
            'payment_date' => 'required|date|before_or_equal:'. today(),
            'payment_time' => 'required|date_format:H:i',
            'package_type' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_date.before_or_equal' => "The payment date field must be a date before or equal to ". today()->format('Y-m-d'),
        ];
    }
}