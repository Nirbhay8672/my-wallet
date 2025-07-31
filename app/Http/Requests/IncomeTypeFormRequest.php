<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeTypeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:income_types,name,' . ($this->id ?? ''),
            'active' => 'boolean',
        ];

        // Icon is required for new income types, optional for updates
        if (!$this->filled('id')) {
            $rules['icon'] = 'required|file|image|mimes:jpeg,jpg,png|max:2048';
        } else {
            $rules['icon'] = 'nullable|file|image|mimes:jpeg,jpg,png|max:2048';
        }

        return $rules;
    }

    public function fields(): array
    {
        $fields = [
            'name' => $this->name,
            'icon' => 'temp',
            'active' => $this->boolean('active', true),
        ];

        return $fields;
    }

    public function action(): string
    {
        return is_null($this->id) ? 'created' : 'updated';
    }
}
