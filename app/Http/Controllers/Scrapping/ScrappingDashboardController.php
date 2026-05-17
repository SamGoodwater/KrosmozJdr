<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contrôleur pour le dashboard de scrapping
 *
 * Affiche l'interface utilisateur pour gérer les imports depuis DofusDB.
 */
class ScrappingDashboardController extends Controller
{
    /**
     * Affiche la page principale du dashboard de scrapping
     */
    public function index(): Response
    {
        return Inertia::render('Pages/scrapping/Index', [
            'title' => 'Gestion du Scrapping',
            'description' => 'Importez des données depuis DofusDB vers KrosmozJDR',
        ]);
    }
}
