# Capacités (`capabilities`)

## Rôle et description
Les capacités regroupent les compétences spéciales, pouvoirs ou aptitudes particulières accessibles aux créatures, classes ou spécialisations. Elles enrichissent la personnalisation et la stratégie de jeu.

## Relations principales
- **Créatures** : via le pivot `capability_creature` (N:N avec `creatures`).
- **Classes** : via le pivot `capability_class` (N:N avec `classes`).
- **Spécialisations** : via le pivot `capability_specialization` (N:N avec `specializations`).

## Exemples d’utilisation
- Attribution d’une capacité spéciale à un personnage ou une classe.
- Définition de pouvoirs uniques pour une spécialisation.

## Import des données legacy

Une commande Artisan permet d'importer les capacités depuis un export JSON PHPMyAdmin de l'ancienne base :

```bash
php artisan capabilities:import-legacy database/seeders/data/capability.json
```

Options :
- `--dry-run` : simule l'import sans écrire en base
- `--force-update` : met à jour les capacités existantes (même nom) au lieu de les ignorer

Voir [IMPORT_LEGACY_CAPABILITIES.md](../../50-Fonctionnalités/Import-Legacy/IMPORT_LEGACY_CAPABILITIES.md) pour le mapping détaillé.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md)
- [ENTITY_SPECIALIZATIONS.md](ENTITY_SPECIALIZATIONS.md) 