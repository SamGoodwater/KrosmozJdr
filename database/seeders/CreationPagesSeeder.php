<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Arborescence « Création » : aide à la création d’entités (chartes / normes).
 * Une sous-page par **groupe technique** (spell, creature, object) : toutes les caractéristiques
 * d’un groupe partagent le même référentiel de sens ; les types d’entité (monstre, consommable, etc.)
 * se distinguent par le contenu, pas par des chartes dupliquées.
 *
 * Prérequis : seeders des caractéristiques et pivots (norms_grid) exécutés avant
 * (ex. {@see CreatureCharacteristicSeeder}, {@see ObjectCharacteristicSeeder}, {@see SpellCharacteristicSeeder}).
 */
class CreationPagesSeeder extends Seeder
{
    /**
     * Sous-pages : une par groupe (`characteristic_*` pivot), `entity` = *.
     *
     * @var list<array{title: string, slug: string, icon: string, group: string, entity: string, intro: string, advice: string}>
     */
    private const SUBPAGES = [
        [
            'title' => 'Sorts et capacités',
            'slug' => 'creation-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'group' => 'spell',
            'entity' => '*',
            'intro' => '<h2>Groupe sort / capacités — chartes</h2>'
                .'<p>Ce catalogue couvre <strong>toutes</strong> les caractéristiques du groupe <em>sort</em> : '
                .'dégâts, soins, portée, PA, buffs, etc. Les <strong>capacités</strong> s’appuient sur le même groupe '
                .'en base : une seule page évite de dupliquer les mêmes grilles.</p>'
                .'<p>Complète avec des sections texte (conseils, exemples) si besoin.</p>',
            'advice' => '<p><strong>Conseils d’équilibrage</strong></p>'
                .'<ul>'
                .'<li>Commence par la ligne <strong>Neutre</strong> au niveau visé, puis applique uniquement les modificateurs réellement pertinents.</li>'
                .'<li>Pour un sort à très forte portée ou à grande zone, évite de dépasser les bornes hautes de dégâts/soins au même niveau.</li>'
                .'<li>Un coût PA élevé peut justifier une valeur plus haute, mais garde une cohérence avec les limites min/max affichées sous le tableau.</li>'
                .'<li>Les capacités utilitaires (entrave, placement, contrôle) compensent souvent la valeur brute : privilégie alors une lecture plus prudente.</li>'
                .'</ul>',
        ],
        [
            'title' => 'Créatures',
            'slug' => 'creation-creatures',
            'icon' => 'fa-solid fa-dragon',
            'group' => 'creature',
            'entity' => '*',
            'intro' => '<h2>Groupe créature — chartes</h2>'
                .'<p>Référentiel unique pour monstres, classes jouables, PNJ : PV, stats, CA, maîtrises, etc. '
                .'Le sens des caractéristiques est commun à tout le groupe <em>creature</em>.</p>',
            'advice' => '<p><strong>Conseils de construction</strong></p>'
                .'<ul>'
                .'<li>Si la créature est censée encaisser, privilégie d’abord les <strong>points de vie</strong> puis la défense, avant d’augmenter les dégâts.</li>'
                .'<li>Évite de cumuler des valeurs hautes sur trop d’axes (PV, dégâts, mobilité, contrôle) au même niveau.</li>'
                .'<li>Pour les créatures rapides ou techniques, monte plutôt mobilité/initiative et garde des PV plus modérés.</li>'
                .'<li>Vérifie systématiquement que les valeurs finales restent dans les bornes min/max du niveau cible.</li>'
                .'</ul>',
        ],
        [
            'title' => 'Objets',
            'slug' => 'creation-objets',
            'icon' => 'fa-solid fa-box-open',
            'group' => 'object',
            'entity' => '*',
            'intro' => '<h2>Groupe objet — chartes</h2>'
                .'<p>Un seul catalogue pour équipements, consommables, ressources, panoplies : les bonus et portées '
                .'objet partagent les mêmes échelles. Tu documentes les nuances (slot, rareté, usage) en texte autour du catalogue.</p>',
            'advice' => '<p><strong>Conseils de calibration</strong></p>'
                .'<ul>'
                .'<li>Sur les bas niveaux, reste proche des paliers faibles/modérés ; réserve les valeurs fortes aux objets rares ou à fortes contraintes.</li>'
                .'<li>Évite les objets qui surclassent la progression normale du niveau (surtout sur plusieurs stats en même temps).</li>'
                .'<li>Pour consommables et ressources, adapte la puissance à la fréquence d’obtention et au coût d’accès.</li>'
                .'<li>Utilise les limites min/max comme garde-fou avant validation finale.</li>'
                .'</ul>',
        ],
    ];

    /**
     * Anciennes sous-pages (6 pages par type d’entité) remplacées par 3 pages par groupe.
     * Suppression logique au re-seed pour éviter les doublons dans le menu.
     *
     * @var list<string>
     */
    private const DEPRECATED_CHILD_SLUGS = [
        'creation-monstres',
        'creation-equipement',
        'creation-ressources',
        'creation-consommables',
        'creation-capacites',
    ];

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();

        $parent = $this->createOrRestorePage([
            'title' => 'Création',
            'slug' => 'creation',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 850,
            'menu_group' => 'Aide',
            'parent_id' => null,
            'icon' => 'fa-solid fa-compass-drafting',
            'created_by' => $creatorId,
        ]);

        $this->ensureTextSection(
            $parent,
            'creation-intro',
            'Introduction',
            '<h2>Aide à la création</h2>'
            .'<p>Trois espaces alignés sur les <strong>groupes de caractéristiques</strong> du jeu : '
            .'<strong>sort</strong> (sorts et capacités), <strong>créature</strong>, <strong>objet</strong> (équipement, consommables, ressources…). '
            .'Chaque groupe a un sens interne cohérent — pas besoin d’une page par type d’entité pour les chartes.</p>'
            .'<p>Ajoute des sections <em>texte</em> sur chaque sous-page pour conseils, exemples ou liens.</p>',
            0,
            $creatorId
        );

        $this->removeDeprecatedCreationChildren($parent);

        $order = 0;
        foreach (self::SUBPAGES as $meta) {
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

            $this->ensureTextSection(
                $sub,
                $meta['slug'].'-intro',
                'Introduction',
                $meta['intro'],
                0,
                $creatorId
            );

            $this->ensureCatalogSection(
                $sub,
                $meta['slug'].'-catalog',
                'Catalogue des chartes',
                $meta['group'],
                $meta['entity'],
                1,
                $creatorId
            );

            $this->ensureTextSection(
                $sub,
                $meta['slug'].'-advice',
                '',
                $meta['advice'],
                2,
                $creatorId
            );
        }

        $this->command?->info('📐 Pages Création : parent + '.count(self::SUBPAGES).' sous-pages (par groupe).');
    }

    private function removeDeprecatedCreationChildren(Page $parent): void
    {
        foreach (self::DEPRECATED_CHILD_SLUGS as $slug) {
            $page = Page::withTrashed()->where('parent_id', $parent->id)->where('slug', $slug)->first();
            if ($page === null) {
                continue;
            }
            if (! $page->trashed()) {
                $page->delete();
                $this->command?->info("🗑️ Ancienne page « {$slug} » archivée (remplacée par la structure par groupe).");
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
