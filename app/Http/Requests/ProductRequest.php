<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

public function rules(): array
{
    $id = $this->route('product')->id ?? null;

    return [
        'name' => 'required|string|max:255',
        'sku' => "required|string|unique:products,sku,{$id}|max:255",
        'price' => 'required|numeric|min:0.01',
        'quantity' => 'required|integer|min:0',
        'image_path' => 'nullable|string|max:255',
    ];
}
}
