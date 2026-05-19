<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminOverviewStatsService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Récapitulatif administration : utilisateurs dans le temps et par rôle.
 */
class AdminRecapController extends Controller
{
    public function __invoke(AdminOverviewStatsService $stats): InertiaResponse
    {
        return Inertia::render('Admin/Recap/Index', [
            'recap' => $stats->adminRecap(),
        ]);
    }
}
