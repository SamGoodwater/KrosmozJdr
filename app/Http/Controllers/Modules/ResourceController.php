<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ResourceFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Resource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ResourceController extends Controller
{
    use AuthorizesRequests;

    public function index(ResourceFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Resource::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $resources = Resource::paginate($paginationMaxDisplay);

        return Inertia::render('resource.index', [
            'resources' => $resources,
        ]);
    }

    public function show(Resource $resource, ResourceFilterRequest $request): \Inertia\Response
    {
        $this->authorize('view', $resource);

        return Inertia::render('Resources/Show', [
            'resources' => $resource->resources,
            'panoply' => $resource->panoply,
            'type' => $resource->type()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Resource::class);

        return Inertia::render('resource.create');
    }

    public function store(ResourceFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Resource::class);

        $data = DataService::extractData($request, new Resource(), [
            [
                'disk' => 'modules',
                'path_name' => 'resources',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $resource = Resource::create($data);

        event(new NotificationSuperAdminEvent('resource', 'create',  $resource));

        return redirect()->route('resource.show', ['resource' => $resource]);
    }

    public function edit(Resource $resource): \Inertia\Response
    {
        $this->authorize('update', $resource);

        return Inertia::render('resource.edit', [
            'resource' => $resource,
            'resources' => $resource->resources,
            'panoply' => $resource->panoply,
            'type' => $resource->type()
        ]);
    }

    public function update(Resource $resource, ResourceFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $resource);
        $old_resource = $resource;

        $data = DataService::extractData($request, new Resource(), [
            [
                'disk' => 'modules',
                'path_name' => 'resources',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $resource->update($data);

        event(new NotificationSuperAdminEvent('resource', "update", $resource, $old_resource));

        return redirect()->route('resource.show', ['resource' => $resource]);
    }

    public function delete(Resource $resource): RedirectResponse
    {
        $this->authorize('delete', $resource);
        event(new NotificationSuperAdminEvent('resource', "delete", $resource));
        $resource->delete();

        return redirect()->route('resource.index');
    }

    public function forceDelete(Resource $resource): RedirectResponse
    {
        $this->authorize('forceDelete', $resource);

        DataService::deleteFile($resource, 'image');
        event(new NotificationSuperAdminEvent('resource', "forced_delete", $resource));
        $resource->forceDelete();

        return redirect()->route('resource.index');
    }

    public function restore(Resource $resource): RedirectResponse
    {
        $this->authorize('restore', $resource);

        $resource->restore();

        return redirect()->route('resource.index');
    }
}
