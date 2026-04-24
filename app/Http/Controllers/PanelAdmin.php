<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prueba;
use App\Models\Ciclista;
use App\Models\Equipo;
use App\Models\Etapa;

class PanelAdmin extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $pruebas = Prueba::count();
        $ciclistas = Ciclista::count();
        $equipos = Equipo::count();

        // 5 ultimas etapas
        $ultimasEtapas = Etapa::orderBy('fecha', 'desc')->take(5)->get();
        
        return view('dashboard', compact('pruebas', 'ciclistas', 'equipos', 'ultimasEtapas'));
    }
}
