<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur pour le dashboard de scrapping
 * 
 * Affiche l'interface utilisateur pour gérer les imports depuis DofusDB.
 * 
 * @package App\Http\Controllers\Scrapping
 */
class ScrappingDashboardController extends Controller
{
    /**
     * Affiche la page principale du dashboard de scrapping
     * 
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Pages/scrapping/Index', [
            'title' => 'Gestion du Scrapping',
            'description' => 'Importez des données depuis DofusDB vers KrosmozJDR',
        ]);
    }
}

