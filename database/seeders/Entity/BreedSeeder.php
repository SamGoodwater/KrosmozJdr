<?php

namespace Database\Seeders\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\Page;
use App\Models\Section;
use App\Services\Entity\LegacyEntitySectionImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Reconstruit les sections CMS des classes depuis les fiches rédigées
 * ({@code database/seeders/data/playable-breeds.php}), sinon HTML legacy
 * ou colonnes scrappées ({@code specificity}, {@code life_dice}, {@code evolution}).
 *
 * Les fiches rédigées sont toujours rafraîchies (plus de dump « Capacités disponibles »).
 */
class BreedSeeder extends Seeder
{
    public function run(): void
    {
        $importer = app(LegacyEntitySectionImportService::class);
        $authored = $this->loadAuthoredSheets();
        $synced = 0;
        $skipped = 0;

        Breed::query()->orderBy('name')->each(function (Breed $breed) use ($importer, $authored, &$synced, &$skipped): void {
            $sheet = $authored[$breed->name] ?? null;
            if (is_array($sheet)) {
                $this->seedAuthoredClassSheet($importer, $breed, $sheet);
                $synced++;

                return;
            }

            if ($breed->sections()->exists()) {
                $skipped++;

                return;
            }

            $legacySlug = $this->resolveLegacySlug($breed);
            $legacyHtml = $importer->loadLegacyPageHtml(
                LegacyEntitySectionImportService::DATA_SUBDIR_BREEDS,
                $legacySlug,
            );

            if ($legacyHtml !== null) {
                $this->importFromLegacyHtml($importer, $breed, $legacySlug, $legacyHtml);
            } else {
                $this->rebuildSectionsFromColumns($importer, $breed);
            }

            $synced++;
        });

        $this->command?->info(sprintf(
            'BreedSeeder : %d classe(s) avec sections, %d déjà liée(s) (ignorées).',
            $synced,
            $skipped,
        ));

        $code = Artisan::call('pages:sync-bibliotheque-entities');
        if ($code !== 0 && $this->command !== null) {
            $this->command->error('Échec de pages:sync-bibliotheque-entities après BreedSeeder.');
        }
    }

    private function importFromLegacyHtml(
        LegacyEntitySectionImportService $importer,
        Breed $breed,
        string $legacySlug,
        string $legacyHtml,
    ): void {
        $creatorId = $importer->resolveDefaultCreatorId();
        $pageSlug = 'import-breed-'.$legacySlug;
        $page = $importer->ensureImportPage(
            $pageSlug,
            'Import legacy — Classe '.$breed->name,
            $creatorId,
        );

        $parsed = $importer->parseLegacySections($legacyHtml);
        $capabilitySync = [];

        $importer->importParsedSections(
            $breed,
            $page,
            'import-breed-'.$legacySlug,
            $parsed,
            $creatorId,
            function (array $capabilityNames, int $level) use (&$capabilitySync): void {
                foreach ($capabilityNames as $capabilityName) {
                    $capabilityName = trim((string) $capabilityName);
                    if ($capabilityName === '') {
                        continue;
                    }

                    $capability = Capability::query()->where('name', $capabilityName)->first();
                    if ($capability) {
                        $capabilitySync[$capability->id] = [];
                    }
                }
            },
        );

        if ($capabilitySync !== []) {
            $breed->capabilities()->syncWithoutDetaching($capabilitySync);
        }
    }

    private function rebuildSectionsFromColumns(LegacyEntitySectionImportService $importer, Breed $breed): void
    {
        $creatorId = $importer->resolveDefaultCreatorId();
        $prefix = 'breed-'.$breed->id;
        $page = $importer->ensureImportPage(
            $prefix,
            'Sections — Classe '.$breed->name,
            $creatorId,
        );

        $parsed = [];
        $level = 1;
        $order = 1;

        $specificity = trim((string) ($breed->specificity ?? ''));
        if ($specificity !== '') {
            $content = $importer->wrapPlainTextSection('Spécificité', $specificity);
            if ($content !== '') {
                $parsed[] = ['title' => 'Spécificité', 'level' => $level++, 'content' => $content];
            }
        }

        $lifeDice = trim((string) ($breed->life_dice ?? ''));
        if ($lifeDice !== '') {
            $parsed[] = [
                'title' => 'Dé de vie',
                'level' => $level++,
                'content' => $importer->buildCharacteristicKrefParagraph('life_dice_creature', 'Dé de vie', $lifeDice),
            ];
        }

        $evolution = trim((string) ($breed->evolution ?? ''));
        if ($evolution !== '' && ! $this->isVisuallyEmptyHtml($evolution)) {
            $content = $importer->wrapPlainTextSection('Évolution', $evolution);
            if ($content !== '') {
                $parsed[] = ['title' => 'Évolution', 'level' => $level++, 'content' => $content];
            }
        }

        if ($parsed === []) {
            return;
        }

        $sync = [];
        foreach ($parsed as $index => $sectionData) {
            $section = $importer->upsertTextSection(
                page: $page,
                slug: $prefix.'-'.Str::slug((string) $sectionData['title']).'-'.($index + 1),
                title: (string) $sectionData['title'],
                contentHtml: (string) $sectionData['content'],
                order: $order++,
                creatorId: $creatorId,
            );
            $sync[$section->id] = ['level' => max(1, (int) ($sectionData['level'] ?? 1))];
        }

        if ($sync !== []) {
            $breed->sections()->sync($sync);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadAuthoredSheets(): array
    {
        $path = database_path('seeders/data/playable-breeds.php');
        if (! is_file($path)) {
            return [];
        }

        /** @var mixed $sheets */
        $sheets = require $path;
        if (! is_array($sheets)) {
            return [];
        }

        $byName = [];
        foreach ($sheets as $sheet) {
            if (! is_array($sheet)) {
                continue;
            }
            $name = trim((string) ($sheet['name'] ?? ''));
            if ($name !== '') {
                $byName[$name] = $sheet;
            }
        }

        return $byName;
    }

    /**
     * @param  array<string, mixed>  $sheet
     */
    private function seedAuthoredClassSheet(
        LegacyEntitySectionImportService $importer,
        Breed $breed,
        array $sheet,
    ): void {
        $creatorId = $importer->resolveDefaultCreatorId();
        $slug = $this->resolveLegacySlug($breed);
        $page = $importer->ensureImportPage(
            'import-breed-'.$slug,
            'Classe '.$breed->name,
            $creatorId,
            Page::STATE_PLAYABLE,
        );
        $page->fill([
            'title' => 'Classe '.$breed->name,
            'state' => Page::STATE_PLAYABLE,
        ])->save();

        $sync = $importer->importParsedSections(
            $breed,
            $page,
            'import-breed-'.$slug,
            (new ClassSheetContentRenderer)->sections($sheet),
            $creatorId,
            null,
            Section::STATE_PLAYABLE,
        );

        $this->pruneImportPage($page, $sync);
    }

    /**
     * @param  array<int, array{level: int}>  $sync
     */
    private function pruneImportPage(Page $page, array $sync): void
    {
        if ($sync === []) {
            return;
        }

        Section::query()
            ->where('page_id', $page->id)
            ->whereNotIn('id', array_keys($sync))
            ->delete();
    }

    private function resolveLegacySlug(Breed $breed): string
    {
        $name = Str::slug((string) $breed->name);

        return $name !== '' ? $name : 'classe-'.$breed->id;
    }

    private function isVisuallyEmptyHtml(string $html): bool
    {
        $text = trim(strip_tags($html));

        return $text === '';
    }
}
