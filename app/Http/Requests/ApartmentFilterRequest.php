<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartmentFilterRequest extends FormRequest
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
            'sort' => 'string|nullable',
            'page' => 'integer|nullable',
            'bedrooms' => 'string|nullable',
            'bathrooms' => 'string|nullable',
            'guests' => 'integer|nullable',
            'start' => 'date_format:Y-m-d|nullable',
            'priceRange' => 'string|nullable',
            'nights' => 'integer|nullable',
        ];
    }
}
