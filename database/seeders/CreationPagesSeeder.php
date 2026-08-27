<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Services\PageService;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Database\Seeder;

/**
 * Atelier MJ « Création » : hub Pour les MJ, tableau des bonus d’équipement,
 * chartes (créatures / objets / sorts) reparentées depuis Contribution.
 *
 * Prérequis : seeders des caractéristiques et pivots (norms_grid) exécutés avant
 * (ex. {@see CreatureCharacteristicSeeder}, {@see ObjectCharacteristicSeeder}, {@see SpellCharacteristicSeeder}).
 */
class CreationPagesSeeder extends Seeder
{
    /** @var list<array{title: string, slug: string, icon: string, group: string, entity: string, intro: string}> */
    private const CHARTE_PAGES = [
        [
            'title' => 'Créatures',
            'slug' => 'contribution-creatures',
            'icon' => 'fa-solid fa-dragon',
            'group' => 'creature',
            'entity' => '*',
            'intro' => '<h2>Chartes des caractéristiques — Créatures</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les caractéristiques des créatures '
                .'(monstres, classes jouables, PNJ). Chaque charte définit les valeurs attendues selon le <strong>niveau</strong> (1–20) '
                .'et la <strong>puissance</strong> (très faible → très forte).</p>'
                .'<p>Utilise ces chartes pour vérifier qu’une créature est équilibrée par rapport aux références du jeu. '
                .'Les conditions de lecture permettent d’ajuster la ligne de puissance ou le niveau en fonction d’autres caractéristiques.</p>',
        ],
        [
            'title' => 'Objets',
            'slug' => 'contribution-objets',
            'icon' => 'fa-solid fa-shield-halved',
            'group' => 'object',
            'entity' => '*',
            'intro' => '<h2>Chartes des caractéristiques — Objets</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les bonus d’équipement. '
                .'Les valeurs sont calibrées selon les <strong>règles 5.2.4</strong> (Équipements et panoplies) : '
                .'+1-2 aux niveaux 1-5, +2-3 aux niveaux 6-10, +3-4 aux niveaux 11-15, +4-5 aux niveaux 16-20.</p>'
                .'<p>Un objet dont les bonus dépassent significativement la ligne « neutre » de sa charte est potentiellement '
                .'déséquilibré. Les objets rares ou légendaires peuvent atteindre la ligne « fort » ou « très fort ».</p>',
        ],
        [
            'title' => 'Sorts',
            'slug' => 'contribution-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'group' => 'spell',
            'entity' => '*',
            'intro' => '<h2>Chartes des caractéristiques — Sorts</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les effets de sorts. '
                .'Les dégâts, soins et boucliers sont calibrés selon les <strong>règles 5.2.3</strong> (Sorts et aptitudes), '
                .'avec une progression de ~1d6 (niveau 1) à ~5d6+mod (niveau 20).</p>'
                .'<p>Les conditions de lecture prennent en compte le coût en PA et la zone d’effet : '
                .'un sort coûteux (5+ PA) peut avoir des dégâts supérieurs, tandis qu’un sort en zone (≥2 cases) '
                .'devrait avoir des dégâts réduits par cible.</p>',
        ],
    ];

    /**
     * Anciennes sous-pages (par type d’entité ou catalogues `creation-*` doublons).
     *
     * @var list<string>
     */
    private const DEPRECATED_CHILD_SLUGS = [
        'creation-monstres',
        'creation-equipement',
        'creation-ressources',
        'creation-consommables',
        'creation-capacites',
        'creation-creatures',
        'creation-objets',
        'creation-sorts',
    ];

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();
        $characteristicNames = $this->loadCharacteristicNames();

        $parent = $this->createOrRestorePage([
            'title' => 'Création',
            'slug' => 'creation',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 850,
            'menu_group' => 'Pour les MJ',
            'parent_id' => null,
            'icon' => 'fa-solid fa-hat-wizard',
            'created_by' => $creatorId,
        ]);

        $this->ensureTextSection(
            $parent,
            'creation-intro',
            'Introduction',
            '<h2>Atelier de création</h2>'
            .'<p>Espace réservé aux MJ pour concevoir et équilibrer le contenu : tableau des bonus d’équipement, '
            .'chartes des créatures, des objets et des sorts. Les chiffres affichés ici sont une <strong>projection</strong> '
            .'du système de caractéristiques : on corrige les fiches, pas une grille figée.</p>',
            0,
            $creatorId
        );

