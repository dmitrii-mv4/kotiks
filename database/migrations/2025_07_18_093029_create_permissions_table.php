<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('title');
            $table->timestamps();
        });

        DB::table('permissions')->insert([
            [
                'id' => '1',
                'name' => 'show_admin',
                'title' => 'Показать Административную панель',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '2',
                'name' => 'show_service_site',
                'title' => 'Показать сайт на обслуживании',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '3',
                'name' => 'users_viewAny',
                'title' => 'Показать пользователей',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '4',
                'name' => 'users_view',
                'title' => 'Показать профиль пользователей',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '5',
                'name' => 'users_create',
                'title' => 'Создавать пользователей',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '6',
                'name' => 'users_update',
                'title' => 'Редактировать пользователей',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '7',
                'name' => 'users_delete',
                'title' => 'Удалять пользователей',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '8',
                'name' => 'roles_viewAny',
                'title' => 'Показать роли',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '9',
                'name' => 'roles_create',
                'title' => 'Создать роль',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '10',
                'name' => 'roles_update',
                'title' => 'Редактирование роли',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => '11',
                'name' => 'roles_delete',
                'title' => 'Удаление роли',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
