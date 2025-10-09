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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Добавление ролей по умолчанию
        DB::table('roles')->insert([
        [
            'id' => '1',
            'name' => 'Главный админстратор',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '2',
            'name' => 'Администратор',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '3',
            'name' => 'Модератор',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id' => '4',
            'name' => 'Пользователь',
            'created_at' => now(),
            'updated_at' => now()
        ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