        $this->removeDeprecatedCreationChildren($parent);

        $this->seedEquipementsPage($parent, $creatorId);

        $order = 1;
        foreach (self::CHARTE_PAGES as $meta) {
            $sub = $this->createOrRestorePage([
                'title' => $meta['title'],
                'slug' => $meta['slug'],
                'in_menu' => true,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GAME_MASTER,
                'write_level' => User::ROLE_ADMIN,
                'menu_order' => $order++,
                'menu_group' => null,
                'parent_id' => $parent->id,
                'icon' => $meta['icon'],
                'created_by' => $creatorId,
            ]);

            $this->ensureCatalogSection(
                $sub,
                $meta['slug'].'-catalog',
                'Catalogue des chartes',
                $meta['group'],
                $meta['entity'],
                0,
                $creatorId
            );

            $this->ensureTextSection(
                $sub,
                $meta['slug'].'-intro',
                'Introduction',
                $meta['intro'],
                1,
                $creatorId
            );

            $normsKeys = $this->characteristicKeysWithNormsFromDefinitions($meta['group']);
            $charteOrder = 2;
            foreach ($normsKeys as $charKey) {
                $charName = $characteristicNames[$charKey] ?? $charKey;
                $this->ensureCharacteristicNormsSection(
                    $sub,
                    $meta['slug'].'-norms-'.str_replace('_', '-', $charKey),
                    $charName,
                    $charKey,
                    $meta['group'],
                    $meta['entity'],
                    $charteOrder++,
                    $creatorId
                );
            }

            $this->command?->info(
                "📄 Page {$meta['slug']} : catalogue + intro + ".count($normsKeys).' chartes.'
            );
        }

