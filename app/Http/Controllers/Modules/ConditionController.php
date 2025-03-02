<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ConditionFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Condition;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ConditionController extends Controller
{
    use AuthorizesRequests;

    public function index(ConditionFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Condition::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $conditions = Condition::paginate($paginationMaxDisplay);

        return Inertia::render('condition.index', [
            'conditions' => $conditions,
        ]);
    }

    public function show(Condition $condition, ConditionFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $condition);

        return Inertia::render('Organisms/Conditions/Show', [
            'resources' => $condition->resources,
            'panoply' => $condition->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Condition::class);

        return Inertia::render('condition.create');
    }

    public function store(ConditionFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Condition::class);

        $data = DataService::extractData($request, new Condition(), [
            [
                'disk' => 'modules',
                'path_name' => 'conditions',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $condition = Condition::create($data);

        event(new NotificationSuperAdminEvent('condition', 'create',  $condition));

        return redirect()->route('condition.show', ['condition' => $condition]);
    }

    public function edit(Condition $condition): \Inertia\Response
    {
        $this->authorize('update', $condition);

        return Inertia::render('condition.edit', [
            'condition' => $condition,
            'resources' => $condition->resources,
            'panoply' => $condition->panoply,
        ]);
    }

    public function update(Condition $condition, ConditionFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $condition);
        $old_condition = $condition;

        $data = DataService::extractData($request, $condition(), [
            [
                'disk' => 'modules',
                'path_name' => 'conditions',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $condition->update($data);

        event(new NotificationSuperAdminEvent('condition', "update", $condition, $old_condition));

        return redirect()->route('condition.show', ['condition' => $condition]);
    }

    public function delete(Condition $condition): RedirectResponse
    {
        $this->authorize('delete', $condition);
        event(new NotificationSuperAdminEvent('condition', "delete", $condition));
        $condition->delete();

        return redirect()->route('condition.index');
    }

    public function forceDelete(Condition $condition): RedirectResponse
    {
        $this->authorize('forceDelete', $condition);

        DataService::deleteFile($condition, 'image');
        event(new NotificationSuperAdminEvent('condition', "forced_delete", $condition));
        $condition->forceDelete();

        return redirect()->route('condition.index');
    }

    public function restore(Condition $condition): RedirectResponse
    {
        $this->authorize('restore', $condition);

        $condition->restore();

        return redirect()->route('condition.index');
    }
}
