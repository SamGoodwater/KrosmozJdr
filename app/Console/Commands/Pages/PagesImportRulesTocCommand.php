<?php

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Support\Cms\RulesHtmlSectionSplitter;
use App\Support\Cms\RulesImportSlugHelper;
use App\Support\Cms\RulesMarkdownCharacteristicKrefAutowrap;
use App\Support\Cms\RulesMarkdownInternalRulesLinkToPageKref;
use App\Support\Cms\RulesMarkdownPlainReferenceToKref;
use App\Support\Cms\RulesTocParser;
use App\Support\Cms\RulesTocSlugIndex;
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
        {--force-content : Remplace le HTML des sections par celui généré depuis les .md (krefs, liens) ; sans ce flag, une section déjà remplie garde son ancien contenu}';

    protected $description = 'Crée/maj pages et sections depuis la table des matières des règles.';

    /**
     * Contenus HTML découpés par numéro TOC (ex: 3.2.2 => liste de blocs).
     *
     * @var array<string, list<array{title: string, html: string}>>
     */
    private array $sectionContentByNumber = [];

    private bool $forceContent = false;

    /** Nombre de sections existantes où le HTML issu des .md n’a pas été appliqué (contenu CMS déjà présent, sans --force-content). */
    private int $skippedExistingSectionBodyFromMarkdown = 0;

    /**
     * Types de référence autorisés dans les shortcodes Markdown.
     *
     * @var array<int, string>
     */
    private array $allowedKrefTypes = ['characteristic', 'entity', 'page', 'pageSection', 'page_section'];

    private ?RulesTocSlugIndex $rulesTocSlugIndex = null;

    public function handle(): int
    {
        $path = (string) ($this->argument('path') ?: base_path('private/game/rules/TABLE_DES_MATIERES.md'));
        $dryRun = (bool) $this->option('dry-run');
        $this->forceContent = (bool) $this->option('force-content');

        if (! is_file($path)) {
            $this->error("Fichier introuvable: {$path}");

            return ArtisanExitCode::FAILURE;
        }

        $tree = RulesTocParser::parse($path);
        if (count($tree) === 0) {
            $this->warn('Aucune hiérarchie détectée dans la table des matières.');

            return ArtisanExitCode::SUCCESS;
        }

        $this->rulesTocSlugIndex = RulesTocSlugIndex::fromTree($tree);
        $rulesRootDirectory = dirname($path);
        $this->sectionContentByNumber = $this->buildSectionContentMap($rulesRootDirectory);
        $this->line(sprintf(
            'Contenus de sections détectés: %d',
            count($this->sectionContentByNumber)
        ));
        if ($this->forceContent) {
            $this->warn('Mode force-content: le contenu existant des sections sera écrasé.');
        } else {
            $this->comment(
                'Sans --force-content, les sections texte déjà remplies en base conservent leur HTML '
                .'(les changements dans les .md — y compris les [[kref:characteristic:…]] — ne sont appliqués au CMS '
                .'qu’avec --force-content).'
            );
        }

        if ($dryRun) {
            $this->info('Mode dry-run: aucun changement en base.');
            $this->printTreePreview($tree);

            return ArtisanExitCode::SUCCESS;
        }

        $creatorId = $this->resolveDefaultCreatorId();
        $this->skippedExistingSectionBodyFromMarkdown = 0;

        DB::beginTransaction();
        try {
            foreach ($tree as $level1) {
                $parent = $this->upsertLevel1Page($level1, $creatorId);

                foreach ($level1['children'] as $level2) {
                    $child = $this->upsertLevel2Page($level2, (int) $parent->id, $creatorId);

                    $expectedSectionSlugs = [];
                    $pageSectionOrder = 0;
                    foreach ($level2['sections'] as $level3) {
                        foreach ($this->upsertLevel3Sections($level3, (int) $child->id, $creatorId, $pageSectionOrder) as $slug) {
                            $expectedSectionSlugs[] = $slug;
                        }
                    }

                    $this->purgeOrphanSectionsOnPage((int) $child->id, $expectedSectionSlugs);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import interrompu: '.$e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        $this->info('Import terminé avec succès.');
        if (! $this->forceContent && $this->skippedExistingSectionBodyFromMarkdown > 0) {
            $this->warn(sprintf(
                '%d section(s) existante(s) : le HTML des fichiers Markdown n’a pas remplacé le contenu déjà '
                .'enregistré. Pour appliquer les .md (références kref, etc.), relance : '
                .'php artisan pages:import-rules-toc --force-content',
                $this->skippedExistingSectionBodyFromMarkdown
            ));
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @param  array{number:string,title:string,menu_order:int,children:array<int, mixed>}  $level1
     */
    private function upsertLevel1Page(array $level1, ?int $creatorId): Page
    {
        $slug = RulesImportSlugHelper::buildPageSlug($level1['number'], $level1['title']);

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
     * @param  array{number:string,title:string,menu_order:int,sections:array<int, mixed>}  $level2
     */
    private function upsertLevel2Page(array $level2, int $parentId, ?int $creatorId): Page
    {
        $slug = RulesImportSlugHelper::buildPageSlug($level2['number'], $level2['title']);

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
     * @param  array{number:string,title:string,order:int}  $level3
     * @return list<string> Slugs créés ou mis à jour
     */
    private function upsertLevel3Sections(array $level3, int $pageId, ?int $creatorId, int &$pageSectionOrder): array
    {
        $baseSlug = $this->buildSectionSlug($level3['number'], $level3['title']);
        $chunks = $this->resolveSectionChunks($level3['number'], $level3['title']);
        $slugs = [];

        foreach ($chunks as $index => $chunk) {
            $chunkTitle = trim($chunk['title']) !== '' ? trim($chunk['title']) : $level3['title'];
            $slug = count($chunks) === 1
                ? $baseSlug
                : ($index === 0 ? $baseSlug : $baseSlug.'-'.Str::slug($chunkTitle));
            $content = trim($chunk['html']);

            $this->upsertTextSectionBySlug(
                $pageId,
                $slug,
                $chunkTitle,
                $content !== '' ? $content : '<p>'.e($chunkTitle).'</p>',
                $pageSectionOrder,
                $creatorId,
                $level3['number'],
            );

            $slugs[] = $slug;
            $pageSectionOrder++;
        }

        return $slugs;
    }

    /**
     * @return list<array{title: string, html: string}>
     */
    private function resolveSectionChunks(string $number, string $fallbackTitle): array
    {
        if (! isset($this->sectionContentByNumber[$number])) {
            return [['title' => $fallbackTitle, 'html' => '<h3>'.e($fallbackTitle).'</h3>']];
        }

        $chunks = $this->sectionContentByNumber[$number];

        return $chunks !== [] ? $chunks : [['title' => $fallbackTitle, 'html' => '<h3>'.e($fallbackTitle).'</h3>']];
    }

    private function upsertTextSectionBySlug(
        int $pageId,
        string $slug,
        string $title,
        string $content,
        int $order,
        ?int $creatorId,
        string $level3Number,
    ): Section {
        $existing = Section::withTrashed()
            ->where('page_id', $pageId)
            ->where('slug', $slug)
            ->first();

        $textSettings = [
            'align' => 'left',
            'size' => 'md',
            'enableRichReferences' => true,
        ];

        $attributes = [
            'page_id' => $pageId,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => $textSettings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            $mergedSettings = array_merge(
                is_array($existing->settings) ? $existing->settings : [],
                $textSettings,
            );
            $attributes['settings'] = $mergedSettings;

            // Respecter un éventuel contenu édité à la main: on ne l'écrase pas.
            $existingData = is_array($existing->data) ? $existing->data : [];
            $existingParams = is_array($existing->params) ? $existing->params : [];
            $hasCustomDataContent = isset($existingData['content']) && trim((string) $existingData['content']) !== '';
            $hasCustomParamsContent = isset($existingParams['content']) && trim((string) $existingParams['content']) !== '';

            if ($this->forceContent) {
                $attributes['data'] = $this->replaceSectionContent($existingData, $content);
                $attributes['params'] = $this->replaceSectionContent($existingParams, $content);
            } else {
                $hasMarkdownForNumber = isset($this->sectionContentByNumber[$level3Number]);
                if ($hasMarkdownForNumber && ($hasCustomDataContent || $hasCustomParamsContent)) {
                    $this->skippedExistingSectionBodyFromMarkdown++;
                }
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

    /**
     * @param  list<string>  $expectedSlugs
     */
    private function purgeOrphanSectionsOnPage(int $pageId, array $expectedSlugs): void
    {
        if ($expectedSlugs === []) {
            return;
        }

        Section::query()
            ->where('page_id', $pageId)
            ->whereNotIn('slug', $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    /**
     * @param  array<string, mixed>  $payload
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
        if (! is_dir($rulesRootDirectory)) {
            return [];
        }

        $rulesRootReal = realpath($rulesRootDirectory) ?: $rulesRootDirectory;

        $contentByNumber = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rulesRootDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $path = (string) $fileInfo->getPathname();

            $basename = pathinfo($path, PATHINFO_BASENAME);
            if (in_array($basename, ['TABLE_DES_MATIERES.md', 'INDEX.md'], true)) {
                continue;
            }

            if (! preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }

            $number = (string) $matches[1];
            $rawMarkdown = file_get_contents($path);
            if (! is_string($rawMarkdown) || trim($rawMarkdown) === '') {
                continue;
            }

            $normalizedMarkdown = $this->stripFirstMarkdownHeading($rawMarkdown);
            $normalizedMarkdown = RulesMarkdownInternalRulesLinkToPageKref::apply(
                $normalizedMarkdown,
                $path,
                $rulesRootReal,
                $this->rulesTocSlugIndex,
            );
            $normalizedMarkdown = RulesMarkdownPlainReferenceToKref::apply(
                $normalizedMarkdown,
                $this->rulesTocSlugIndex,
            );
            $normalizedMarkdown = RulesMarkdownCharacteristicKrefAutowrap::apply($normalizedMarkdown);
            $normalizedMarkdown = $this->replaceKrefShortcodes($normalizedMarkdown);
            $html = trim((string) Str::markdown($normalizedMarkdown));
            if ($html === '') {
                continue;
            }

            $chunks = RulesHtmlSectionSplitter::split($html);
            $contentByNumber[$number] = $chunks !== []
                ? $chunks
                : [['title' => '', 'html' => $html]];
        }

        return $contentByNumber;
    }

    private function stripFirstMarkdownHeading(string $markdown): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $markdown);
        if (! is_array($lines) || count($lines) === 0) {
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
     * Remplace les shortcodes markdown de références riches par des spans `.kref`.
     *
     * Syntaxe :
     * - [[kref:characteristic:action_points_creature|Points d'action]]
     * - [[kref:page:regles-2-2-les-caracteristiques|Caractéristiques]]
     * - [[kref:pageSection:regles-2-2-les-caracteristiques:123|Section cible]] (id numérique)
     * - [[kref:pageSection:regles-2-2-les-caracteristiques@regle-2-2-2-…|Section]] (slug section, scroll + aperçu)
     * - [[kref:entity:spells:42|Boule de feu]]
     */
    private function replaceKrefShortcodes(string $markdown): string
    {
        $pattern = '/\[\[kref:([a-zA-Z_]+):([^\]|]+)(?:\|([^\]]+))?\]\]/u';

        return (string) preg_replace_callback($pattern, function (array $matches): string {
            $rawType = trim((string) ($matches[1] ?? ''));
            $rawTarget = trim((string) ($matches[2] ?? ''));
            $label = trim((string) ($matches[3] ?? ''));

            if ($rawType === '' || $rawTarget === '' || ! in_array($rawType, $this->allowedKrefTypes, true)) {
                return (string) $matches[0];
            }

            $type = $rawType === 'page_section' ? 'pageSection' : $rawType;
            $payload = $this->buildKrefPayload($type, $rawTarget);
            if ($payload === null) {
                return (string) $matches[0];
            }

            $finalLabel = $label !== '' ? $label : $rawTarget;
            $title = $this->encodeKrefTitle($type, $payload, $finalLabel);
            $classes = $this->isKrefNavigable($type) ? 'kref kref--nav' : 'kref';

            return '<span class="'.$classes.'" title="'.e($title).'">'.e($finalLabel).'</span>';
        }, $markdown);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildKrefPayload(string $type, string $target): ?array
    {
        if ($type === 'characteristic') {
            return ['key' => trim($target)];
        }

        if ($type === 'page') {
            return ['pageSlug' => trim($target)];
        }

        if ($type === 'pageSection') {
            if (str_contains($target, '@')) {
                [$pageSlug, $sectionSlug] = array_pad(explode('@', $target, 2), 2, '');
                $pageSlug = trim((string) $pageSlug);
                $sectionSlug = trim((string) $sectionSlug);
                if ($pageSlug === '' || $sectionSlug === '') {
                    return null;
                }

                return ['pageSlug' => $pageSlug, 'sectionSlug' => $sectionSlug];
            }

            [$pageSlug, $sectionId] = array_pad(explode(':', $target, 2), 2, '');
            $pageSlug = trim((string) $pageSlug);
            $sectionId = trim((string) $sectionId);
            if ($pageSlug === '' || $sectionId === '') {
                return null;
            }

            return ['pageSlug' => $pageSlug, 'sectionId' => ctype_digit($sectionId) ? (int) $sectionId : $sectionId];
        }

        if ($type === 'entity') {
            [$entityType, $id] = array_pad(explode(':', $target, 2), 2, '');
            $entityType = trim((string) $entityType);
            $id = trim((string) $id);
            if ($entityType === '' || $id === '') {
                return null;
            }

            return ['entityType' => $entityType, 'id' => ctype_digit($id) ? (int) $id : $id];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function encodeKrefTitle(string $type, array $payload, string $label): string
    {
        $json = json_encode([
            't' => $type,
            'p' => $payload,
            'l' => trim($label),
        ], JSON_UNESCAPED_UNICODE);

        if (! is_string($json) || $json === '') {
            return '';
        }

        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    private function isKrefNavigable(string $type): bool
    {
        return in_array($type, ['entity', 'page', 'pageSection'], true);
    }

    /**
     * @param  array<string, mixed>  $attributes
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

    private function buildSectionSlug(string $number, string $title): string
    {
        return RulesImportSlugHelper::buildSectionSlug($number, $title);
    }

    /**
     * @param  array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, array{number:string,title:string,order:int}>}>}>  $tree
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
