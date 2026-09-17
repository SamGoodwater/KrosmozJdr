<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectReviewWebRequest;
use App\Jobs\RunProjectReviewJob;
use App\Services\Project\DevReportsService;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
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
    use SharesProjectConsoleJob;

    public function index(DevReportsService $reports): Response
    {
        return Inertia::render('Admin/project-review/Index', array_merge([
            'reports' => $reports->listMarkdownReports()->all(),
            'reportsPathHint' => 'storage/app/dev-reports/',
        ], $this->consoleJobProps(ProjectConsoleDomain::REVIEW)));
    }

    public function download(DevReportsService $reports, string $report): BinaryFileResponse
    {
        $resolved = $reports->resolveSafeDownloadPath($report);
        abort_if($resolved === null, 404);

        return response()->download($resolved['path'], $resolved['basename'], [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    public function store(StoreProjectReviewWebRequest $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $svc = new DevReportsService;

        File::ensureDirectoryExists($svc->storageDirectory());

        $basename = 'review-web-'.now()->format('Y-m-d-His').'-'.Str::random(8).'.md';
        $fullPath = $svc->storageDirectory().'/'.$basename;
        $args = $request->artisanArguments();
        $commandLine = ProjectConsoleJobTracker::commandLine('project:review', array_merge([
            '--report-path' => $basename,
            '--no-cursor-prompts' => true,
        ], $args));

        $user = $request->user();
        $record = $tracker->tryQueue(ProjectConsoleDomain::REVIEW, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->back()
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::REVIEW));
        }

        RunProjectReviewJob::dispatch(
            $user->id,
            $fullPath,
            $args,
            $record->id,
        );

        return redirect()
            ->back()
            ->with('success', 'Génération du rapport démarrée (file d’attente). Le suivi s’affiche ci-dessous.');
    }
}
