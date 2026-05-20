# Recette manuelle — release 1.3.2

Checklist pour valider le produit avant tag. Complément automatisé : `tests/e2e/release-1.3.2.spec.ts` (Playwright smoke).

## Prérequis

- Base seedée : `php artisan project:seed` ou `php artisan scrapping:setup` (inclut `DofusdbEffectMappingSeeder`)
- Vérification : `php artisan project:init:verify` (si échec « mappings effets » : `php artisan db:seed --class=DofusdbEffectMappingSeeder`)
- Navigateurs Playwright (une fois) : `pnpm run playwright:install`
- Comptes : invité, joueur, MJ, admin (super_admin optionnel)

## Invité

- [ ] Accueil : sections hero, esprit du jeu, premiers pas visibles
- [ ] Recherche **Alt+K** : résultats limités aux entités publiques stables
- [ ] Pages légales : CGU, confidentialité, cookies (markdown)
- [ ] Changelog public : pas de section « Technique » exposée aux joueurs

## Joueur

- [ ] Connexion / profil / paramètres `#notifications`
- [ ] Toggle **Activer les notifications** : désactivé → plus de toast/email
- [ ] Préférences par type (site / email) enregistrées
- [ ] Bibliothèque : fiches **full** / **minimal** selon droits
- [ ] Création entité : modal minimal → édition complète

## MJ

- [ ] Matrice **Gérer l’affichage** : restriction invité sur brouillon
- [ ] Auteur voit/modifie sa fiche avant publication (policy)
- [ ] Feedback FAB : envoi + option email récap si connecté

## Admin

- [ ] `/admin/entity-display-visibility` : sauvegarde + invalidation permissions
- [ ] Caractéristiques : audit `characteristics:audit-definitions`
- [ ] `project:init:verify --with-rules` OK après import TOC

## Zone sensible / RGPD

- [ ] Export données personnelles
- [ ] Demande suppression (confirmation mot de passe)

## Automatisation

```bash
php artisan serve &
pnpm run dev                  # ou pnpm run build (obligatoire pour hydrater Inertia)
pnpm run playwright:install   # si première exécution Playwright
pnpm run test:e2e -- tests/e2e/release-1.3.2.spec.ts
pnpm run test:a11y
```
