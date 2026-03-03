<?php

declare(strict_types=1);

namespace Database\Seeders\Type;

use App\Models\Type\ResourceType;
use App\Models\User;
use Database\Seeders\Concerns\LoadsSeederDataFile;
use Illuminate\Database\Seeder;

/**
 * Seed des types de ressources depuis database/seeders/data/resource_types.php.
 *
 * Fichier généré par : php artisan scrapping:types:extract
 * Régénéré depuis la BDD par : php artisan scrapping:seeders:export --item-types
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */
class ResourceTypeSeeder extends Seeder
{
    use LoadsSeederDataFile;

    private const DATA_FILE = 'database/seeders/data/resource_types.php';

    public function run(): void
    {
        $path = base_path(self::DATA_FILE);
        if (!is_file($path)) {
            if ($this->command) {
                $this->command->warn('Fichier absent : ' . self::DATA_FILE . ' — exécutez php artisan scrapping:types:extract');
            }

            return;
        }

        $rows = $this->loadDataFile(self::DATA_FILE);
        $systemUser = User::getSystemUser();
        $createdBy = $systemUser?->id;

        foreach ($rows as $row) {
            $typeId = (int) ($row['dofusdb_type_id'] ?? 0);
            if ($typeId <= 0) {
                continue;
            }
            ResourceType::updateOrCreate(
                ['dofusdb_type_id' => $typeId],
                [
                    'name' => (string) ($row['name'] ?? ''),
                    'decision' => (string) ($row['decision'] ?? 'pending'),
                    'state' => (string) ($row['state'] ?? 'draft'),
                    'read_level' => (int) ($row['read_level'] ?? User::ROLE_GUEST),
                    'write_level' => (int) ($row['write_level'] ?? User::ROLE_ADMIN),
                    'created_by' => $row['created_by'] ?? $createdBy,
                ]
            );
        }

        if ($this->command) {
            $this->command->info('✅ ResourceTypeSeeder : ' . count($rows) . ' types.');
        }
    }
}
