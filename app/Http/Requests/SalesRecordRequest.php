<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'integer', 'min:1'],
            'order_id' => ['required', 'string'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
