<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:10',
            'long_description' => 'nullable|min:20',
            'completed' => 'boolean',
        ];
    }
}
