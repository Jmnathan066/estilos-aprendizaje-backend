<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('curso');
            $table->string('correo');                       // <- agregado: para enviar y para el Excel
            $table->string('estilo_aprendizaje')->nullable();

            // <- agregado: puntaje 0-20 de cada estilo, para el desglose opcional en el Excel
            $table->unsignedTinyInteger('puntaje_activo')->nullable();
            $table->unsignedTinyInteger('puntaje_reflexivo')->nullable();
            $table->unsignedTinyInteger('puntaje_teorico')->nullable();
            $table->unsignedTinyInteger('puntaje_pragmatico')->nullable();

            $table->boolean('correo_enviado')->default(false); // <- agregado: para saber si sí llegó

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
