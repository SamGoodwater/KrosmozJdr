<?php

namespace App\Services;

use App\Models\Section;
use App\Models\Page;
use App\Models\User;
use App\Enums\SectionType;
use App\Enums\PageState;
use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mews\Purifier\Facades\Purifier;

/**
 * Service pour la gestion des sections.
 * 
 * Centralise la logique métier liée aux sections :
 * - Création avec valeurs par défaut
 * - Mise à jour avec validation
 * - Réorganisation
 * - Gestion des permissions
 * - Récupération des sections affichables
 * 
 * @example
 * $section = SectionService::create($data, $user);
 * $sections = SectionService::getDisplayableSections($page, $user);
 */
class SectionService
{
    /**
     * Nettoie les champs HTML potentiellement dangereux selon le template.
     *
     * @param string $template Template (ex: 'text')
     * @param array<string, mixed> $data Données de section (tableau complet contenant potentiellement 'data')
     * @return array<string, mixed> Données nettoyées
     */
    private static function sanitizeSectionPayload(string $template, array $data): array
    {
        if (!isset($data['data']) || !is_array($data['data'])) {
            return $data;
        }

        // Sections texte : content = HTML issu d'un éditeur riche (TipTap)
        if ($template === SectionType::TEXT->value) {
            $content = $data['data']['content'] ?? null;
            if (is_string($content) && $content !== '') {
                $data['data']['content'] = Purifier::clean($content, 'section_text');
            }
        }

        return $data;
    }

