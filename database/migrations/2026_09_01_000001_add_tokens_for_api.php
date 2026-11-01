<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->uuid('token')->nullable()->unique(); // identifica el resultado del estudiante sin sesión
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 64)->nullable()->unique(); // sha256 del token del profesor
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', fn (Blueprint $t) => $t->dropColumn('token'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('api_token'));
    }
};
