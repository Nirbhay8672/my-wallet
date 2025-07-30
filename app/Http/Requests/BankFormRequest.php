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
        return [
            'name' => 'required|string|max:255',
            'logo' => 'required|file',
        ];
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
