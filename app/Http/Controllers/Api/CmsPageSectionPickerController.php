<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * Recherche de pages et sections pour mentions riches (éditeur TipTap).
 */
class CmsPageSectionPickerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', $request->query('search', '')));
        $limit = min(max((int) $request->query('limit', 24), 1), 60);

        $pagesOut = [];
        $sectionsOut = [];

        $pageQuery = Page::query()->orderBy('title')->limit($limit);
        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $pageQuery->where('title', 'like', $like);
        }
        /** @var Collection<int, Page> $pageCandidates */
        $pageCandidates = $pageQuery->get()->filter(fn (Page $p) => Gate::forUser($user)->allows('view', $p))->take(15);

        foreach ($pageCandidates as $page) {
            $pagesOut[] = [
                'kind' => 'page',
                'pageSlug' => $page->slug,
                'title' => $page->title,
                'href' => route('pages.show', $page->slug),
            ];
        }

        $sectionQuery = Section::query()
            ->with(['page:id,title,slug'])
            ->orderBy('title')
            ->limit($limit * 2);
        if ($q !== '') {
            $like = '%'.addcslashes($q, '%_\\').'%';
            $sectionQuery->where('title', 'like', $like);
        }

        $sectionCandidates = $sectionQuery->get()->filter(function (Section $section) use ($user) {
            if (! $section->page) {
                return false;
            }

            return Gate::forUser($user)->allows('view', $section->page)
                && Gate::forUser($user)->allows('view', $section);
        })->take($limit);

        foreach ($sectionCandidates as $section) {
            $page = $section->page;
            if (! $page) {
                continue;
            }
            $sectionsOut[] = [
                'kind' => 'pageSection',
                'pageSlug' => $page->slug,
                'sectionId' => $section->id,
                'sectionTitle' => $section->title,
                'pageTitle' => $page->title,
                'href' => route('pages.show', $page->slug).'#section-'.$section->id,
                'anchorId' => 'section-'.$section->id,
            ];
        }

        return response()->json([
            'pages' => $pagesOut,
            'sections' => $sectionsOut,
        ]);
    }
}
