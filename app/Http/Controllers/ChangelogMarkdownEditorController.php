<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateChangelogMarkdownRequest;
use App\Services\ChangelogMarkdownService;
use Illuminate\Http\JsonResponse;

/**
 * Lecture et écriture des fichiers Markdown du journal, pour l’admin de la page.
 *
 * @example GET `/changelog/sources` liste la frise, l’intro et les versions.
 */
final class ChangelogMarkdownEditorController extends Controller
{
    public function __construct(
        private ChangelogMarkdownService $changelogMarkdown,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'files' => $this->changelogMarkdown->editableFiles(),
        ]);
    }

    public function update(UpdateChangelogMarkdownRequest $request, string $name): JsonResponse
    {
        $this->changelogMarkdown->write($name, (string) $request->validated('markdown'));

        return response()->json([
            'name' => $name,
            'saved' => true,
        ]);
    }
}