        PageService::clearMenuCache();
        $this->command?->info('📐 Pages Création : hub MJ + équipements + '.count(self::CHARTE_PAGES).' chartes.');
    }

    private function seedEquipementsPage(Page $parent, ?int $creatorId): void
    {
        $page = $this->createOrRestorePage([
            'title' => 'Équipements',
            'slug' => 'creation-equipements',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => $parent->id,
            'icon' => 'fa-solid fa-shield-halved',
            'created_by' => $creatorId,
        ]);

        $this->ensureTextSection(
            $page,
            'creation-equipements-intro',
            'Lire le tableau',
            '<h2>Bonus d’équipement</h2>'
            .'<p>Ce tableau projette les plafonds de bonus par <strong>emplacement</strong> (type d’objet) et par '
            .'<strong>caractéristique</strong>, d’après la table <em>formula</em> des caractéristiques objet. '
            .'Ce n’est pas une grille figée : si un chiffre est faux, on corrige la caractéristique, pas cette page.</p>'
            .'<p>Chaque colonne 1–2, 3–4, … 19–20 indique le plafond au début de la tranche (plus grand seuil de formule '
            .'≤ niveau de début). Un tiret signifie que le bonus n’est pas encore débloqué (valeur 0).</p>'
            .'<p>Les colonnes Prix / unité, FM max et Prix rune viennent du même enregistrement. Les écarts entre '
            .'<em>formula</em>, grille de normes et min/max se voient ici : ils se corrigent dans les fiches caractéristiques.</p>',
            0,
            $creatorId
        );

        $this->ensureEquipmentBonusTableSection(
            $page,
            'creation-equipements-table',
            'Tableau des bonus',
            1,
            $creatorId
        );

        $this->ensureTextSection(
            $page,
            'creation-equipements-rarete',
            'Rareté',
            '<h2>Rareté</h2>'
            .'<p>La rareté d’un objet se déduit de son <strong>prix</strong> dans la tranche de niveau, pas du nombre de caractéristiques.</p>'
            .'<ul>'
            .'<li>Plus de <strong>commun</strong> à partir du niveau 5</li>'
            .'<li>Plus de <strong>peu commun</strong> à partir du niveau 9</li>'
            .'<li>Plus de <strong>rare</strong> à partir du niveau 15 (très rare / légendaire)</li>'
            .'<li>Jamais <strong>unique</strong>, sauf Dofus et cas spéciaux</li>'
            .'</ul>',
            2,
            $creatorId
        );
    }

    private function removeDeprecatedCreationChildren(Page $parent): void
    {
        foreach (self::DEPRECATED_CHILD_SLUGS as $slug) {
            /** @var Page|null $page */
            $page = Page::withTrashed()->where('slug', $slug)->first();
            if (! $page instanceof Page) {
                continue;
            }
            if ($page->parent_id !== null && (int) $page->parent_id !== (int) $parent->id) {
                $page->parent_id = $parent->id;
                $page->save();
            }
            if (! $page->trashed()) {
                Page::destroy($page->id);
                $this->command?->info("🗑️ Ancienne page « {$slug} » archivée (doublon / structure remplacée).");
            }
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrRestorePage(array $attributes): Page
    {
        $slug = (string) $attributes['slug'];
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }
            $page->fill($attributes);
            $page->save();
            $this->command?->info("♻️ Page {$slug} restaurée/mise à jour");

            return $page;
        }

        $page = Page::create($attributes);
        $this->command?->info("✅ Page {$slug} créée");

        return $page;
    }

    private function ensureTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
        int $order,
        ?int $creatorId
    ): Section {
        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => ['align' => 'left', 'size' => 'md'],
            'data' => ['content' => $contentHtml],
            'params' => ['content' => $contentHtml],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureCatalogSection(
        Page $page,
        string $slug,
        string $title,
        string $group,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = [
            'group' => $group,
            'entity' => $entity,
            'characteristic_keys' => [],
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'type' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureCharacteristicNormsSection(
        Page $page,
        string $slug,
        string $title,
        string $characteristicKey,
        string $group,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = [
            'characteristic_key' => $characteristicKey,
            'group' => $group,
            'entity' => $entity,
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::CHARACTERISTIC_NORMS->value,
            'type' => SectionType::CHARACTERISTIC_NORMS->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureEquipmentBonusTableSection(
        Page $page,
        string $slug,
        string $title,
        int $order,
        ?int $creatorId
    ): Section {
        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::EQUIPMENT_BONUS_TABLE->value,
            'type' => SectionType::EQUIPMENT_BONUS_TABLE->value,
            'settings' => [],
            'data' => [],
            'params' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function ensureSection(Page $page, string $slug, array $attributes): Section
    {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $attributes = array_merge(['page_id' => $page->id, 'slug' => $slug], $attributes);

        if ($section) {
            if ($section->trashed()) {
                $section->restore();
            }
            $section->fill($attributes);
            $section->save();

            return $section;
        }

        return Section::create($attributes);
    }

    /**
     * @return array<string, string>
     */
    private function loadCharacteristicNames(): array
    {
        try {
            $names = Characteristic::pluck('name', 'key')->toArray();
            if (count($names) > 0) {
                return $names;
            }
        } catch (\Throwable) {
            // Table non disponible, fallback sur le fichier
        }

        return $this->loadCharacteristicNamesFromDefinitionFiles();
    }

    /**
     * @return array<string, string>
     */
    private function loadCharacteristicNamesFromDefinitionFiles(): array
    {
        $names = [];
        foreach (CharacteristicDefinitionReader::allDefinitionAbsolutePaths() as $path) {
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $c = $def['characteristic'] ?? [];
            if (! is_array($c)) {
                continue;
            }
            $key = $c['key'] ?? '';
            if (! is_string($key) || $key === '') {
                continue;
            }
            $names[$key] = is_string($c['name'] ?? null) && $c['name'] !== '' ? $c['name'] : $key;
        }

        return $names;
    }

    /**
     * @return list<string>
     */
    private function characteristicKeysWithNormsFromDefinitions(string $group): array
    {
        $dir = base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT.'/'.$group);
        if (! is_dir($dir)) {
            return [];
        }
        $found = [];
        foreach (glob($dir.DIRECTORY_SEPARATOR.'*-definition.json') ?: [] as $path) {
            if (! is_file($path)) {
                continue;
            }
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $key = $def['characteristic']['key'] ?? '';
            if (! is_string($key) || $key === '') {
                continue;
            }
            foreach ($def['entities'] as $payload) {
                if (! is_array($payload)) {
                    continue;
                }
                $hasNorms = ! empty($payload['norms_grid'])
                    || ! empty($payload['norms_conditions'])
                    || (isset($payload['norms_description']) && is_string($payload['norms_description']) && $payload['norms_description'] !== '');
                if ($hasNorms) {
                    $found[$key] = true;
                    break;
                }
            }
        }
        $list = array_keys($found);
        sort($list);

        return $list;
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

        $firstUser = User::query()->orderBy('id', 'asc')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }
}
