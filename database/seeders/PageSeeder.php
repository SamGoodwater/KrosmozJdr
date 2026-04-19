<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Database\Seeder;

/**
 * Seed les pages de contribution : présentation du projet puis une sous-page
 * par groupe d'entité (créature, objet, sort), chacune avec une introduction
 * puis deux sections par caractéristique normée (texte + charte interactive).
 */
class PageSeeder extends Seeder
{
    /** Groupes d'entités avec métadonnées pour les pages. */
    private const ENTITY_GROUPS = [
        'creature' => [
            'title' => 'Créatures',
            'slug' => 'contribution-creatures',
            'icon' => 'fa-solid fa-dragon',
            'intro' => '<h2>Chartes des caractéristiques — Créatures</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les caractéristiques des créatures '
                .'(monstres, classes jouables, PNJ). Chaque charte définit les valeurs attendues selon le <strong>niveau</strong> (1–20) '
                .'et la <strong>puissance</strong> (très faible → très forte).</p>'
                .'<p>Utilise ces chartes pour vérifier qu\'une créature est équilibrée par rapport aux références du jeu. '
                .'Les conditions de lecture permettent d\'ajuster la ligne de puissance ou le niveau en fonction d\'autres caractéristiques.</p>',
            'entity' => '*',
        ],
        'object' => [
            'title' => 'Objets',
            'slug' => 'contribution-objets',
            'icon' => 'fa-solid fa-shield-halved',
            'intro' => '<h2>Chartes des caractéristiques — Objets</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les bonus d\'équipement. '
                .'Les valeurs sont calibrées selon les <strong>règles 5.2.4</strong> (Équipements et panoplies) : '
                .'+1-2 aux niveaux 1-5, +2-3 aux niveaux 6-10, +3-4 aux niveaux 11-15, +4-5 aux niveaux 16-20.</p>'
                .'<p>Un objet dont les bonus dépassent significativement la ligne « neutre » de sa charte est potentiellement '
                .'déséquilibré. Les objets rares ou légendaires peuvent atteindre la ligne « fort » ou « très fort ».</p>',
            'entity' => '*',
        ],
        'spell' => [
            'title' => 'Sorts',
            'slug' => 'contribution-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'intro' => '<h2>Chartes des caractéristiques — Sorts</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les effets de sorts. '
                .'Les dégâts, soins et boucliers sont calibrés selon les <strong>règles 5.2.3</strong> (Sorts et aptitudes), '
                .'avec une progression de ~1d6 (niveau 1) à ~5d6+mod (niveau 20).</p>'
                .'<p>Les conditions de lecture prennent en compte le coût en PA et la zone d\'effet : '
                .'un sort coûteux (5+ PA) peut avoir des dégâts supérieurs, tandis qu\'un sort en zone (≥2 cases) '
                .'devrait avoir des dégâts réduits par cible.</p>',
            'entity' => '*',
        ],
    ];

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();
        $characteristicNames = $this->loadCharacteristicNames();

        // Page parente : Contribution
        $contributionPage = $this->createOrRestorePage([
            'title' => 'Contribution',
            'slug' => 'contribution',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 900,
            'menu_group' => 'Informations',
            'parent_id' => null,
            'icon' => 'fa-solid fa-handshake-angle',
            'created_by' => $creatorId,
        ]);

        // Section introduction du projet
        $this->ensureTextSection(
            $contributionPage,
            'contribution-intro',
            'Contribuer au projet',
            '<h2>Bienvenue, contributeur !</h2>'
            .'<p><strong>Krosmoz JDR</strong> est un jeu de rôle sur table inspiré de l\'univers du Krosmoz (Dofus, Wakfu). '
            .'Ce projet est le fruit d\'un travail collaboratif qui vise à adapter les mécaniques du jeu vidéo '
            .'en un système de JDR complet et équilibré, jouable avec des dés et une feuille de personnage.</p>'
            .'<h3>Les piliers du projet</h3>'
            .'<ul>'
            .'<li><strong>Fidélité à l\'univers</strong> : respecter le lore et l\'ambiance du Krosmoz tout en l\'adaptant au format JDR.</li>'
            .'<li><strong>Équilibre</strong> : chaque classe, sort et objet doit être viable. Les chartes de caractéristiques (présentées dans les sous-pages) servent de référence.</li>'
            .'<li><strong>Accessibilité</strong> : le système doit rester simple à prendre en main, même pour les novices du JDR.</li>'
            .'<li><strong>Modularité</strong> : le MJ peut activer ou désactiver des modules (crafting, PvP, exploration) selon sa campagne.</li>'
            .'</ul>'
            .'<h3>Comment contribuer ?</h3>'
            .'<ul>'
            .'<li><strong>Équilibrage</strong> : utilise les chartes ci-dessous pour vérifier que les valeurs d\'une créature, d\'un objet ou d\'un sort sont cohérentes.</li>'
            .'<li><strong>Contenu</strong> : propose de nouvelles classes, sorts, monstres ou objets en respectant les normes établies.</li>'
            .'<li><strong>Relecture</strong> : signale les incohérences, fautes ou déséquilibres que tu repères.</li>'
            .'<li><strong>Playtest</strong> : joue des sessions et remonte tes retours d\'expérience.</li>'
            .'</ul>'
            .'<p>Les sous-pages suivantes détaillent les <strong>chartes de caractéristiques</strong> pour chaque type d\'entité. '
            .'Chaque charte est un tableau interactif montrant les valeurs de référence par niveau et par puissance.</p>',
            1,
            $creatorId
        );

        // Sous-pages par groupe d'entité
        $menuOrder = 0;
        foreach (self::ENTITY_GROUPS as $group => $meta) {
            $normsKeys = $this->characteristicKeysWithNormsFromDefinitions($group);
            if ($normsKeys === []) {
                continue;
            }

            $subPage = $this->createOrRestorePage([
                'title' => $meta['title'],
                'slug' => $meta['slug'],
                'in_menu' => true,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'menu_order' => $menuOrder++,
                'menu_group' => null,
                'parent_id' => $contributionPage->id,
                'icon' => $meta['icon'],
                'created_by' => $creatorId,
            ]);

            // Section intro du groupe
            $this->ensureTextSection(
                $subPage,
                $meta['slug'].'-intro',
                'Introduction',
                $meta['intro'],
                0,
                $creatorId
            );

            // Sections par caractéristique normée : texte + charte
            $order = 1;
            foreach ($normsKeys as $charKey) {
                $charName = $characteristicNames[$charKey] ?? $charKey;

                // Section charte interactive
                $this->ensureCharacteristicNormsSection(
                    $subPage,
                    $meta['slug'].'-norms-'.str_replace('_', '-', $charKey),
                    $charName,
                    $charKey,
                    $group,
                    $meta['entity'],
                    $order++,
                    $creatorId
                );
            }

            if ($this->command) {
                $this->command->info("📄 Page {$meta['slug']} : {$order} sections (intro + ".count($normsKeys).' chartes).');
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
            if ($this->command) {
                $this->command->info("♻️ Page {$slug} restaurée/mise à jour");
            }

            return $page;
        }

        $page = Page::create($attributes);
        if ($this->command) {
            $this->command->info("✅ Page {$slug} créée");
        }

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
            'read_level' => User::ROLE_GUEST,
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
            'read_level' => User::ROLE_GUEST,
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
        // Essayer depuis la BDD d'abord
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
     * Libellés depuis les définitions JSON (fallback si la table `characteristics` est vide).
     *
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
     * Clés ayant au moins une norme dans les définitions JSON du groupe.
     *
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

        $firstUser = User::query()->orderBy('id')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }
}
