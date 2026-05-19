<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ChangelogMarkdownService;
use Illuminate\Http\Response;

/**
 * @description Export Markdown assemblé (intro + navigation + fichier `changelog/{semver}.md`) pour le template CMS `legal_markdown`.
 *
 * @example `GET /changelog/feed/1.3.2` — route `changelog.feed`.
 */
final class ChangelogMarkdownFeedController extends Controller
{
    public function __construct(
        private ChangelogMarkdownService $changelogMarkdown,
    ) {}

    public function show(string $version): Response
    {
        if (! $this->changelogMarkdown->isValidVersionSlug($version)) {
            abort(404);
        }

        $markdown = $this->changelogMarkdown->composeFeedMarkdown($version);

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=120',
        ]);
    }
}
