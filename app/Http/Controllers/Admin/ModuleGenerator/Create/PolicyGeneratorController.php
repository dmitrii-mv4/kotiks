<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PolicyGeneratorController extends CreateModuleController
{
    private function ensurePolicyDir()
    {
        $modulesPolicyPath = app_path('Policies/Modules');
        if (!File::exists($modulesPolicyPath)) {
            File::makeDirectory($modulesPolicyPath, 0755, true);
        }
        return $modulesPolicyPath;
    }

    /**
     * Создаём Policies
     */
    public function createPolicies($validated, $modelName)
    {
        // Получаем данные
        $moduleName = Str::studly($validated['code']);
        $moduleNameCode = $validated['code'];
        
        $className = $moduleName.'Policy';

        // 1. Создаем папку Modules если нужно
        $modulesPolicyPath = $this->ensurePolicyDir();

        // 2. Создаем файл Policy в правильной папке
        $policyPath = app_path("Policies/Modules/".$moduleName."Policy.php");

        // $moduleNameCodePermissions = 'module_' . $moduleNameCode."->permissions";
        $moduleNamePermissionsViewAny = 'module_' . $moduleNameCode."_viewAny";
        $moduleNamePermissionsCreate = 'module_' . $moduleNameCode."_create";
        $moduleNamePermissionsUpdate = 'module_' . $moduleNameCode."_update";
        $moduleNamePermissionsDelete = 'module_' . $moduleNameCode."_delete";

        $content = <<<PHP
<?php

namespace App\Policies\Modules;

use Illuminate\Auth\Access\Response;
use App\Models\User;

class $className
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User \$user): bool
    {
        // Получаем все разрешения пользователя
        \$permissions = \$user->permissions;

        // Поиск конкретного разрешения по имени
        \$showPermission = \$permissions->firstWhere('name', '$moduleNamePermissionsViewAny');

        if (\$showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User \$user): bool
    {
        // Получаем все разрешения пользователя
        \$permissions = \$user->permissions;

        // Поиск конкретного разрешения по имени
        \$showPermission = \$permissions->firstWhere('name', '$moduleNamePermissionsCreate');

        if (\$showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User \$user): bool
    {
        // Получаем все разрешения пользователя
        \$permissions = \$user->permissions;

        // Поиск конкретного разрешения по имени
        \$showPermission = \$permissions->firstWhere('name', '$moduleNamePermissionsUpdate');

        if (\$showPermission)
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User \$user): bool
    {
        // Получаем все разрешения пользователя
        \$permissions = \$user->permissions;

        // Поиск конкретного разрешения по имени
        \$showPermission = \$permissions->firstWhere('name', '$moduleNamePermissionsDelete');

        if (\$showPermission)
        {
            return true;
        }

        return false;
    }
}
PHP;

        File::put($policyPath, $content);
        
        if (!File::exists($policyPath)) {
            throw new \Exception("Файл Policy не найден: ".$policyPath);
        }
        
        return "Policy создана: " . $policyPath;
    }
}