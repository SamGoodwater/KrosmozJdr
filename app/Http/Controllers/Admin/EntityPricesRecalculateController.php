<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SharesProjectConsoleJob;
use App\Http\Controllers\Controller;
use App\Jobs\RunEntityPricesRecalculateJob;
use App\Services\Project\ProjectConsoleJobTracker;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Lance le recalcul des prix kamas depuis la gestion du contenu.
 */
class EntityPricesRecalculateController extends Controller
{
    use SharesProjectConsoleJob;

    public function store(Request $request, ProjectConsoleJobTracker $tracker): RedirectResponse
    {
        $user = $request->user();
        if ($user === null || ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['items', 'consumables'])],
        ]);
        $type = $validated['type'];

        $commandLine = ProjectConsoleJobTracker::commandLine('entities:recalculate-prices', ['type' => $type]);
        $record = $tracker->tryQueue(ProjectConsoleDomain::ENTITY_PRICES, $commandLine, $user->id);
        if ($record === null) {
            return redirect()
                ->route('admin.content.dashboard.index')
                ->with('error', ProjectConsoleDomain::busyMessage(ProjectConsoleDomain::ENTITY_PRICES));
        }

        Log::info('admin.entity_prices.dispatched', [
            'user_id' => $user->id,
            'type' => $type,
            'ip' => $request->ip(),
            'console_job_id' => $record->id,
        ]);

        RunEntityPricesRecalculateJob::dispatch($user->id, $type, $record->id);

        $label = $type === 'items' ? 'équipements' : 'consommables';

        return redirect()
            ->route('admin.content.dashboard.index')
            ->with('success', 'Recalcul des prix des '.$label.' planifié. Un worker doit exécuter la file.');
    }
}
