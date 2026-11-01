<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Seguro en BD nueva: solo borra el índice si existe
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS estudiantes_correo_unique');
    }

    public function down(): void
    {
    }
};