    /**
     * Crée une nouvelle section avec des valeurs par défaut.
     * 
     * @param array<string, mixed> $data Données de la section
     * @param User $user Utilisateur créateur
     * @return Section Section créée
     * @throws \Exception Si la création échoue
     */
    public static function create(array $data, User $user): Section
    {
        DB::beginTransaction();
        try {
            // Compat legacy: type/params -> template/data (+ sync inverse)
            if (!isset($data['template']) && isset($data['type'])) {
                $data['template'] = $data['type'];
            }
            if (!isset($data['type']) && isset($data['template'])) {
                $data['type'] = $data['template'];
            }
            if (!isset($data['data']) && isset($data['params']) && is_array($data['params'])) {
                $data['data'] = $data['params'];
            }
            if (!isset($data['params']) && isset($data['data']) && is_array($data['data'])) {
                $data['params'] = $data['data'];
            }

            // Calculer l'ordre automatiquement si non fourni
            if (!isset($data['order']) || $data['order'] === 0) {
                $data['order'] = self::calculateNextOrder($data['page_id']);
            }

            // Ajouter les valeurs par défaut pour settings et data
            $template = $data['template'] ?? SectionType::TEXT->value;
            $defaults = self::getDefaultValues($template);
            
            $data['settings'] = array_merge($defaults['settings'], $data['settings'] ?? []);
            $data['data'] = array_merge($defaults['data'], $data['data'] ?? []);
            // Garder params en sync avec data (legacy)
            $data['params'] = array_merge($defaults['data'], (is_array($data['params'] ?? null) ? $data['params'] : []));

            // Sanitization (anti-XSS) sur les champs HTML
            $data = self::sanitizeSectionPayload($template, $data);
            
            // Valeurs par défaut pour les autres champs
            $data['created_by'] = $user->id;
            $data['state'] = $data['state'] ?? PageState::DRAFT->value;
            $data['is_visible'] = $data['is_visible'] ?? Visibility::GUEST->value;
            $data['can_edit_role'] = $data['can_edit_role'] ?? Visibility::ADMIN->value;

            $section = Section::create($data);
            $section->load(['page', 'users', 'files', 'createdBy']);

            DB::commit();
            return $section;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la section', [
                'data' => $data,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Met à jour une section existante.
     * 
     * Fusionne intelligemment les données existantes avec les nouvelles :
     * - Les `settings` et `data` sont fusionnés (merge) pour préserver les valeurs non modifiées
     * - Les autres champs sont remplacés directement
     * 
     * @param Section $section Section à mettre à jour
     * @param array<string, mixed> $data Données à mettre à jour (peut contenir settings, data, title, etc.)
     * @param User $user Utilisateur effectuant la mise à jour
     * @return Section Section mise à jour avec relations chargées
     * @throws \Exception Si la mise à jour échoue (transaction rollback)
     * 
     * @example
     * // Mise à jour partielle (seulement le titre)
     * SectionService::update($section, ['title' => 'Nouveau titre'], $user);
     * 
     * // Mise à jour des données (fusion avec les données existantes)
     * SectionService::update($section, ['data' => ['content' => 'Nouveau contenu']], $user);
     */
    public static function update(Section $section, array $data, User $user): Section
    {
        DB::beginTransaction();
        try {
            // Compat legacy: type/params -> template/data (+ sync inverse)
            if (!isset($data['template']) && isset($data['type'])) {
                $data['template'] = $data['type'];
            }
            if (!isset($data['type']) && isset($data['template'])) {
                $data['type'] = $data['template'];
            }
            if (!isset($data['data']) && isset($data['params']) && is_array($data['params'])) {
                $data['data'] = $data['params'];
            }
            if (!isset($data['params']) && isset($data['data']) && is_array($data['data'])) {
                $data['params'] = $data['data'];
            }

            // Fusionner les settings et data existants avec les nouveaux
            // Cela permet de mettre à jour seulement une partie des données sans perdre le reste
            if (isset($data['settings'])) {
                $data['settings'] = array_merge($section->settings ?? [], $data['settings']);
            }
            if (isset($data['data'])) {
                $data['data'] = array_merge($section->data ?? [], $data['data']);
            }
            // Legacy: fusionner params aussi (en mirror de data)
            if (isset($data['params'])) {
                $data['params'] = array_merge($section->params ?? [], $data['params']);
            }

            // Sanitization (anti-XSS) : utiliser le template effectif (nouveau si fourni, sinon existant)
            $effectiveTemplate = $data['template'] ?? ($section->template instanceof SectionType ? $section->template->value : (string) $section->template);
            if ($effectiveTemplate) {
                $data = self::sanitizeSectionPayload((string) $effectiveTemplate, $data);
            }

            $section->update($data);
            // Recharger les relations pour avoir les données à jour
            $section->load(['page', 'users', 'files', 'createdBy']);

            DB::commit();
            return $section;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la section', [
                'section_id' => $section->id,
                'data' => $data,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Réorganise l'ordre des sections (drag & drop).
     * 
     * Met à jour l'ordre de plusieurs sections en une seule transaction.
     * Utilisé lors du réordonnancement via drag & drop dans l'interface.
     * 
     * @param array $sections Tableau de sections avec structure : [['id' => int, 'order' => int], ...]
     * @param User $user Utilisateur effectuant la réorganisation (pour logs)
     * @return void
     * @throws \Exception Si la réorganisation échoue (transaction rollback)
     * 
     * @example
     * SectionService::reorder([
     *     ['id' => 1, 'order' => 1],
     *     ['id' => 2, 'order' => 2],
     *     ['id' => 3, 'order' => 3],
     * ], $user);
     */
    public static function reorder(array $sections, User $user): void
    {
        DB::beginTransaction();
        try {
            // Mettre à jour chaque section avec son nouvel ordre
            foreach ($sections as $sectionData) {
                Section::where('id', $sectionData['id'])
                    ->update(['order' => $sectionData['order']]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la réorganisation des sections', [
                'sections' => $sections,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Supprime une section (soft delete).
     * 
     * @param Section $section Section à supprimer
     * @param User $user Utilisateur effectuant la suppression
     * @return bool True si la suppression a réussi
     * @throws \Exception Si la suppression échoue
     */
    public static function delete(Section $section, User $user): bool
    {
        try {
            $section->delete();
            Log::info('Section supprimée', [
                'section_id' => $section->id,
                'user_id' => $user->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la section', [
                'section_id' => $section->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Récupère les sections affichables pour une page et un utilisateur.
     * 
     * @param Page $page Page concernée
     * @param User|null $user Utilisateur (null pour invité)
     * @return \Illuminate\Database\Eloquent\Collection<int, Section> Collection de sections affichables
     */
    public static function getDisplayableSections(Page $page, ?User $user = null): Collection
    {
        return Section::where('page_id', $page->id)
            ->displayable($user)
            ->orderBy('order')
            ->get();
    }

    /**
     * Récupère toutes les sections d'une page selon les permissions de l'utilisateur.
     * 
     * **Logique de récupération :**
     * - Si l'utilisateur peut modifier la page : retourne TOUTES les sections (drafts inclus)
     *   → Permet d'éditer toutes les sections, même non publiées
     * - Sinon : retourne uniquement les sections affichables (publiées + visibles)
     *   → Respecte la visibilité et l'état pour les utilisateurs sans droits d'édition
     * 
     * @param Page $page Page concernée
     * @param User|null $user Utilisateur (null pour invité)
     * @return \Illuminate\Database\Eloquent\Collection<int, Section> Collection de sections (toutes ou affichables selon permissions)
     * 
     * @example
     * // Pour un éditeur : toutes les sections
     * $allSections = SectionService::getSectionsForPage($page, $editor);
     * 
     * // Pour un visiteur : seulement les sections publiées et visibles
     * $visibleSections = SectionService::getSectionsForPage($page, null);
     */
    public static function getSectionsForPage(Page $page, ?User $user = null): Collection
    {
        // Si l'utilisateur peut modifier la page, inclure toutes les sections (y compris les drafts)
        // Sans filtre de visibilité ni d'état, car l'utilisateur doit pouvoir voir toutes les sections pour les éditer
        if ($user && $user->can('update', $page)) {
            return Section::where('page_id', $page->id)
                ->orderBy('order')
                ->get();
        }
        
        // Sinon, retourner uniquement les sections affichables (publiées et visibles)
        return self::getDisplayableSections($page, $user);
    }

    /**
     * Calcule le prochain ordre pour une nouvelle section.
     * 
     * @param int $pageId ID de la page
     * @return int Prochain ordre disponible
     */
    private static function calculateNextOrder(int $pageId): int
    {
        $maxOrder = Section::where('page_id', $pageId)
            ->max('order') ?? 0;
        
        return $maxOrder + 1;
    }

    /**
     * Retourne les valeurs par défaut pour un template de section.
     * 
     * **Source des données :**
     * - Les valeurs par défaut sont définies dans `config/section_templates.php`
     * - Ce fichier doit être synchronisé avec les fichiers `config.js` des templates frontend
     * - Aucune référence hardcodée aux templates spécifiques dans cette méthode
     * 
     * **Synchronisation :**
     * - Lors de la modification d'un `config.js` frontend, mettre à jour ce fichier PHP
     * - Un script de synchronisation automatique pourrait être créé à l'avenir
     * 
     * @param string $template Type de template (text, image, gallery, video, entity_table)
     * @return array Structure : ['settings' => array, 'data' => array]
     * 
     * @example
     * $defaults = SectionService::getDefaultValues('text');
     * // ['settings' => [], 'data' => ['content' => null]]
     * 
     * @see config/section_templates.php
     * @see resources/js/Pages/Organismes/section/templates/ pour les fichiers config.js de chaque template
     */

    public static function getDefaultValues(string $template): array
    {
        // Charger la configuration depuis le fichier de config
        $templatesConfig = config('section_templates', []);
        
        // Retourner les defaults du template ou des valeurs vides par défaut
        return $templatesConfig[$template] ?? [
            'settings' => [],
            'data' => [],
        ];
    }
}

