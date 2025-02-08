<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ResourcetypeFilterRequest;
use App\Models\Modules\Resourcetype;
use App\Events\NotificationSuperAdminEvent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ResourcetypeController extends Controller
{
    use AuthorizesRequests;

    public function index(ResourcetypeFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Resourcetype::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $resourcetypes = Resourcetype::paginate($paginationMaxDisplay);

        return Inertia::render('resourcetype.index', [
            'resourcetypes' => $resourcetypes,
        ]);
    }

    public function show(Resourcetype $resourcetype, ResourcetypeFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $resourcetype);

        return Inertia::render('Organisms/Resourcetypes/Show', [
            'resources' => $resourcetype->resources()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Resourcetype::class);

        return Inertia::render('resourcetype.create');
    }

    public function store(ResourcetypeFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Resourcetype::class);

        $data = DataService::extractData($request, new Resourcetype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $resourcetype = Resourcetype::create($data);

        event(new NotificationSuperAdminEvent('resourcetype', 'create',  $resourcetype));

        return redirect()->route('resourcetype.show', ['resourcetype' => $resourcetype]);
    }

    public function edit(Resourcetype $resourcetype): \Inertia\Response
    {
        $this->authorize('update', $resourcetype);

        return Inertia::render('resourcetype.edit', [
            'resourcetype' => $resourcetype
        ]);
    }

    public function update(Resourcetype $resourcetype, ResourcetypeFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $resourcetype);
        $old_resourcetype = clone $resourcetype;

        $data = DataService::extractData($request, $resourcetype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $resourcetype->update($data);

        event(new NotificationSuperAdminEvent('resourcetype', "update", $resourcetype, $old_resourcetype));

        return redirect()->route('resourcetype.show', ['resourcetype' => $resourcetype]);
    }

    public function delete(Resourcetype $resourcetype): RedirectResponse
    {
        $this->authorize('delete', $resourcetype);
        event(new NotificationSuperAdminEvent('resourcetype', "delete", $resourcetype));
        $resourcetype->delete();

        return redirect()->route('resourcetype.index');
    }

    public function forceDelete(Resourcetype $resourcetype): RedirectResponse
    {
        $this->authorize('forceDelete', $resourcetype);
        event(new NotificationSuperAdminEvent('resourcetype', "forced_delete", $resourcetype));
        $resourcetype->forceDelete();

        return redirect()->route('resourcetype.index');
    }

    public function restore(Resourcetype $resourcetype): RedirectResponse
    {
        $this->authorize('restore', $resourcetype);

        $resourcetype->restore();

        return redirect()->route('resourcetype.index');
    }
}
