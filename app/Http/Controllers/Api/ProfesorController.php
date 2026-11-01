<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProfesorController extends Controller
{
    private function consulta(Request $request)
    {
        $q = Estudiante::query()->orderBy('created_at', 'desc');
        if ($request->filled('grado')) {
            $q->where('curso', $request->grado);
        }
        return $q;
    }

    public function index(Request $request)
    {
        return response()->json([
            'estudiantes' => $this->consulta($request)->get()->map(fn ($e) => [
                'nombre_completo' => trim("$e->nombre $e->segundo_nombre $e->apellido $e->segundo_apellido"),
                'curso' => $e->curso,
                'activo' => $e->puntaje_activo,
                'reflexivo' => $e->puntaje_reflexivo,
                'teorico' => $e->puntaje_teorico,
                'pragmatico' => $e->puntaje_pragmatico,
                'estilo' => $e->estilo_aprendizaje,
            ]),
            'grados' => Estudiante::select('curso')->distinct()->orderBy('curso')->pluck('curso'),
        ]);
    }

    public function excel(Request $request)
    {
        $hoja = ($spreadsheet = new Spreadsheet())->getActiveSheet();
        $hoja->setTitle('Resultados');
        $hoja->fromArray(['Nombre', 'Segundo nombre', 'Apellido', 'Segundo apellido', 'Correo', 'Grado',
            'Estilo de aprendizaje', 'Activo', 'Reflexivo', 'Teórico', 'Pragmático', 'Fecha'], null, 'A1');

        $fila = 2;
        foreach ($this->consulta($request)->get() as $e) {
            $hoja->fromArray([$e->nombre, $e->segundo_nombre, $e->apellido, $e->segundo_apellido, $e->correo,
                $e->curso, $e->estilo_aprendizaje, $e->puntaje_activo, $e->puntaje_reflexivo,
                $e->puntaje_teorico, $e->puntaje_pragmatico, $e->created_at->format('Y-m-d H:i:s')], null, 'A'.$fila++);
        }
        foreach (range('A', 'L') as $c) {
            $hoja->getColumnDimension($c)->setAutoSize(true);
        }

        $nombre = $request->filled('grado') ? 'resultados_grado_'.$request->grado.'.xlsx' : 'resultados_generales.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(fn () => $writer->save('php://output'), $nombre, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
