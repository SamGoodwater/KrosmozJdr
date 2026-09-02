<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminOverviewStatsService;
use App\Services\Rules\GameDownloadCatalog;
use App\Support\Project\ProjectConsoleDomain;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Vue d’ensemble — gestion du contenu (entités × statuts, pages, sections).
 */
class ContentManagementDashboardController extends Controller
{
    use SharesProjectConsoleJob;

    public function __invoke(AdminOverviewStatsService $stats, GameDownloadCatalog $downloads): InertiaResponse
    {
        return Inertia::render('Admin/Content/Dashboard/Index', array_merge([
            'overview' => $stats->contentOverview(),
            'stateLabels' => AdminOverviewStatsService::stateLabels(),
            'stateColors' => AdminOverviewStatsService::stateColors(),
            'rulesDownloads' => $downloads->generatedStatus(),
        ], $this->consoleJobProps(ProjectConsoleDomain::RULES_DOWNLOADS)));
    }
}
