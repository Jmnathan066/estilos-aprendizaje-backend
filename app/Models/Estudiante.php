<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    protected $fillable = [
        'nombre', 'segundo_nombre', 'apellido', 'segundo_apellido', 'correo', 'curso',
        'estilo_aprendizaje', 'puntaje_activo', 'puntaje_reflexivo', 'puntaje_teorico',
        'puntaje_pragmatico', 'correo_enviado',
    ];

    protected $hidden = ['id'];
}
