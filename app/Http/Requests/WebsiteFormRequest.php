<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WebsiteFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'up_or_down' => 'required',
            'website_name' => 'required|string|unique:websites,website_name',
            'website_logo' => 'required|file|mimes:jpg,png|max:500000',
            'website_url' => 'required|string',
            'google_rank' => 'required|numeric',
            'time' => 'required|numeric',
            'total_update' => 'required|numeric',
            'is_backup_active' => 'required|numeric',
            'total_site_helth' => 'required|numeric',
            'total_php_issue' => 'required|numeric',
            'wp_admin_url' => 'required|string',

            'client_name' => 'required|string',
            'client_phone_number' => 'required|numeric|digits:10',
            'client_email' => 'required|email',
            'client_joining_date' => 'required|date|before_or_equal:'. today(),
        ];
    }

    public function messages(): array
    {
        return [
            'joining_date.before_or_equal' => "The joining date field must be a date before or equal to ". today()->format('Y-m-d'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ]));
    }
}