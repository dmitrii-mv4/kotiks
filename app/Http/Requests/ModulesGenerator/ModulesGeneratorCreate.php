<?php

namespace App\Http\Requests\ModulesGenerator;

use Illuminate\Foundation\Http\FormRequest;

class ModulesGeneratorCreate extends FormRequest
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
            'name' => 'required|min:3|max:255',
            'code' => 'required|min:3|max:255',

            // Валидация для массивов свойств
            'name_property' => 'sometimes|array',
            'name_property.*' => 'required|min:2|max:255|distinct',
            
            'property' => 'sometimes|array',
            'property.*' => 'required|min:2|max:255',
            
            'code_property' => 'sometimes|array',
            'code_property.*' => 'required|min:2|max:255|distinct',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Название модуля обязательно для заполнения!',
            'name.min' => 'Название модуля должно быть не менее :min символов',
            'name.max' => 'Название модуля должно быть не более :max символов',
            
            'code.required' => 'Код модуля обязателен для заполнения!',
            'code.min' => 'Код модуля должен быть не менее :min символов',
            'code.max' => 'Код модуля должен быть не более :max символов',
            'code.unique' => 'Модуль с таким кодом уже существует',
            
            // Сообщения для массивов
            'name_property.*.required' => 'Название свойства обязательно для заполнения',
            'name_property.*.min' => 'Название свойства должно быть не менее :min символов',
            'name_property.*.max' => 'Название свойства должно быть не более :max символов',
            'name_property.*.distinct' => 'Названия свойств не должны повторяться',
            
            'property.*.required' => 'Тип свойства обязателен для заполнения',
            'property.*.min' => 'Тип свойства должен быть не менее :min символов',
            'property.*.max' => 'Тип свойства должен быть не более :max символов',
            
            'code_property.*.required' => 'Код свойства обязателен для заполнения',
            'code_property.*.min' => 'Код свойства должен быть не менее :min символов',
            'code_property.*.max' => 'Код свойства должен быть не более :max символов',
            'code_property.*.distinct' => 'Коды свойств не должны повторяться',
        ];
    }
}