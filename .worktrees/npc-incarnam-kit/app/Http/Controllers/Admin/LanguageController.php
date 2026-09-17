<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLanguageRequest;
use App\Http\Requests\Admin\UpdateLanguageRequest;
use App\Http\Resources\Entity\LanguageResource;
use App\Models\Entity\Language;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Language::class);

        $languages = Language::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/languages/Index', [
            'languages' => LanguageResource::collection($languages)->resolve(request()),
        ]);
    }

    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        Language::create($request->validated());

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue créée.');
    }

    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        $language->update($request->validated());

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue mise à jour.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        $this->authorize('delete', $language);

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Langue supprimée.');
    }
}
