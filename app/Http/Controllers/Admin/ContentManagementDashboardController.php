<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Models\ProjectConsoleJob;
use App\Services\Admin\AdminOverviewStatsService;
use App\Services\Rules\GameDownloadCatalog;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Vue d’ensemble — gestion du contenu (entités × statuts, pages, sections).
 *
 * Les props / actions pipeline (atelier DofusDB, compilation livre, prix) ne sont
 * exposées qu’aux admins.
 */
class ContentManagementDashboardController extends Controller
{
    use SharesProjectConsoleJob;

    public function __invoke(
        Request $request,
        AdminOverviewStatsService $stats,
        GameDownloadCatalog $downloads,
    ): InertiaResponse {
        $canPipeline = $request->user()?->isAdmin() === true;

        $payload = [
            'overview' => $stats->contentOverview(),
            'stateLabels' => AdminOverviewStatsService::stateLabels(),
            'stateColors' => AdminOverviewStatsService::stateColors(),
            'canPipeline' => $canPipeline,
            'rulesDownloads' => null,
            'pricesConsoleJob' => null,
        ];

        if ($canPipeline) {
            $payload = array_merge($payload, [
                'rulesDownloads' => $downloads->generatedStatus(),
                'pricesConsoleJob' => ProjectConsoleJob::latestForDomain(ProjectConsoleDomain::ENTITY_PRICES)?->toStatusPayload(),
            ], $this->consoleJobProps(ProjectConsoleDomain::RULES_DOWNLOADS));
        }

        return Inertia::render('Admin/Content/Dashboard/Index', $payload);
    }
}
