<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ApartmentPriceRequest extends FormRequest
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
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d',
        ];
    }

    public function from(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->input('from'));
    }

    public function to(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->input('to'));
    }
}
