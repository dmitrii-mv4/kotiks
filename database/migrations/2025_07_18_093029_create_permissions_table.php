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
            $table->text('description');
            $table->timestamps();
        });

        DB::table('permissions')->insert([
        [
            'id' => '1',
            'name' => 'show_admin',
            'description' => 'Показать Административную панель.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '2',
            'name' => 'show_service_site',
            'description' => 'Показать сайт на обслуживании.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '3',
            'name' => 'users_viewAny',
            'description' => 'Показать пользователей.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '4',
            'name' => 'users_view',
            'description' => 'Показать пользователей.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '5',
            'name' => 'users_create',
            'description' => 'Создавать пользователей.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '6',
            'name' => 'users_update',
            'description' => 'Редактировать пользователей.',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '7',
            'name' => 'users_delete',
            'description' => 'Удалять пользователей.',
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
