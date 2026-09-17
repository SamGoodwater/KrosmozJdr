# Index calibration — 282 caractéristiques (1.3.2)

Les tableaux détaillés ligne par ligne ont été extraits du To do 1.3.1 → 1.3.2 pour alléger le suivi release.

## Sources de vérité

| Ressource | Rôle |
| --- | --- |
| [`docs/110- To Do/characteristic_definitions_index.csv`](../../110-%20To%20Do/characteristic_definitions_index.csv) | Index + colonne `statut_editorial` |
| `database/seeders/data/characteristic-definitions/{creature,object,spell}/` | Définitions JSON versionnées |
| `php artisan characteristics:definitions-progress` | Progression qualité automatisée |
| `php artisan characteristics:audit-definitions --report=storage/app/characteristics-audit.md` | Rapport écarts structurels / qualité |

## Commandes outillage

```bash
php artisan characteristics:audit-definitions --report=storage/app/characteristics-audit.md
php artisan characteristics:definitions-progress
php artisan characteristics:definitions-apply --item-types --object-skills --sync-csv
```

## Ordre de validation métier

1. Créature (112)
2. Objet (86)
3. Sort (84)

Gate après lot : `php artisan test --filter=Characteristic`
