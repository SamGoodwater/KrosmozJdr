<?php

namespace Database\Seeders\Entity;

use App\Console\Concerns\WritesArtisanCommandOutput;
use App\Models\Entity\Capability;
use App\Models\Entity\Specialization;
use App\Models\User;
use App\Services\Entity\LegacyEntitySectionImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Importe des spécialisations depuis des exports HTML statiques (sans réseau).
 *
 * Fichiers : {@code database/seeders/data/legacy-specializations/{slug}.html} (ignorés par Git, à placer en local).
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

        $code = Artisan::call('pages:sync-bibliotheque-entities');
        $this->writeArtisanCommandOutput();
        if ($code !== 0) {
            $this->command?->error('Échec de pages:sync-bibliotheque-entities après import des spécialisations.');
        }
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
        $parsedSections = $importer->parseLegacySections($legacyHtml);
        $specializationCapabilitySync = [];

        $importer->importParsedSections(
            $specialization,
            $page,
            $sectionSlugPrefix,
            $parsedSections,
            $creatorId,
            function (array $capabilityNames, int $legacyLevel) use (&$specializationCapabilitySync): void {
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
            },
        );

        if ($specializationCapabilitySync !== []) {
            $specialization->capabilities()->syncWithoutDetaching($specializationCapabilitySync);
        }
    }
}
