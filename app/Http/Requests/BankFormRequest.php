<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        // Logo is required for new banks, optional for updates
        if (!$this->filled('id')) {
            $rules['logo'] = 'required|file|image|mimes:jpeg,jpg,png|max:2048';
        } else {
            $rules['logo'] = 'nullable|file|image|mimes:jpeg,jpg,png|max:2048';
        }

        return $rules;
    }

    public function fields(): array
    {
        $fields = [
            'name' => $this->name,
            'logo' => 'temp',
        ];

        return $fields;
    }

    public function action(): string
    {
        return is_null($this->id) ? 'created' : 'updated';
    }
}
