<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'temperature_unit' => 'required|in:C,F',
            'theme' => 'required|in:light,dark,system',
            'timezone' => 'required|timezone',
            'default_packing_items' => 'nullable|array',
            'notifications' => 'nullable|array',
            'privacy_settings' => 'nullable|array',
        ];
    }
}
