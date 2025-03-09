<?php

namespace App\Http\Controllers;

use App\Http\Requests\PageFilterRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;
use App\Events\NotificationSuperAdminEvent;
use Inertia\Inertia;
use App\Http\Resources\PageResource;

class PageController extends Controller
{
    use AuthorizesRequests;

    public function index(PageFilterRequest $request): \Inertia\Response
    {
        // Pas besoin d'autorisation pour la liste
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $pages = Page::where('is_public', true)
            ->with('sections')
            ->orderBy('order_num')
            ->paginate($paginationMaxDisplay);

        return Inertia::render('Pages/Index', [
            'pages' => PageResource::collection($pages),
            'canCreate' => Auth::check() && Auth::user()->can('create', Page::class)
        ]);
    }

    public function show(Page $page): \Inertia\Response
    {
        // Vérifier si la page est publique ou si l'utilisateur a le droit de la voir
        if (!$page->is_public && !Auth::user()?->can('view', $page)) {
            abort(403);
        }

        return Inertia::render('Pages/Show', [
            'page' => new PageResource($page->load('sections')),
            'canEdit' => Auth::check() && Auth::user()->can('update', $page),
            'canDelete' => Auth::check() && Auth::user()->can('delete', $page)
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Page::class);

        $page = new Page();
        return Inertia::render('Pages/Create', [
            'page' => $page,
            'pages' => Page::orderBy('order_num')->pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function store(PageFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $data = DataService::extractData($request, new Page());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $page = Page::create($data);
        $page->sections()?->sync($request->validated('sections'));

        event(new NotificationSuperAdminEvent('page', 'create',  $page));

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été créée');
    }

    public function edit(Page $page): \Inertia\Response
    {
        $this->authorize('update', $page);

        return Inertia::render('Pages/Edit', [
            'page' => $page,
            'pages' => Page::orderBy('order_num')->pluck("name", "is_editable", "is_public", "is_visible", "is_dropdown", "uniqid",)
        ]);
    }

    public function update(Page $page, PageFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $page);
        $old_page = clone $page;

        $data = DataService::extractData($request, $page);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $page->update($data);
        $page->sections()?->sync($request->validated('sections'));

        event(new NotificationSuperAdminEvent('page', "update", $page, $old_page));

        return redirect()->route('pages.show', ['page' => $page])->with('success', 'La page a bien été modifiée');
    }

    public function delete(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);
        event(new NotificationSuperAdminEvent('page', "delete", $page));
        $page->delete();

        return redirect()->route('pages.index')->with('success', 'La page a bien été supprimée');
    }

    public function forcedDelete(Page $page): RedirectResponse
    {
        $this->authorize('forceDelete', $page);

        $page->sections()->detach();
        event(new NotificationSuperAdminEvent('page', "forced_delete", $page));
        $page->forceDelete();

        return redirect()->route('pages.index')->with('success', 'La page a bien été supprimée définitivement');
    }

    public function restore(Page $page): RedirectResponse
    {
        $this->authorize('restore', $page);

        if (!$page->trashed()) {
            return redirect()->route('pages.index')->with('error', 'La page n\'est pas dans la corbeille');
        }
        $page->restore();

        return redirect()->route('pages.index')->with('success', 'La page a bien été restaurée');
    }
}
