<?php

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Support\Cms\KrefShortcodeReplacer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeder idempotent des pages critiques (accueil, CGU).
 */
class CriticalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCreatorId = $this->resolveDefaultCreatorId();
        $this->ensureLegalMarkdownFiles();
        $this->ensureEntityMenuIcons();

        $homePage = $this->createOrRestoreBySlug([
            'title' => 'Accueil',
            'slug' => 'accueil',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
        ], 'Page Accueil');

        $this->seedAccueilSections($homePage, $defaultCreatorId);

        $conditionsPage = $this->createOrRestoreBySlug([
            'title' => 'États',
            'slug' => 'conditions',
            'in_menu' => false,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
            'page_css_classes' => 'color-condition-500',
        ], 'Page États');

        $this->ensureTextSection(
            $conditionsPage,
            'conditions-intro',
            'Les états',
            '<p>Les états sont des affixes temporaires applicables aux créatures. Ils modélisent les effets de durée, séparément des caractéristiques et des traits permanents.</p>',
            1,
            $defaultCreatorId
        );

        $this->ensureEntityTableSection(
            $conditionsPage,
            'conditions-table',
            'Liste des états',
            'conditions',
            2,
            $defaultCreatorId
        );

        $traitsPage = $this->createOrRestoreBySlug([
            'title' => 'Traits',
            'slug' => 'creature-traits',
            'in_menu' => false,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
            'page_css_classes' => 'color-creature-trait-500',
        ], 'Page Traits');

        $this->ensureTextSection(
            $traitsPage,
            'creature-traits-intro',
            'Les traits',
            '<p>Les traits sont des propriétés permanentes et non dissipables attachées aux créatures, classes ou spécialisations.</p>',
            1,
            $defaultCreatorId
        );

        $this->ensureEntityTableSection(
            $traitsPage,
            'creature-traits-table',
            'Liste des traits',
            'creature-traits',
            2,
            $defaultCreatorId
        );

        $legalesPage = $this->createOrRestoreBySlug([
            'title' => 'Légales',
            'slug' => 'legales',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 950,
            'menu_group' => 'Informations',
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
        ], 'Page Légales');

        $this->ensureTextSection(
            $legalesPage,
            'legales-intro',
            'Mentions légales',
            '<p>Tu trouveras ici les documents juridiques du site.</p>',
            0,
            $defaultCreatorId
        );

        $cguPage = $this->createOrRestoreBySlug([
            'title' => 'Conditions Générales d\'Utilisation',
            'slug' => 'cgu',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => 'Informations',
            'parent_id' => $legalesPage->id,
            'created_by' => $defaultCreatorId,
            'menu_item_css_classes' => 'color-neutral-500 box-shadow-glass',
        ], 'Page CGU');

        $this->ensureLegalMarkdownSection(
            $cguPage,
            'legal-cgu',
            'Conditions Générales d\'Utilisation',
            '/legal/cgu',
            1,
            $defaultCreatorId
        );

        $policyPage = $this->createOrRestoreBySlug([
            'title' => 'Politique de confidentialité et cookies',
            'slug' => 'politique-donnees',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 1,
            'menu_group' => 'Informations',
            'parent_id' => $legalesPage->id,
            'created_by' => $defaultCreatorId,
            'menu_item_css_classes' => 'color-neutral-500 box-shadow-glass',
        ], 'Page Politique donnees');

        $this->ensureLegalMarkdownSection(
            $policyPage,
            'legal-politique-donnees',
            'Politique de confidentialité et cookies',
            '/legal/politique-donnees',
            1,
            $defaultCreatorId
        );

        $cookiesPage = $this->createOrRestoreBySlug([
            'title' => 'Politique cookies (synthèse)',
            'slug' => 'cookies',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 2,
            'menu_group' => 'Informations',
            'parent_id' => $legalesPage->id,
            'created_by' => $defaultCreatorId,
            'menu_item_css_classes' => 'color-neutral-500 box-shadow-glass',
        ], 'Page Cookies légales');

        $this->ensureLegalMarkdownSection(
            $cookiesPage,
            'legal-cookies',
            'Cookies — synthèse',
            '/legal/cookies',
            1,
            $defaultCreatorId
        );

        $changelogPage = $this->createOrRestoreBySlug([
            'title' => 'Changelog',
            'slug' => 'changelog',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 960,
            'menu_group' => 'Informations',
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
            'menu_item_css_classes' => 'color-neutral-500 box-shadow-glass',
        ], 'Page Changelog');

        $this->ensureLegalMarkdownSection(
            $changelogPage,
            'legal-changelog',
            'Changelog du site',
            '/changelog/feed/'.$this->publicChangelogSemver(),
            1,
            $defaultCreatorId
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrRestoreBySlug(array $attributes, string $label): Page
    {
        $slug = (string) $attributes['slug'];
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }

            $page->fill($attributes);
            $page->save();
            $this->command?->info("♻️ {$label} restaurée/mise à jour ({$slug})");

            return $page;
        }

        $page = Page::create($attributes);
        $this->command?->info("✅ {$label} créée ({$slug})");

        return $page;
    }

    private function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $superAdmin = User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id', 'asc')->first();
        if ($superAdmin) {
            return (int) $superAdmin->id;
        }

        $firstUser = User::query()->orderBy('id', 'asc')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }

    /**
     * Sections de la page d’accueil (6 blocs : hero, bêta, jeu, plateforme, parcours, contribuer).
     */
    private function seedAccueilSections(Page $homePage, ?int $creatorId): void
    {
        $kref = KrefShortcodeReplacer::forEssentialPages();

        $this->ensureTextSection(
            $homePage,
            'hero-accueil',
            'Le Monde des Douze, autour de la table',
            $kref->replace(
                '<p><strong>Le Monde des Douze, autour de la table.</strong> Dofus rencontre le JdR tactique : tu annonces, tu lances, tu dépenses tes [[kref:characteristic:action_points_creature|PA]] — et tu racontes ta légende.</p>'
            ),
            1,
            $creatorId,
            true
        );

        $this->ensureTextSection(
            $homePage,
            'encart-beta',
            'Version bêta',
            $kref->replace(
                '<div class="alert alert-info shadow-sm not-prose">'
                .'<i class="fa-solid fa-flask" aria-hidden="true"></i>'
                .'<div>'
                .'<p><strong>Krosmoz JDR est en bêta.</strong> Les règles sont jouables et le site évolue vite — mais tout n’est pas encore au catalogue.</p>'
                .'<ul>'
                .'<li><strong>En cours</strong> : peu de sorts, monstres et classes pleinement disponibles dans les bibliothèques.</li>'
                .'<li><strong>En progression</strong> : règles, [[kref:page:essentiels-bien-demarrer|L’Essentiel]], outils du site, contenu qui grossit version après version.</li>'
                .'</ul>'
                .'<p>Les bibliothèques se remplissent — la bêta, c’est jouer et construire en même temps. Envie d’aider ? Voir la section <em>Contribuer</em> plus bas.</p>'
                .'</div>'
                .'</div>'
            ),
            2,
            $creatorId,
            true
        );

        $this->ensureTextSection(
            $homePage,
            'le-jeu',
            'Le jeu',
            $kref->replace(
                '<ul>'
                .'<li><strong>Univers Dofus / Wakfu</strong> — classes iconiques, humour léger, Monde des Douze.</li>'
                .'<li><strong>Tactique au tour par tour</strong> — [[kref:characteristic:action_points_creature|PA]], [[kref:characteristic:movement_points_creature|PM]], grille, choix qui comptent.</li>'
                .'<li><strong>JdR classique</strong> — d20, progression, narration à table avec ton groupe.</li>'
                .'<li><strong>Pas besoin de tout lire</strong> — [[kref:page:essentiels-bien-demarrer|L’Essentiel]] pour démarrer, le détail dans les [[kref:page:regles-1-introduction|Règles]].</li>'
                .'</ul>'
            ),
            3,
            $creatorId,
            true
        );

        $this->ensureTextSection(
            $homePage,
            'la-plateforme',
            'La plateforme',
            $kref->replace(
                '<p>Ce site, c’est ton camp de base pour jouer et préparer :</p>'
                .'<ul>'
                .'<li><strong>[[kref:page:regles-1-introduction|Règles]]</strong> — corpus structuré par chapitres.</li>'
                .'<li><strong>L’Essentiel</strong> — résumés pour jouer et trucs pour le MJ ([[kref:page:essentiels-bien-demarrer|Bien démarrer]]).</li>'
                .'<li><strong>Bibliothèques</strong> — classes, sorts, monstres, équipements… catalogue en cours de remplissage.</li>'
                .'<li><strong>Recherche rapide</strong> — <kbd>Alt</kbd> + <kbd>K</kbd> pour retrouver une page ou une entité.</li>'
                .'<li><strong>Compte</strong> — favoris, notifications, contribution selon ton rôle.</li>'
                .'</ul>'
            ),
            4,
            $creatorId,
            true
        );

        $this->ensureTextSection(
            $homePage,
            'par-ou-commencer',
            'Par où commencer ?',
            $kref->replace(
                '<p><strong>Nouveau·elle en JdR ou sur Krosmoz</strong></p>'
                .'<ul>'
                .'<li>[[kref:page:essentiels-bien-demarrer|L’Essentiel — Bien démarrer]]</li>'
                .'<li>[[kref:page:essentiels-creation-personnage|Création de personnage]]</li>'
                .'<li>Parcourir une [[kref:page:bibliotheque-breed|classe]] en bibliothèque</li>'
                .'</ul>'
                .'<p><strong>Déjà à l’aise en JdR</strong></p>'
                .'<ul>'
                .'<li>[[kref:page:regles-1-introduction|Règles]] ch. 1–3 + [[kref:page:essentiels-combat|Essentiel combat]]</li>'
                .'</ul>'
                .'<p><strong>Meneuse ou meneur de jeu (MJ)</strong></p>'
                .'<ul>'
                .'<li>[[kref:page:essentiels-bien-demarrer|L’Essentiel]] pour les rappels de table</li>'
                .'<li>[[kref:page:regles-1-introduction|Règles]] comme référence + bibliothèques pour préparer les rencontres</li>'
                .'<li><kbd>Alt</kbd> + <kbd>K</kbd> en session pour retrouver une règle ou une fiche</li>'
                .'</ul>'
                .'<p>Questions légales ? Menu <em>Légales</em> et [[kref:page:cgu|Conditions générales d’utilisation]].</p>'
            ),
            5,
            $creatorId,
            true
        );

        $this->ensureTextSection(
            $homePage,
            'contribuer',
            'Contribuer',
            $kref->replace(
                '<p>La bêta avance grâce aux tables et aux contributeur·rice·s. Tu peux aider de plusieurs façons :</p>'
                .'<ul>'
                .'<li><strong>Retours de jeu</strong> — playtest, relecture, signalement de coquilles</li>'
                .'<li><strong>Contenu</strong> — sorts, monstres, classes, textes (selon les droits sur ton compte)</li>'
                .'<li><strong>Communauté</strong> — [[kref:page:nous-rejoindre|Nous rejoindre]] pour Discord, GitHub et la demande d’accès éditeur</li>'
                .'</ul>'
                .'<p>Tu peux aussi passer par la page <a href="/contribuer">Contribuer</a> — elle te redirige vers les bonnes ressources.</p>'
            ),
            6,
            $creatorId,
            true
        );

        $this->removeOrphanAccueilSections($homePage);
    }

    /**
     * Supprime les sections d’accueil obsolètes (slug hors structure courante).
     */
    private function removeOrphanAccueilSections(Page $homePage): void
    {
        $expectedSlugs = [
            'hero-accueil',
            'encart-beta',
            'le-jeu',
            'la-plateforme',
            'par-ou-commencer',
            'contribuer',
        ];

        Section::query()
            ->where('page_id', $homePage->id)
            ->whereNotIn('slug', $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    private function ensureTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
        int $order,
        ?int $creatorId,
        bool $enableRichReferences = false
    ): Section {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $settings = [
            'align' => 'left',
            'size' => 'md',
        ];
        if ($enableRichReferences) {
            $settings['enableRichReferences'] = true;
        }

        $attributes = [
            'page_id' => $page->id,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => $settings,
            'data' => [
                'content' => $contentHtml,
            ],
            'params' => [
                'content' => $contentHtml,
            ],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($section) {
            if ($section->trashed()) {
                $section->restore();
            }
            $section->fill($attributes);
            $section->save();
            $this->command?->info("♻️ Section {$slug} restaurée/mise à jour");

            return $section;
        }

        $section = Section::create($attributes);
        $this->command?->info("✅ Section {$slug} créée");

        return $section;
    }

    private function ensureEntityTableSection(
        Page $page,
        string $slug,
        string $title,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $payload = [
            'entity' => $entity,
            'filters' => [],
            'limit' => 50,
            'columns' => [],
        ];

        $attributes = [
            'page_id' => $page->id,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::ENTITY_TABLE->value,
            'type' => SectionType::ENTITY_TABLE->value,
            'settings' => $payload,
            'data' => $payload,
            'params' => $payload,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($section) {
            if ($section->trashed()) {
                $section->restore();
            }
            $section->fill($attributes);
            $section->save();
            $this->command?->info("♻️ Section {$slug} restaurée/mise à jour");

            return $section;
        }

        $section = Section::create($attributes);
        $this->command?->info("✅ Section {$slug} créée");

        return $section;
    }

    private function publicChangelogSemver(): string
    {
        $v = trim((string) config('releases.public_changelog_semver'));

        return $v !== '' ? $v : '1.3.2';
    }

    private function ensureLegalMarkdownSection(
        Page $page,
        string $slug,
        string $title,
        string $sourceUrl,
        int $order,
        ?int $creatorId
    ): Section {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $attributes = [
            'page_id' => $page->id,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::LEGAL_MARKDOWN->value,
            'type' => SectionType::LEGAL_MARKDOWN->value,
            'settings' => [],
            'data' => [
                'sourceUrl' => $sourceUrl,
                'title' => $title,
            ],
            'params' => [
                'sourceUrl' => $sourceUrl,
                'title' => $title,
            ],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($section) {
            if ($section->trashed()) {
                $section->restore();
            }
            $section->fill($attributes);
            $section->save();
            $this->command?->info("♻️ Section {$slug} restaurée/mise à jour");

            return $section;
        }

        $section = Section::create($attributes);
        $this->command?->info("✅ Section {$slug} créée");

        return $section;
    }

    private function ensureLegalMarkdownFiles(): void
    {
        $disk = Storage::disk('public');
        $documents = [
            'legal/cgu.md' => $this->defaultCguMarkdown(),
            'legal/politique-donnees.md' => $this->defaultPrivacyMarkdown(),
            'legal/cookies.md' => $this->defaultCookiesMarkdown(),
        ];

        foreach ($documents as $path => $content) {
            if ($disk->exists($path)) {
                continue;
            }
            $disk->put($path, $content);
            $this->command?->info("✅ Document legal cree: {$path}");
        }

        $changelog = [
            'changelog/intro.md' => $this->defaultChangelogIntroMarkdown(),
            'changelog/'.$this->publicChangelogSemver().'.md' => $this->defaultChangelogVersionMarkdown(),
        ];

        foreach ($changelog as $path => $content) {
            if ($disk->exists($path)) {
                continue;
            }
            $disk->put($path, $content);
            $this->command?->info("✅ Fichier changelog cree: {$path}");
        }

        $prior = $this->defaultChangelogPriorVersionPath();
        if ($prior !== null && ! $disk->exists($prior)) {
            $disk->put($prior, $this->defaultChangelogPriorVersionMarkdown());
            $this->command?->info("✅ Fichier changelog cree: {$prior}");
        }
    }

    /**
     * @return non-empty-string|null
     */
    private function defaultChangelogPriorVersionPath(): ?string
    {
        $current = trim((string) config('releases.public_changelog_semver'), " \t\n\r\x0\v");
        if ($current === '') {
            return null;
        }
        try {
            $parts = explode('.', $current);
            if (count($parts) !== 3) {
                return null;
            }
            [$major, $minor, $patch] = $parts;
            if (! ctype_digit((string) $major) || ! ctype_digit((string) $minor) || ! ctype_digit((string) $patch)) {
                return null;
            }
            $prevPatch = ((int) $patch) - 1;
            if ($prevPatch < 0) {
                return null;
            }

            return 'changelog/'.$major.'.'.$minor.'.'.$prevPatch.'.md';
        } catch (\Throwable) {
            return null;
        }
    }

    private function defaultCguMarkdown(): string
    {
        return <<<'MD'
# Conditions Generales d'Utilisation (CGU)

Derniere mise a jour : 2026-03-06

## 1. Objet
KrosmozJDR est une plateforme de jeu de role en ligne. Les presentes CGU encadrent l'acces et l'utilisation du service.

## 2. Compte utilisateur
- La creation d'un compte peut etre necessaire pour certaines fonctionnalites.
- Tu es responsable de la confidentialite de tes identifiants.
- Toute utilisation abusive peut entrainer la suspension du compte.

## 3. Contenus et responsabilites
- Les contenus publies doivent respecter la loi et les regles de la plateforme.
- Les contenus illicites, haineux ou frauduleux sont interdits.
- L'editeur peut moderer, masquer ou supprimer des contenus non conformes.

## 4. Disponibilite du service
Le service est fourni "en l'etat". Des interruptions temporaires peuvent survenir pour maintenance, evolution ou securite.

## 5. Propriete intellectuelle
Les elements du site (marques, graphismes, textes, code, etc.) sont proteges. Toute reproduction non autorisee est interdite.

## 6. Donnees personnelles et cookies
Le traitement des donnees et l'utilisation des cookies sont detailles dans la Politique de confidentialite et cookies.

## 7. Contact
Pour toute question : contact@krosmoz-jdr.fr
MD;
    }

    private function defaultPrivacyMarkdown(): string
    {
        return <<<'MD'
# Politique de confidentialite et cookies

Derniere mise a jour : 2026-03-06

## 1. Responsable du traitement
Projet : KrosmozJDR  
Contact : contact@krosmoz-jdr.fr

## 2. Donnees traitees
Selon les usages, nous pouvons traiter :
- Donnees de compte (pseudo, email, role).
- Donnees techniques de session et de securite.
- Donnees de contenu que tu saisis volontairement.

## 3. Finalites
Les donnees sont utilisees pour :
- fournir le service et l'authentification ;
- securiser la plateforme ;
- administrer les contenus et les comptes.

## 4. Base legale
- Execution du service (fourniture du compte et des fonctionnalites).
- Interet legitime (securite et prevention des abus).
- Consentement lorsque requis (cookies tiers optionnels).

## 5. Cookies
### Cookies necessaires (toujours actifs)
- Session Laravel (maintien de connexion et securite CSRF).
- Cookies techniques indispensables au fonctionnement.

### Cookies tiers (optionnels, avec consentement)
- Certains contenus externes (ex: YouTube/Vimeo) peuvent deposer des cookies tiers.
- Ces cookies ne sont actives qu'apres acceptation explicite.

## 6. Duree de conservation
Les donnees sont conservees uniquement pour la duree necessaire aux finalites annoncees et obligations legales.

## 7. Tes droits
Tu peux demander l'acces, la rectification, l'effacement, la limitation ou l'opposition, selon la reglementation applicable.

## 8. Contact
Pour exercer tes droits ou poser une question : contact@krosmoz-jdr.fr
MD;
    }

    private function defaultCookiesMarkdown(): string
    {
        return <<<'MD'
# Cookies (synthèse)

Dernière mise à jour : 2026-05-19

## Nécessaires

- Cookies de session et de sécurité (CSRF) fournis par l’application.
- Réglages fonctionnels indispensables sans publicité comportementale.

## Tiers (contenus externes)

- Les médias externes (YouTube, Vimeo…) ne déposent des cookies tiers **qu’après** consentement depuis la bannière.

## Détail légal complet

Voir la **[Politique de confidentialité et cookies](/pages/politique-donnees)** (page CMS) qui reprend données personnelles et cookies ensemble.
MD;
    }

    private function defaultChangelogIntroMarkdown(): string
    {
        return <<<'MD'
# À propos de ce changelog

Les versions suivent le schéma **X.Y.Z** (semver courte). Pour chaque version : **contenu / produit** en premier, puis un volet **technique** succinct lorsque c’est utile.

Utilise la navigation injectée automatiquement en tête pour passer d’un fichier changelog à un autre.
MD;
    }

    private function defaultChangelogPriorVersionMarkdown(): string
    {
        return <<<'MD'
# Changelog — 1.3.1

Version archivée pour la navigation semver (précède habituellement le gel fonctionnel suivant).

## Technique

- Travaux préparatoires avant la ligne directrice semver publique actuelle dans `config/releases.php`.
MD;
    }

    private function defaultChangelogVersionMarkdown(): string
    {
        return <<<'MD'
# KrosmozJDR — notes de version (semver publique)

> Si ce fichier existe déjà depuis le dépôt, le seeder ne l’écrase pas ; ce bloc sert uniquement de repli après déploiement.

## Contenu / produit

- Matrice « Gérer l’affichage » (visibilité par type d’entité × état workflow × rôle minimal).
- Recherche globale (API + en-tête / filtres).
- Documents Markdown légaux servis depuis `storage/app/public/legal/` via routes **`/legal/…`** ou le legacy **`/storage/legal/*.md`**.
- Changelog versionné sous **`storage/app/public/changelog/{X.Y.Z}.md`**, exposition agrégée par **`GET /changelog/feed/{version}`**.

## Technique (court)

- Policies (`BaseEntityPolicy`, Breed…) + gardes **`Model` / `instanceof`** contre régressions LSP.
- Défauts CMS page/section (lecture invité, écriture MJ si niveaux omis ; décision **Q6**).
- Bump cache permissions après sauvegarde matrice (**`EntityPermissionService`**).
MD;
    }

    /**
     * Assure les fichiers WebP attendus par le menu (clés {@see \resources\js\config\entities.js}) :
     * `condition.webp`, `creature-trait.webp`.
     *
     * Copie idempotente depuis des icônes existantes si le cible manque.
     */
    private function ensureEntityMenuIcons(): void
    {
        $dir = storage_path('app/public/images/icons/entities');
        if (! is_dir($dir)) {
            return;
        }

        $creatureTrait = $dir.DIRECTORY_SEPARATOR.'creature-trait.webp';
        $trait = $dir.DIRECTORY_SEPARATOR.'trait.webp';
        if (! is_file($creatureTrait) && is_file($trait)) {
            copy($trait, $creatureTrait);
            $this->command?->info('CriticalPagesSeeder : creature-trait.webp créé à partir de trait.webp.');
        }

        $condition = $dir.DIRECTORY_SEPARATOR.'condition.webp';
        $fallback = $dir.DIRECTORY_SEPARATOR.'spell.webp';
        if (! is_file($condition) && is_file($fallback)) {
            copy($fallback, $condition);
            $this->command?->info('CriticalPagesSeeder : condition.webp créé à partir de spell.webp (placeholder).');
        }
    }
}
