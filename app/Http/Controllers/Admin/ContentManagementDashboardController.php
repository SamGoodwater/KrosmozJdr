<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminOverviewStatsService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Vue d’ensemble — gestion du contenu (entités × statuts, pages, sections).
 */
class ContentManagementDashboardController extends Controller
{
    public function __invoke(AdminOverviewStatsService $stats): InertiaResponse
    {
        return Inertia::render('Admin/Content/Dashboard/Index', [
            'overview' => $stats->contentOverview(),
            'stateLabels' => AdminOverviewStatsService::stateLabels(),
            'stateColors' => AdminOverviewStatsService::stateColors(),
        ]);
    }
}
