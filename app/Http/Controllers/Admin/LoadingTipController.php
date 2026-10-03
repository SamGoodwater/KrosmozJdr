<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoadingTipRequest;
use App\Http\Requests\Admin\UpdateLoadingTipRequest;
use App\Http\Resources\LoadingTipResource;
use App\Models\LoadingTip;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LoadingTipController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', LoadingTip::class);

        $tips = LoadingTip::query()
            ->orderByDesc('featured')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/loading-tips/Index', [
            'tips' => LoadingTipResource::collection($tips)->resolve(request()),
        ]);
    }

    public function store(StoreLoadingTipRequest $request): RedirectResponse
    {
        LoadingTip::create($request->validated());

        return redirect()->route('admin.loading-tips.index')
            ->with('success', 'Astuce créée.');
    }

    public function update(UpdateLoadingTipRequest $request, LoadingTip $loadingTip): RedirectResponse
    {
        $loadingTip->update($request->validated());

        return redirect()->route('admin.loading-tips.index')
            ->with('success', 'Astuce mise à jour.');
    }

    public function destroy(LoadingTip $loadingTip): RedirectResponse
    {
        $this->authorize('delete', $loadingTip);

        $loadingTip->delete();

        return redirect()->route('admin.loading-tips.index')
            ->with('success', 'Astuce supprimée.');
    }
}
