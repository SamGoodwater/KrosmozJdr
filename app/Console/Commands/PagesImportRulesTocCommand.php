<?php

namespace App\Console\Commands;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Importe la hiérarchie des règles depuis une table des matières Markdown.
 *
 * Mapping appliqué:
 * - Niveau 1 (##) => page parente
 * - Niveau 2 (###) => sous-page (enfant du niveau 1)
 * - Niveau 3 (liste - x.x.x) => section texte de la page niveau 2
 */
class PagesImportRulesTocCommand extends Command
{
    protected $signature = 'pages:import-rules-toc
        {path? : Chemin du fichier TABLE_DES_MATIERES.md}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-content : Écrase le contenu existant des sections avec les markdown source}';

    protected $description = 'Crée/maj pages et sections depuis la table des matières des règles.';

    /**
     * Map du contenu HTML des règles par numéro (ex: 3.2.2 => "<p>...</p>").
     *
     * @var array<string, string>
     */
    private array $sectionContentByNumber = [];
    private bool $forceContent = false;

    public function handle(): int
    {
        $path = (string) ($this->argument('path') ?: base_path('docs/400- Jeu/420- Règles/TABLE_DES_MATIERES.md'));
        $dryRun = (bool) $this->option('dry-run');
        $this->forceContent = (bool) $this->option('force-content');

        if (!is_file($path)) {
            $this->error("Fichier introuvable: {$path}");
            return self::FAILURE;
        }

        $tree = $this->parseTocFile($path);
        if (count($tree) === 0) {
            $this->warn('Aucune hiérarchie détectée dans la table des matières.');
            return self::SUCCESS;
        }

        $rulesRootDirectory = dirname($path);
        $this->sectionContentByNumber = $this->buildSectionContentMap($rulesRootDirectory);
        $this->line(sprintf(
            'Contenus de sections détectés: %d',
            count($this->sectionContentByNumber)
        ));
        if ($this->forceContent) {
            $this->warn('Mode force-content: le contenu existant des sections sera écrasé.');
        }

        if ($dryRun) {
            $this->info('Mode dry-run: aucun changement en base.');
            $this->printTreePreview($tree);
            return self::SUCCESS;
        }

        $creatorId = $this->resolveDefaultCreatorId();

        DB::beginTransaction();
        try {
            foreach ($tree as $level1) {
                $parent = $this->upsertLevel1Page($level1, $creatorId);

                foreach ($level1['children'] as $level2) {
                    $child = $this->upsertLevel2Page($level2, (int) $parent->id, $creatorId);

                    foreach ($level2['sections'] as $level3) {
                        $this->upsertLevel3Section($level3, (int) $child->id, $creatorId);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import interrompu: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Import terminé avec succès.');
        return self::SUCCESS;
    }

    /**
     * @return array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, array{number:string,title:string,order:int}>}>}>
     */
    private function parseTocFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) {
            return [];
        }

        $level1Items = [];
        $currentLevel1Number = null;
        $currentLevel2Number = null;

        foreach ($lines as $rawLine) {
            $line = trim((string) $rawLine);
            if ($line === '' || Str::startsWith($line, ['---', '# Table'])) {
                continue;
            }

            if (preg_match('/^##\s+(\d+)\.\s+(.+)$/u', $line, $m)) {
                $n1 = (string) $m[1];
                $title = trim((string) $m[2]);
                $currentLevel1Number = $n1;
                $currentLevel2Number = null;

                $level1Items[$n1] = [
                    'number' => $n1,
                    'title' => $title,
                    'menu_order' => (int) $n1,
                    'children' => $level1Items[$n1]['children'] ?? [],
                ];
                continue;
            }

            if (preg_match('/^###\s+(\d+\.\d+)\s+(.+)$/u', $line, $m) && $currentLevel1Number !== null) {
                $n2 = (string) $m[1];
                $title = trim((string) $m[2]);
                $currentLevel2Number = $n2;

                $level1Items[$currentLevel1Number]['children'][$n2] = [
                    'number' => $n2,
                    'title' => $title,
                    'menu_order' => $this->extractSecondLevelOrder($n2),
                    'sections' => $level1Items[$currentLevel1Number]['children'][$n2]['sections'] ?? [],
                ];
                continue;
            }

            // Exemples supportés:
            // - **1.1.1** Concept général
            // - 2.5. Personnalité et historique
            if ($currentLevel1Number !== null && $currentLevel2Number !== null) {
                if (preg_match('/^\-\s*(?:\*\*)?(\d+(?:\.\d+){1,2})\.?(?:\*\*)?\s*(.+)$/u', $line, $m)) {
                    $n3 = (string) $m[1];
                    $title = trim((string) $m[2], " \t\n\r\0\x0B*-");
                    if ($title === '') {
                        continue;
                    }

                    $level1Items[$currentLevel1Number]['children'][$currentLevel2Number]['sections'][] = [
                        'number' => $n3,
                        'title' => $title,
                        'order' => $this->extractThirdLevelOrder($n3),
                    ];
                }
            }
        }

        // Réindexer proprement
        $result = array_values(array_map(function (array $l1): array {
            $l1['children'] = array_values(array_map(function (array $l2): array {
                return $l2;
            }, $l1['children']));
            return $l1;
        }, $level1Items));

        return $result;
    }

    /**
     * @param array{number:string,title:string,menu_order:int,children:array<int, mixed>} $level1
     */
    private function upsertLevel1Page(array $level1, ?int $creatorId): Page
    {
        $slug = $this->buildPageSlug($level1['number'], $level1['title']);

        return $this->upsertPageBySlug($slug, [
            'title' => $level1['title'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'parent_id' => null,
            'menu_order' => $level1['menu_order'],
            'menu_group' => 'Règles',
            'created_by' => $creatorId,
        ]);
    }

    /**
     * @param array{number:string,title:string,menu_order:int,sections:array<int, mixed>} $level2
     */
    private function upsertLevel2Page(array $level2, int $parentId, ?int $creatorId): Page
    {
        $slug = $this->buildPageSlug($level2['number'], $level2['title']);

        return $this->upsertPageBySlug($slug, [
            'title' => $level2['title'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'parent_id' => $parentId,
            'menu_order' => $level2['menu_order'],
            'menu_group' => 'Règles',
            'created_by' => $creatorId,
        ]);
    }

    /**
     * @param array{number:string,title:string,order:int} $level3
     */
    private function upsertLevel3Section(array $level3, int $pageId, ?int $creatorId): Section
    {
        $slug = $this->buildSectionSlug($level3['number'], $level3['title']);
        $content = $this->resolveSectionContent($level3['number'], $level3['title']);

        $existing = Section::withTrashed()
            ->where('page_id', $pageId)
            ->where('slug', $slug)
            ->first();

        $attributes = [
            'page_id' => $pageId,
            'title' => $level3['title'],
            'slug' => $slug,
            'order' => $level3['order'],
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => ['align' => 'left', 'size' => 'md'],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            // Respecter un éventuel contenu édité à la main: on ne l'écrase pas.
            $existingData = is_array($existing->data) ? $existing->data : [];
            $existingParams = is_array($existing->params) ? $existing->params : [];
            $hasCustomDataContent = isset($existingData['content']) && trim((string) $existingData['content']) !== '';
            $hasCustomParamsContent = isset($existingParams['content']) && trim((string) $existingParams['content']) !== '';

            if ($this->forceContent) {
                $attributes['data'] = $this->replaceSectionContent($existingData, $content);
                $attributes['params'] = $this->replaceSectionContent($existingParams, $content);
            } else {
                $attributes['data'] = $hasCustomDataContent ? $existingData : ['content' => $content];
                $attributes['params'] = $hasCustomParamsContent ? $existingParams : ['content' => $content];
            }

            $existing->fill($attributes);
            $existing->save();

            return $existing;
        }

        $attributes['data'] = ['content' => $content];
        $attributes['params'] = ['content' => $content];

        return Section::create($attributes);
    }

    private function resolveSectionContent(string $number, string $title): string
    {
        if (isset($this->sectionContentByNumber[$number])) {
            $content = trim($this->sectionContentByNumber[$number]);
            if ($content !== '') {
                return $content;
            }
        }

        return '<h3>' . e($title) . '</h3>';
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function replaceSectionContent(array $payload, string $content): array
    {
        $payload['content'] = $content;
        return $payload;
    }

    /**
     * Construit la map des contenus des sections à partir des fichiers markdown.
     *
     * @return array<string, string>
     */
    private function buildSectionContentMap(string $rulesRootDirectory): array
    {
        if (!is_dir($rulesRootDirectory)) {
            return [];
        }

        $contentByNumber = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rulesRootDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $path = (string) $fileInfo->getPathname();

            $basename = pathinfo($path, PATHINFO_BASENAME);
            if (in_array($basename, ['TABLE_DES_MATIERES.md', 'INDEX.md'], true)) {
                continue;
            }

            if (!preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }

            $number = (string) $matches[1];
            $rawMarkdown = file_get_contents($path);
            if (!is_string($rawMarkdown) || trim($rawMarkdown) === '') {
                continue;
            }

            $normalizedMarkdown = $this->stripFirstMarkdownHeading($rawMarkdown);
            $html = trim((string) Str::markdown($normalizedMarkdown));
            if ($html === '') {
                continue;
            }

            $contentByNumber[$number] = $html;
        }

        return $contentByNumber;
    }

    private function stripFirstMarkdownHeading(string $markdown): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $markdown);
        if (!is_array($lines) || count($lines) === 0) {
            return $markdown;
        }

        $firstNonEmptyIndex = null;
        foreach ($lines as $index => $line) {
            if (trim((string) $line) !== '') {
                $firstNonEmptyIndex = $index;
                break;
            }
        }

        if ($firstNonEmptyIndex !== null) {
            $firstLine = trim((string) $lines[$firstNonEmptyIndex]);
            if (preg_match('/^#\s+/u', $firstLine)) {
                unset($lines[$firstNonEmptyIndex]);
                if (isset($lines[$firstNonEmptyIndex + 1]) && trim((string) $lines[$firstNonEmptyIndex + 1]) === '') {
                    unset($lines[$firstNonEmptyIndex + 1]);
                }
            }
        }

        return implode(PHP_EOL, array_values($lines));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function upsertPageBySlug(string $slug, array $attributes): Page
    {
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }
            $page->fill($attributes);
            $page->slug = $slug;
            $page->save();
            return $page;
        }

        $attributes['slug'] = $slug;
        return Page::create($attributes);
    }

    private function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $superAdmin = User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->first();
        if ($superAdmin) {
            return (int) $superAdmin->id;
        }

        $firstUser = User::query()->orderBy('id')->first();
        return $firstUser ? (int) $firstUser->id : null;
    }

    private function buildPageSlug(string $number, string $title): string
    {
        $normalizedNumber = str_replace('.', '-', trim($number));
        return Str::slug("regles-{$normalizedNumber}-{$title}");
    }

    private function buildSectionSlug(string $number, string $title): string
    {
        $normalizedNumber = str_replace('.', '-', trim($number));
        return Str::slug("regle-{$normalizedNumber}-{$title}");
    }

    private function extractSecondLevelOrder(string $number): int
    {
        $parts = explode('.', $number);
        return isset($parts[1]) ? (int) $parts[1] : 0;
    }

    private function extractThirdLevelOrder(string $number): int
    {
        $parts = explode('.', $number);
        if (isset($parts[2])) {
            return (int) $parts[2];
        }
        if (isset($parts[1])) {
            return (int) $parts[1];
        }
        return 0;
    }

    /**
     * @param array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, array{number:string,title:string,order:int}>}>}> $tree
     */
    private function printTreePreview(array $tree): void
    {
        foreach ($tree as $l1) {
            $this->line("N1 {$l1['number']} - {$l1['title']}");
            foreach ($l1['children'] as $l2) {
                $this->line("  N2 {$l2['number']} - {$l2['title']}");
                foreach ($l2['sections'] as $l3) {
                    $this->line("    N3 {$l3['number']} - {$l3['title']}");
                }
            }
        }
    }
}

