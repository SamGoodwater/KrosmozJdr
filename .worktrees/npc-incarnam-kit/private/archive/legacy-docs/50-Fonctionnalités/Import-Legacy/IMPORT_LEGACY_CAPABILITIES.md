# Import des capacités legacy

Import des capacités depuis l'ancienne base (export JSON PHPMyAdmin) vers le nouveau système.

## Commande

```bash
php artisan capabilities:import-legacy <fichier.json> [--dry-run] [--force-update]
```

## Format attendu

Export du plugin « Export to JSON » pour PHPMyAdmin :
- Array racine avec objets `type=header|database|table`
- Table `capability` avec clé `data` contenant les lignes

## Mapping ancien → nouveau

| Ancien (export) | Nouveau (capabilities) | Notes |
|-----------------|------------------------|-------|
| `name` | `name` | Obligatoire |
| `description` | `description` | null si vide |
| `effect` | `effect` | null si vide |
| `level` | `level` | défaut "0" |
| `pa` | `pa` | défaut "0" |
| `po` | `po` | défaut "" |
| `po_editable` "0"/"1" | `po_editable` bool | |
| `time_before_use_again` | `time_before_use_again` | |
| `casting_time` | `casting_time` | |
| `duration` | `duration` | |
| `element` | `element` | converti en int (0-29) via mapping legacy |
| `is_magic` "0"/"1" | `is_magic` bool | |
| `ritual_available` "0"/"1" | `ritual_available` bool | |
| `powerful` | `powerful` | null si vide |
| `usable` "0"/"1" | `state` | "1" → playable, "0" → draft |
| — | `read_level` | 0 (défaut) |
| — | `write_level` | 3 (défaut) |
| — | `image` | null |
| — | `created_by` | null |

**Champs non migrés** : `id`, `uniqid`, `timestamp_add`, `timestamp_updated` (les IDs sont régénérés, timestamps = now).

## Comportement

- **Doublons** : détection par `name`. Par défaut, les capacités existantes sont ignorées.
- **`--force-update`** : met à jour les capacités portant le même nom.
- **Transaction** : tout l’import est en une transaction (rollback en cas d’erreur).
- **Relations** : les pivots `capability_creature` et `capability_specialization` ne sont pas importés par cette commande. Une migration séparée pourra les lier si les exports existent.
