<?php

namespace App\Http\Requests\Users\Roles;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ModuleGenerator;

class RoleEditRequest extends FormRequest
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
        $rules = [
            'name' => 'required|min:3|max:255',
            'show_admin' => 'sometimes|boolean',
            
            // Права для пользователей
            'users_viewAny' => 'sometimes|boolean',
            'users_view' => 'sometimes|boolean',
            'users_create' => 'sometimes|boolean',
            'users_update' => 'sometimes|boolean',
            'users_delete' => 'sometimes|boolean',
            
            // Права для ролей
            'roles_viewAny' => 'sometimes|boolean',
            'roles_create' => 'sometimes|boolean',
            'roles_update' => 'sometimes|boolean',
            'roles_delete' => 'sometimes|boolean',
        ];

        // Динамически добавляем правила для всех модулей
        $allModulesData = ModuleGenerator::getAllModuleData();
        
        foreach ($allModulesData as $moduleGroup) {
            foreach ($moduleGroup as $module) {
                $moduleCode = $module->code;
                
                // Добавляем правила для каждого действия модуля
                $rules['module_' . $moduleCode . '_viewAny'] = 'sometimes|boolean';
                $rules['module_' . $moduleCode . '_view'] = 'sometimes|boolean';
                $rules['module_' . $moduleCode . '_create'] = 'sometimes|boolean';
                $rules['module_' . $moduleCode . '_update'] = 'sometimes|boolean';
                $rules['module_' . $moduleCode . '_delete'] = 'sometimes|boolean';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Это поле обязательно для заполнения!',
            'name.min' => 'Имя должно быть не менее :min символов',
            'name.max' => 'Имя должно быть не более :max символов',
        ];
    }
}