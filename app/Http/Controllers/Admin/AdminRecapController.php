<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Console\CommandGuide;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminOverviewStatsService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Récapitulatif administration : utilisateurs dans le temps et par rôle.
 *
 * Super-admin : liste des commandes `ui: true` avec liens vers les pages thématiques.
 */
class AdminRecapController extends Controller
{
    public function __invoke(AdminOverviewStatsService $stats): InertiaResponse
    {
        $user = request()->user();

        return Inertia::render('Admin/Recap/Index', [
            'recap' => $stats->adminRecap(),
            'commands' => ($user && $user->isSuperAdmin())
                ? CommandGuide::forUiCards()
                : [],
        ]);
    }
}
