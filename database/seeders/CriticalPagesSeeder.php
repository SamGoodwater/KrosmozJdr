<?php

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
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
     * Sections de la page d’accueil (ton joueur, aligné sur la présentation des règles).
     */
    private function seedAccueilSections(Page $homePage, ?int $creatorId): void
    {
        $this->ensureTextSection(
            $homePage,
            'hero-accueil',
            'Bienvenue sur Krosmoz JDR',
            '<p>Tu es au bon endroit : ici vivent les <strong>règles</strong>, les <strong>bibliothèques de jeu</strong> et les outils pour préparer (ou improviser) ta prochaine aventure.</p>'
            .'<p>Que tu découvres le projet ou que tu reviennes après une pause, prends le temps de parcourir cette page — elle te donne la carte avant d’ouvrir le grimoire.</p>',
            1,
            $creatorId
        );

        $this->ensureTextSection(
            $homePage,
            'quest-ce-que-krosmoz-jdr',
            'Qu’est-ce que Krosmoz JDR ?',
            '<p><strong>Krosmoz JDR</strong> est un jeu de rôle sur table qui croise la structure et la profondeur tactique des grands JDR classiques avec l’univers coloré, drôle et familier de <em>Dofus</em> / <em>Wakfu</em>.</p>'
            .'<p>Les personnages évoluent, les combats se jouent au tour par tour avec des choix qui comptent, et les scènes laissent de la place à l’improvisation et au récit collectif. Pas besoin d’avoir tout lu pour commencer une table : l’essentiel est expliqué au fil des chapitres des règles.</p>',
            2,
            $creatorId
        );

        $this->ensureTextSection(
            $homePage,
            'ce-site',
            'Ce site, à quoi ça sert ?',
            '<p>Ce site officiel est le point d’entrée pour jouer et préparer des parties :</p>'
            .'<ul>'
            .'<li><strong>Règles</strong> — le corpus complet, structuré par chapitres (création de personnage, combat, magie, progression…).</li>'
            .'<li><strong>Bibliothèques</strong> — classes, sorts, monstres, équipements, états et autres entrées consultables et filtrables.</li>'
            .'<li><strong>Recherche rapide</strong> — raccourci <kbd>Alt</kbd> + <kbd>K</kbd> pour retrouver une page ou une entité sans fouiller les menus.</li>'
            .'<li><strong>Compte</strong> — inscription optionnelle pour suivre tes favoris, recevoir des notifications et, selon ton rôle, contribuer au contenu.</li>'
            .'</ul>'
            .'<p>Le site grandit avec le jeu : de nouvelles entrées et de nouveaux outils arrivent au fil des versions.</p>',
            3,
            $creatorId
        );

        $this->ensureTextSection(
            $homePage,
            'esprit-du-jeu',
            'L’esprit du jeu',
            '<p>Les textes officiels (règles comme pages d’accueil) visent un ton <strong>clair et précis</strong>, sans jargon inutile. On assume une part d’<strong>humour léger</strong> héritée de l’univers Krosmoz, sans que la mécanique devienne une blague : quand un sort ou une règle s’applique, elle s’applique.</p>'
            .'<p>En table, on privilégie :</p>'
            .'<ul>'
            .'<li>des <strong>choix tactiques</strong> lisibles (position, ressources, timing) ;</li>'
            .'<li>une <strong>narration partagée</strong> (les joueuses et joueurs font avancer l’histoire, le MJ arbitre et anime) ;</li>'
            .'<li>des <strong>personnages attachants</strong>, pas des fiches optimisées au détriment du fun.</li>'
            .'</ul>'
            .'<p>L’aventure avant tout — le reste est dans le menu <em>Règles</em>.</p>',
            4,
            $creatorId
        );

        $this->ensureTextSection(
            $homePage,
            'premiers-pas',
            'Par où commencer ?',
            '<p><strong>Si tu es joueuse ou joueur</strong></p>'
            .'<ul>'
            .'<li>Ouvre le menu <em>Règles</em> et lis la présentation du jeu, puis la création de personnage quand tu es prêt·e.</li>'
            .'<li>Parcours les <em>Bibliothèques</em> pour te faire une idée des classes, sorts et équipements disponibles.</li>'
            .'<li>Crée un compte si tu veux enregistrer des favoris ou rester informé·e des mises à jour.</li>'
            .'</ul>'
            .'<p><strong>Si tu es meneuse ou meneur de jeu</strong></p>'
            .'<ul>'
            .'<li>Les mêmes ressources t’aident à préparer : règles de référence, bestiaire, objets, états.</li>'
            .'<li>Avec un compte et les droits adaptés, tu peux proposer ou modifier du contenu (pages, sections, entités) selon la politique du site.</li>'
            .'<li>Utilise la recherche <kbd>Alt</kbd> + <kbd>K</kbd> en session pour retrouver une règle ou une fiche en quelques secondes.</li>'
            .'</ul>'
            .'<p>Une question sur les données personnelles ou les conditions d’utilisation ? Consulte les pages <em>Légales</em> et [[kref:page:cgu|Conditions générales d’utilisation]] dans le menu.</p>',
            5,
            $creatorId
        );

        $this->ensureTextSection(
            $homePage,
            'nouveautes-version',
            'Nouveautés de la version 1.3.2',
            '<p>La version <strong>1.3.2</strong> apporte notamment : une recherche globale plus fluide, des fiches entités plus lisibles, des réglages de visibilité pour les contenus, des améliorations d’accessibilité (navigation clavier, contrastes) et un formulaire de retour enrichi pour nous aider à corriger le site.</p>'
            .'<p>Le détail pour les joueuses, joueurs et MJ est dans le [[kref:page:changelog|journal des mises à jour]].</p>',
            6,
            $creatorId
        );
    }

    private function ensureTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
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
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => [
                'align' => 'left',
                'size' => 'md',
            ],
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
