<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectReviewWebRequest;
use App\Jobs\RunProjectReviewJob;
use App\Services\Project\DevReportsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Consultation et génération des rapports `project:review` (super-admin interactif).
 */
class ProjectReviewWebController extends Controller
{
    public function index(DevReportsService $reports): Response
    {
        return Inertia::render('Admin/project-review/Index', [
            'reports' => $reports->listMarkdownReports()->all(),
            'reportsPathHint' => 'storage/app/dev-reports/',
        ]);
    }

    public function download(DevReportsService $reports, string $report): BinaryFileResponse
    {
        $resolved = $reports->resolveSafeDownloadPath($report);
        abort_if($resolved === null, 404);

        return response()->download($resolved['path'], $resolved['basename'], [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    public function store(StoreProjectReviewWebRequest $request): RedirectResponse
    {
        $svc = new DevReportsService;

        File::ensureDirectoryExists($svc->storageDirectory());

        $basename = 'review-web-'.now()->format('Y-m-d-His').'-'.Str::random(8).'.md';
        $fullPath = $svc->storageDirectory().'/'.$basename;

        RunProjectReviewJob::dispatch(
            $request->user()->id,
            $fullPath,
            $request->artisanArguments(),
        );

        return redirect()
            ->back()
            ->with('success', 'Génération du rapport démarrée (file d’attente). Recharge cette page après quelques instants.');
    }
}
