<?php

namespace Database\Seeders\Entity;

use App\Enums\SectionType;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\BibliothequeEntityPageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Importe des spécialisations depuis des exports HTML statiques (sans réseau).
 *
 * Fichiers : {@code database/seeders/data/legacy-specializations/{slug}.html} (ignorés par Git, à placer en local).
 * Régénération : POST {@code https://jdr.iota21.fr/index.php?c=page&a=show} (form {@code url_name} = slug),
 * puis copier le champ {@code html} de la réponse JSON dans le fichier correspondant.
 */
class SpecializationSeeder extends Seeder
{
    private const LEGACY_BASE_URL = 'https://jdr.iota21.fr';

    public function run(): void
    {
        $this->importLegacySpecialization(
            legacySlug: 'erudit',
            specializationName: 'Érudit',
            importPageSlug: 'import-specialization-erudit',
            importPageTitle: 'Import legacy — Spécialisation Érudit',
            sectionSlugPrefix: 'import-erudit',
            shortDescription: "Spécialisation centrée sur le savoir, la magie et l'analyse."
        );

        $this->importLegacySpecialization(
            legacySlug: 'milicien_ne',
            specializationName: 'Milicien·ne',
            importPageSlug: 'import-specialization-milicien-ne',
            importPageTitle: 'Import legacy — Spécialisation Milicien·ne',
            sectionSlugPrefix: 'import-milicien-ne',
            shortDescription: 'Spécialisation axée sur l\'ordre, la protection et le combat structuré.'
        );

        $this->importLegacySpecialization(
            legacySlug: 'voleur_euse',
            specializationName: 'Voleur·euse',
            importPageSlug: 'import-specialization-voleur-euse',
            importPageTitle: 'Import legacy — Spécialisation Voleur·euse',
            sectionSlugPrefix: 'import-voleur-euse',
            shortDescription: 'Spécialisation tournée vers la discrétion, la ruse et la finesse.'
        );

        $this->importLegacySpecialization(
            legacySlug: 'devot',
            specializationName: 'Dévot',
            importPageSlug: 'import-specialization-devot',
            importPageTitle: 'Import legacy — Spécialisation Dévot',
            sectionSlugPrefix: 'import-devot',
            shortDescription: 'Spécialisation liée à la foi, au soutien et aux pouvoirs sacrés.'
        );

        $this->importLegacySpecialization(
            legacySlug: 'artiste',
            specializationName: 'Artiste',
            importPageSlug: 'import-specialization-artiste',
            importPageTitle: 'Import legacy — Spécialisation Artiste',
            sectionSlugPrefix: 'import-artiste',
            shortDescription: 'Spécialisation axée sur la performance, le spectacle et la créativité.'
        );

        $this->importLegacySpecialization(
            legacySlug: 'explorateur_rice',
            specializationName: 'Explorateur·rice',
            importPageSlug: 'import-specialization-explorateur-rice',
            importPageTitle: 'Import legacy — Spécialisation Explorateur·rice',
            sectionSlugPrefix: 'import-explorateur-rice',
            shortDescription: 'Spécialisation orientée découverte, terrain et autonomie.'
        );

        $stats = app(BibliothequeEntityPageService::class)->syncAll();
        $this->command?->info(sprintf(
            'Sous-pages bibliothèque : %d classes, %d spécialisations synchronisées.',
            $stats['breeds'],
            $stats['specializations']
        ));
    }

    /**
     * @param  string  $legacySlug  Slug {@code url_name} de l’ancien site (fichier {@code {slug}.html}).
     * @param  string  $sectionSlugPrefix  Préfixe unique pour les slugs de sections CMS.
     */
    private function importLegacySpecialization(
        string $legacySlug,
        string $specializationName,
        string $importPageSlug,
        string $importPageTitle,
        string $sectionSlugPrefix,
        string $shortDescription,
    ): void {
        $legacyHtml = $this->loadLegacyPageHtml($legacySlug);
        if ($legacyHtml === null) {
            $this->command?->warn("Import spécialisation {$specializationName} ignoré : fichier manquant ou vide (slug: {$legacySlug}).");

            return;
        }

        $creatorId = $this->resolveDefaultCreatorId();

        $specialization = Specialization::query()->firstOrCreate(
            ['name' => $specializationName],
            [
                'short_description' => $shortDescription,
                'description' => '',
                'state' => Specialization::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'created_by' => $creatorId,
            ]
        );

        $page = Page::query()->firstOrCreate(
            ['slug' => $importPageSlug],
            [
                'title' => $importPageTitle,
                'in_menu' => false,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'created_by' => $creatorId,
            ]
        );

        $parsedSections = $this->parseLegacySections($legacyHtml);
        $specializationSectionSync = [];
        $specializationCapabilitySync = [];

        foreach ($parsedSections as $index => $sectionData) {
            $legacyLevel = max(1, (int) ($sectionData['level'] ?? 1));
            $title = (string) ($sectionData['title'] ?? '');
            $contentHtml = (string) ($sectionData['content'] ?? '');
            if ($contentHtml === '') {
                continue;
            }

            $sectionSlug = $sectionSlugPrefix.'-'.Str::slug($title !== '' ? $title : ('section-'.$index)).'-'.($index + 1);
            $section = Section::query()->updateOrCreate(
                ['slug' => $sectionSlug],
                [
                    'page_id' => $page->id,
                    'title' => $title,
                    'order' => $index + 1,
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

            $specializationSectionSync[$section->id] = ['level' => $legacyLevel];

            $capabilityNames = $sectionData['capabilities'] ?? [];
            if (! is_array($capabilityNames)) {
                continue;
            }

            foreach ($capabilityNames as $capabilityName) {
                $capabilityName = trim((string) $capabilityName);
                if ($capabilityName === '') {
                    continue;
                }

                $capability = Capability::query()->where('name', $capabilityName)->first();
                if (! $capability) {
                    continue;
                }

                $specializationCapabilitySync[$capability->id] = ['level' => $legacyLevel];
            }
        }

        if ($specializationSectionSync !== []) {
            $specialization->sections()->sync($specializationSectionSync);
        }

        if ($specializationCapabilitySync !== []) {
            $specialization->capabilities()->syncWithoutDetaching($specializationCapabilitySync);
        }
    }

    private function loadLegacyPageHtml(string $legacySlug): ?string
    {
        $path = database_path("seeders/data/legacy-specializations/{$legacySlug}.html");
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
    private function parseLegacySections(string $html): array
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

    /**
     * @param  list<string>  $capabilityNames
     */
    private function buildCapabilityKrefListHtml(array $capabilityNames): string
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

    private function absolutizeLinks(string $html): string
    {
        return (string) preg_replace_callback('/href="(?!https?:\/\/|#|mailto:)([^"]+)"/i', function (array $match): string {
            $href = ltrim((string) ($match[1] ?? ''), '/');

            return 'href="'.self::LEGACY_BASE_URL.'/'.$href.'"';
        }, $html);
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

    private function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $admin = User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();

        return $admin ? (int) $admin->id : null;
    }
}
