<?php

namespace App\Services\Entity;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\Concerns\HasLeveledSections;
use App\Models\Entity\Capability;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Import de sections CMS depuis HTML legacy (classes, spécialisations).
 */
class LegacyEntitySectionImportService
{
    public const LEGACY_BASE_URL = 'https://jdr.iota21.fr';

    public const DATA_SUBDIR_SPECIALIZATIONS = 'legacy-specializations';

    public const DATA_SUBDIR_BREEDS = 'legacy-breeds';

    public function loadLegacyPageHtml(string $dataSubdir, string $legacySlug): ?string
    {
        $path = database_path("seeders/data/{$dataSubdir}/{$legacySlug}.html");
        if (! is_file($path)) {
            return null;
        }

        $html = file_get_contents($path);
        if ($html === false || trim($html) === '') {
            return null;
        }

        return $html;
    }

    /**
     * @return list<array{title: string, level: int, content: string, capabilities?: list<string>}>
     */
    public function parseLegacySections(string $html): array
    {
        preg_match_all(
            '/<section\s+id="section(?<id>[^"]+)"[^>]*data-name="(?<name>[^"]*)"[^>]*data-type="(?<type>[^"]+)"[^>]*data-uniqid="(?<uniq>[^"]+)"[^>]*>/u',
            $html,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $results = [];
        $currentLevel = 1;
        $count = count($matches[0] ?? []);

        for ($i = 0; $i < $count; $i++) {
            $start = (int) $matches[0][$i][1];
            $end = $i + 1 < $count ? (int) $matches[0][$i + 1][1] : strlen($html);
            $chunk = substr($html, $start, $end - $start) ?: '';

            $name = html_entity_decode(str_replace("\\'", "'", (string) $matches['name'][$i][0]));
            $type = (string) $matches['type'][$i][0];
            $uniq = (string) $matches['uniq'][$i][0];
            $title = trim($name !== '' ? $name : 'Section');

            if (preg_match('/Niveau\s+(\d+)/iu', $title, $levelMatch) === 1) {
                $currentLevel = max(1, (int) ($levelMatch[1] ?? 1));
            }

            if ($type === 'text') {
                $content = $this->extractTextSectionContent($chunk, $uniq);
                if ($content === '') {
                    continue;
                }

                $results[] = [
                    'title' => $title,
                    'level' => $currentLevel,
                    'content' => $this->absolutizeLinks($content),
                ];

                continue;
            }

            if ($type === 'modules/capability_list') {
                $capabilities = $this->extractCapabilityNamesFromCards($chunk);
                if ($capabilities === []) {
                    continue;
                }

                $results[] = [
                    'title' => "Aptitudes (niveau {$currentLevel})",
                    'level' => $currentLevel,
                    'content' => $this->buildCapabilityKrefListHtml($capabilities),
                    'capabilities' => $capabilities,
                ];
            }
        }

        return $results;
    }

    /**
     * Crée ou met à jour les sections texte et synchronise la relation pivot.
     *
     * @param  callable(list<string>, int): void|null  $onCapabilityNames
     * @return array<int, array{level: int}>
     */
    public function importParsedSections(
        Model $entity,
        Page $page,
        string $sectionSlugPrefix,
        array $parsedSections,
        ?int $creatorId = null,
        ?callable $onCapabilityNames = null,
    ): array {
        $this->assertHasLeveledSections($entity);

        $creatorId ??= $this->resolveDefaultCreatorId();
        $sync = [];

        foreach ($parsedSections as $index => $sectionData) {
            $legacyLevel = max(1, (int) ($sectionData['level'] ?? 1));
            $title = (string) ($sectionData['title'] ?? '');
            $contentHtml = (string) ($sectionData['content'] ?? '');
            if ($contentHtml === '') {
                continue;
            }

            $sectionSlug = $sectionSlugPrefix.'-'.Str::slug($title !== '' ? $title : ('section-'.$index)).'-'.($index + 1);
            $section = $this->upsertTextSection(
                page: $page,
                slug: $sectionSlug,
                title: $title,
                contentHtml: $contentHtml,
                order: $index + 1,
                creatorId: $creatorId,
            );

            $sync[$section->id] = ['level' => $legacyLevel];

            $capabilityNames = $sectionData['capabilities'] ?? [];
            if ($onCapabilityNames !== null && is_array($capabilityNames) && $capabilityNames !== []) {
                $onCapabilityNames($capabilityNames, $legacyLevel);
            }
        }

        if ($sync !== []) {
            /** @var Model&HasLeveledSections $entity */
            $entity->sections()->sync($sync);
        }

        return $sync;
    }

    public function upsertTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
        int $order = 1,
        ?int $creatorId = null,
    ): Section {
        $creatorId ??= $this->resolveDefaultCreatorId();

        return Section::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'page_id' => $page->id,
                'title' => $title,
                'order' => $order,
                'template' => SectionType::TEXT->value,
                'type' => SectionType::TEXT->value,
                'settings' => ['enableRichReferences' => true],
                'data' => ['content' => $contentHtml],
                'state' => Section::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'created_by' => $creatorId,
            ]
        );
    }

    public function ensureImportPage(string $slug, string $title, ?int $creatorId = null): Page
    {
        $creatorId ??= $this->resolveDefaultCreatorId();

        return Page::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'in_menu' => false,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'created_by' => $creatorId,
            ]
        );
    }

    /**
     * @param  list<string>  $capabilityNames
     */
    public function buildCapabilityKrefListHtml(array $capabilityNames): string
    {
        $items = [];
        foreach ($capabilityNames as $name) {
            $capability = Capability::query()->where('name', $name)->first();
            if ($capability) {
                $title = $this->encodeKrefTitle('entity', [
                    'entityType' => 'capabilities',
                    'id' => $capability->id,
                ], $capability->name);
                $items[] = '<li><span class="kref kref--nav" title="'.e($title).'">'.e($capability->name).'</span></li>';
            } else {
                $items[] = '<li>'.e($name).'</li>';
            }
        }

        return '<p>Aptitudes disponibles pour ce palier :</p><ul>'.implode('', $items).'</ul>';
    }

    public function buildCharacteristicKrefParagraph(string $characteristicKey, string $label, string $suffix = ''): string
    {
        $characteristic = Characteristic::query()->where('key', $characteristicKey)->first();
        $suffixHtml = $suffix !== '' ? ' : '.e($suffix) : '';

        if (! $characteristic) {
            return '<p>'.e($label).$suffixHtml.'</p>';
        }

        $title = $this->encodeKrefTitle('characteristic', ['key' => $characteristicKey], $characteristic->name ?? $label);
        $span = '<span class="kref kref--nav" title="'.e($title).'">'.e($characteristic->name ?? $label).'</span>';

        return '<p>'.e($label).' '.$span.$suffixHtml.'</p>';
    }

    public function wrapPlainTextSection(string $title, string $text): string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }

        if (str_contains($trimmed, '<') && str_contains($trimmed, '>')) {
            return $trimmed;
        }

        return '<p>'.e($trimmed).'</p>';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function encodeKrefTitle(string $type, array $payload, string $label): string
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

    public function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $admin = User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();

        return $admin ? (int) $admin->id : null;
    }

    private function extractTextSectionContent(string $chunk, string $uniq): string
    {
        $pattern = '/<div\s+id="content'.preg_quote($uniq, '/').'">(.*?)<\/div>\s*<\/div>/us';
        if (preg_match($pattern, $chunk, $match) !== 1) {
            return '';
        }

        return trim((string) ($match[1] ?? ''));
    }

    /**
     * @return list<string>
     */
    private function extractCapabilityNamesFromCards(string $chunk): array
    {
        preg_match_all('/<p class="bold">(.*?)<\/p>/us', $chunk, $matches);
        $names = [];
        foreach ($matches[1] ?? [] as $name) {
            $decoded = trim(strip_tags(html_entity_decode((string) $name)));
            if ($decoded === '') {
                continue;
            }
            $names[] = $decoded;
        }

        return array_values(array_unique($names));
    }

    private function absolutizeLinks(string $html): string
    {
        return (string) preg_replace_callback('/href="(?!https?:\/\/|#|mailto:)([^"]+)"/i', function (array $match): string {
            $href = ltrim((string) ($match[1] ?? ''), '/');

            return 'href="'.self::LEGACY_BASE_URL.'/'.$href.'"';
        }, $html);
    }

    private function assertHasLeveledSections(Model $entity): void
    {
        if (! in_array(HasLeveledSections::class, class_uses_recursive($entity), true)) {
            throw new \InvalidArgumentException(
                'L’entité '.get_class($entity).' doit utiliser HasLeveledSections.',
            );
        }
    }
}
