<?php

declare(strict_types=1);

namespace App\Services\Project;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Models\DofusdbEffectMapping;
use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Type\ItemType;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/**
 * Contrôles post-initialisation (socle seedé utilisable sans scrapping complet).
 */
final class ProjectInitVerifyService
{
    /** @var list<string> */
    private array $failures = [];

    /** @var list<string> */
    private array $warnings = [];

    /**
     * @return array{ok: bool, failures: list<string>, warnings: list<string>}
     */
    public function verify(bool $withRules = false, int $minSpells = 0): array
    {
        $this->failures = [];
        $this->warnings = [];

        $this->checkTable('pages', function (): void {
            $this->checkCriticalPages();
            $this->checkNavMenu();
        });
        $this->checkTable('users', fn () => $this->checkSuperAdmin());
        $this->checkTable('item_types', fn () => $this->checkItemTypes());
        $this->checkTable('characteristics', fn () => $this->checkCharacteristics());
        $this->checkTable('dofusdb_effect_mappings', fn () => $this->checkEffectMappings());

        if ($withRules) {
            $this->checkRulesPages();
        }

        if ($minSpells > 0 && Schema::hasTable('spells')) {
            $count = (int) Spell::query()->count();
            if ($count < $minSpells) {
                $this->warnings[] = "Sorts en base : {$count} (< {$minSpells} attendu).";
            }
        }

        if (! Schema::hasTable('breeds')) {
            $this->warnings[] = 'Table breeds absente (scrapping non exécuté ?).';
        } elseif (Breed::query()->count() === 0) {
            $this->warnings[] = 'Aucune classe (breed) — normal si --skip-scrapping.';
        }

        return [
            'ok' => $this->failures === [],
            'failures' => $this->failures,
            'warnings' => $this->warnings,
        ];
    }

    private function checkTable(string $table, callable $checker): void
    {
        if (! Schema::hasTable($table)) {
            $this->failures[] = "Table manquante : {$table}.";

            return;
        }
        $checker();
    }

    private function checkCriticalPages(): void
    {
        $required = [
            'accueil' => 'Accueil',
            'legales' => 'Légales',
            'cgu' => 'CGU',
            'changelog' => 'Changelog',
        ];

        foreach ($required as $slug => $label) {
            if (! Page::query()->where('slug', $slug)->exists()) {
                $this->failures[] = "Page critique manquante : {$slug} ({$label}).";
            }
        }

        $home = Page::query()->where('slug', 'accueil')->first();
        if ($home !== null) {
            if ($home->sections()->count() < 1) {
                $this->failures[] = 'Page accueil sans section texte.';
            }
            if (! $home->in_menu) {
                $this->failures[] = 'Page accueil absente du menu (in_menu=false).';
            }
            if (! $home->isPlayable()) {
                $this->failures[] = 'Page accueil non publiée (state≠playable).';
            }
        }
    }

    private function checkNavMenu(): void
    {
        $bibliotheques = config('nav_menu.bibliotheques', []);
        if (! is_array($bibliotheques) || $bibliotheques === []) {
            $this->failures[] = 'Config nav_menu.bibliotheques vide — vérifier config/nav_menu.php et NavMenuSeeder.';

            return;
        }

        foreach (['route', 'entity_key'] as $requiredKey) {
            foreach ($bibliotheques as $index => $entry) {
                if (! is_array($entry) || empty($entry[$requiredKey])) {
                    $this->failures[] = "Entrée bibliothèques menu invalide (index {$index}, clé {$requiredKey} manquante).";

                    return;
                }
            }
        }

        $menuPages = Page::query()
            ->where('in_menu', true)
            ->where('state', Page::STATE_PLAYABLE)
            ->count();
        if ($menuPages < 3) {
            $this->warnings[] = "Peu de pages menu publiées ({$menuPages}) — CriticalPagesSeeder / PageSeeder exécutés ?";
        }
    }

    private function checkSuperAdmin(): void
    {
        $hasSuper = User::query()->where('role', User::ROLE_SUPER_ADMIN)->exists();
        if (! $hasSuper) {
            $this->warnings[] = 'Aucun super_admin — exécuter project:super-admin ou le prompt init.';
        }
    }

    private function checkItemTypes(): void
    {
        $count = ItemType::query()->count();
        if ($count < 5) {
            $this->failures[] = "Types d'équipement insuffisants ({$count}) — lancer TypeSeeder / scrapping:types:seed.";
        }
    }

    private function checkCharacteristics(): void
    {
        $master = Characteristic::query()->count();
        if ($master < 50) {
            $this->failures[] = "Caractéristiques maîtres insuffisantes ({$master}).";
        }

        $creature = CharacteristicCreature::query()->count();
        $object = CharacteristicObject::query()->count();
        $spell = CharacteristicSpell::query()->count();

        if ($creature < 50 || $object < 50 || $spell < 50) {
            $this->failures[] = "Pivots caractéristiques incomplets (creature={$creature}, object={$object}, spell={$spell}).";
        }
    }

    private function checkEffectMappings(): void
    {
        if (DofusdbEffectMapping::query()->count() < 1) {
            $this->failures[] = 'Aucun mapping effet DofusDB (DofusdbEffectMappingSeeder).';
        }
    }

    private function checkRulesPages(): void
    {
        $rulesCount = Page::query()->where('slug', 'like', 'regles-%')->count();
        if ($rulesCount < 5) {
            $this->failures[] = "Pages règles CMS insuffisantes ({$rulesCount}) — lancer project:data:import-rules-toc.";
        }
    }
}
