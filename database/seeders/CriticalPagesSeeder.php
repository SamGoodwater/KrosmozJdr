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

        $this->ensureTextSection(
            $homePage,
            'hero-accueil',
            'Bienvenue',
            '<p>Bienvenue sur Krosmoz JDR.</p>',
            1,
            $defaultCreatorId
        );

        $etatsPage = $this->createOrRestoreBySlug([
            'title' => 'États',
            'slug' => 'etats',
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
            $etatsPage,
            'etats-intro',
            'Les états',
            '<p>Les états sont des effets temporaires applicables aux créatures. Consulte la section <strong>3.2.5. Traits et états</strong> des règles pour plus de détails.</p>',
            1,
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
            Storage::disk('public')->url('legal/cgu.md'),
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
            Storage::disk('public')->url('legal/politique-donnees.md'),
            1,
            $defaultCreatorId
        );
    }

    /**
     * @param array<string, mixed> $attributes
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

        $superAdmin = User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->first();
        if ($superAdmin) {
            return (int) $superAdmin->id;
        }

        $firstUser = User::query()->orderBy('id')->first();
        return $firstUser ? (int) $firstUser->id : null;
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
        ];

        foreach ($documents as $path => $content) {
            if ($disk->exists($path)) {
                continue;
            }
            $disk->put($path, $content);
            $this->command?->info("✅ Document legal cree: {$path}");
        }
    }

    private function defaultCguMarkdown(): string
    {
        return <<<MD
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
        return <<<MD
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
}

