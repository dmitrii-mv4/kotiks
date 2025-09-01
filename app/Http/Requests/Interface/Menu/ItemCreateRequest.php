<?php

namespace App\Http\Requests\Interface\Menu;

use Illuminate\Foundation\Http\FormRequest;

class ItemCreateRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|max:255',
            'items.*.url' => 'required|max:255',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Добавьте хотя бы один пункт меню',
            'items.min' => 'Добавьте хотя бы один пункт меню',
            'items.*.title.required' => 'Название пункта меню обязательно',
            'items.*.title.max' => 'Название должно быть не более :max символов',
            'items.*.url.required' => 'URL пункта меню обязателен',
            'items.*.url.max' => 'URL должен быть не более :max символов',
        ];
    }
}