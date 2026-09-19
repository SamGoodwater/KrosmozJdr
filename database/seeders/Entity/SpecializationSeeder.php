<?php

namespace Database\Seeders\Entity;

use App\Console\Concerns\WritesArtisanCommandOutput;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Entity\LegacyEntitySectionImportService;
use App\Services\Entity\LegacySpecializationRealignService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Importe des spécialisations depuis des exports HTML statiques (sans réseau)
 * et pose les fiches brouillon manquantes (Artisan·e, Négociant·e, Sylvain·e, Marin·e, Courtisan·e).
 *
 * Fichiers legacy : {@code database/seeders/data/legacy-specializations/{slug}.html}
 * (ignorés par Git, à placer en local).
 * Brouillons : {@code database/seeders/data/draft-specializations.php}.
 */
class SpecializationSeeder extends Seeder
{
    use WritesArtisanCommandOutput;

    public function run(): void
    {
        $importer = app(LegacyEntitySectionImportService::class);

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'erudit',
            specializationName: 'Érudit',
            importPageSlug: 'import-specialization-erudit',
            importPageTitle: 'Import legacy — Spécialisation Érudit',
            sectionSlugPrefix: 'import-erudit',
            shortDescription: "Spécialisation centrée sur le savoir, la magie et l'analyse."
        );

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'milicien_ne',
            specializationName: 'Milicien·ne',
            importPageSlug: 'import-specialization-milicien-ne',
            importPageTitle: 'Import legacy — Spécialisation Milicien·ne',
            sectionSlugPrefix: 'import-milicien-ne',
            shortDescription: 'Spécialisation axée sur l\'ordre, la protection et le combat structuré.'
        );

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'voleur_euse',
            specializationName: 'Voleur·euse',
            importPageSlug: 'import-specialization-voleur-euse',
            importPageTitle: 'Import legacy — Spécialisation Voleur·euse',
            sectionSlugPrefix: 'import-voleur-euse',
            shortDescription: 'Spécialisation tournée vers la discrétion, la ruse et la finesse.'
        );

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'devot',
            specializationName: 'Dévot',
            importPageSlug: 'import-specialization-devot',
            importPageTitle: 'Import legacy — Spécialisation Dévot',
            sectionSlugPrefix: 'import-devot',
            shortDescription: 'Spécialisation liée à la foi, au soutien et aux pouvoirs sacrés.'
        );

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'artiste',
            specializationName: 'Artiste',
            importPageSlug: 'import-specialization-artiste',
            importPageTitle: 'Import legacy — Spécialisation Artiste',
            sectionSlugPrefix: 'import-artiste',
            shortDescription: 'Spécialisation axée sur la performance, le spectacle et la créativité.'
        );

        $this->importLegacySpecialization(
            $importer,
            legacySlug: 'explorateur_rice',
            specializationName: 'Explorateur·rice',
            importPageSlug: 'import-specialization-explorateur-rice',
            importPageTitle: 'Import legacy — Spécialisation Explorateur·rice',
            sectionSlugPrefix: 'import-explorateur-rice',
            shortDescription: 'Spécialisation orientée découverte, terrain et autonomie.'
        );

        $this->seedDraftSpecializations($importer);

        $code = Artisan::call('pages:sync-bibliotheque-entities');
        $this->writeArtisanCommandOutput();
        if ($code !== 0) {
            $this->command?->error('Échec de pages:sync-bibliotheque-entities après import des spécialisations.');
        }
    }

    /**
     * Pose les spécialisations encore en rédaction (absentes du HTML legacy).
     *
     * Rafraîchit les fiches restées à l’état brouillon, mais n’écrase jamais une
     * fiche sortie du brouillon (passée jouable ou retravaillée à la main).
     */
    private function seedDraftSpecializations(LegacyEntitySectionImportService $importer): void
    {
        $path = database_path('seeders/data/draft-specializations.php');
        if (! is_file($path)) {
            $this->command?->warn('Brouillons de spécialisations ignorés : fichier data/draft-specializations.php manquant.');

            return;
        }

        /** @var mixed $drafts */
        $drafts = require $path;
        if (! is_array($drafts)) {
            $this->command?->error('Brouillons de spécialisations ignorés : le fichier data ne retourne pas un tableau.');

            return;
        }

        $renderer = new DraftSpecializationContentRenderer;
        $creatorId = $importer->resolveDefaultCreatorId();
        $created = 0;
        $skipped = 0;

        foreach ($drafts as $draft) {
            if (! is_array($draft)) {
                continue;
            }

            $name = trim((string) ($draft['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $specialization = Specialization::query()->where('name', $name)->first();

            if ($specialization !== null && $specialization->state !== Specialization::STATE_DRAFT) {
                $skipped++;
                $this->command?->info("Spécialisation {$name} sortie du brouillon, mise à jour ignorée.");

                continue;
            }

            $specialization ??= new Specialization;
            $specialization->fill([
                'name' => $name,
                'short_description' => (string) ($draft['shortDescription'] ?? ''),
                'description' => (string) ($draft['description'] ?? ''),
                'state' => Specialization::STATE_DRAFT,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'created_by' => $creatorId,
            ])->save();

            $page = $importer->ensureImportPage(
                (string) ($draft['importPageSlug'] ?? 'import-specialization-draft'),
                (string) ($draft['importPageTitle'] ?? 'Brouillon — Spécialisation '.$name),
                $creatorId,
                Page::STATE_DRAFT,
            );

            $sync = $importer->importParsedSections(
                $specialization,
                $page,
                (string) ($draft['sectionSlugPrefix'] ?? 'draft-specialization'),
                $renderer->sections($draft),
                $creatorId,
                null,
                Section::STATE_DRAFT,
            );

            $this->pruneImportPage($page, $sync);

            $created++;
        }

        $this->command?->info(sprintf(
            'Brouillons de spécialisations : %d écrite(s), %d hors brouillon ignorée(s).',
            $created,
            $skipped,
        ));
    }

    private function importLegacySpecialization(
        LegacyEntitySectionImportService $importer,
        string $legacySlug,
        string $specializationName,
        string $importPageSlug,
        string $importPageTitle,
        string $sectionSlugPrefix,
        string $shortDescription,
    ): void {
        $legacyHtml = $importer->loadLegacyPageHtml(
            LegacyEntitySectionImportService::DATA_SUBDIR_SPECIALIZATIONS,
            $legacySlug,
        );
        if ($legacyHtml === null) {
            $this->command?->warn("Import spécialisation {$specializationName} ignoré : fichier manquant ou vide (slug: {$legacySlug}).");

            return;
        }

        $creatorId = $importer->resolveDefaultCreatorId();

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

        $page = $importer->ensureImportPage($importPageSlug, $importPageTitle, $creatorId);
        $parsedSections = app(LegacySpecializationRealignService::class)->realign(
            $importer->parseLegacySections($legacyHtml),
            $this->realignmentPlan($legacySlug),
        );
        $specializationCapabilitySync = [];

        $sync = $importer->importParsedSections(
            $specialization,
            $page,
            $sectionSlugPrefix,
            $parsedSections,
            $creatorId,
            function (array $capabilityNames, int $palier) use (&$specializationCapabilitySync): void {
                foreach ($capabilityNames as $capabilityName) {
                    $capabilityName = trim((string) $capabilityName);
                    if ($capabilityName === '') {
                        continue;
                    }

                    $capability = Capability::query()->where('name', $capabilityName)->first();
                    if (! $capability) {
                        continue;
                    }

                    $specializationCapabilitySync[$capability->id] = ['level' => $palier];
                }
            },
        );

        $this->pruneImportPage($page, $sync);

        if ($specializationCapabilitySync !== []) {
            $specialization->capabilities()->sync($specializationCapabilitySync);
        }
    }

    /**
     * Supprime les sections de l’ancienne grille de paliers restées sur la page
     * d’import après un redécoupage.
     *
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

    /**
     * Charge le plan de réalignement (quels bonus nommés deviennent des aptitudes).
     *
     * @return array{aptitudes?: array<string, int>, authored?: array<int, array{name: string, html: string}>}
     */
    private function realignmentPlan(string $legacySlug): array
    {
        static $plans = null;

        if ($plans === null) {
            $path = database_path('seeders/data/legacy-specialization-realignment.php');
            /** @var mixed $loaded */
            $loaded = is_file($path) ? require $path : [];
            $plans = is_array($loaded) ? $loaded : [];
        }

        $plan = $plans[$legacySlug] ?? [];

        return is_array($plan) ? $plan : [];
    }
}
