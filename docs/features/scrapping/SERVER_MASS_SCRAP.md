# Checklist — scrap massif serveur

Procédure pour récupérer **tout** le catalogue DofusDB d’un coup, avec conversion et
mise en forme déjà correctes à l’import.

## Prérequis (une fois sur le serveur)

1. Code à jour + `.env` (DB, cache HTTP DofusDB, filesystems images).
2. Socle seedé :
   ```bash
   php artisan scrapping:setup
   ```
3. Vérifier les freins **avant** l’import :
   ```bash
   php artisan characteristics:audit-definitions
   php artisan scrapping:audit --fail-on-review --json
   php artisan scrapping:effects:quality-gate --allow-empty --json
   ```
4. Optionnel : mesurer le bruit effets déjà en base :
   ```bash
   php artisan scrapping:effects:audit-quality --json
   php artisan scrapping:effects:audit-autre --json
   ```

## Import recommandé

`scrapping:run` active la **quality gate pré-import** par défaut (hors `--simulate`).
Après un import qui inclut `spell`, la **gate effets** s’exécute aussi.

```bash
# Types / catalogues d’abord si besoin
php artisan scrapping:types:seed

# Monstres + objets + sorts (exemple : sans plafond d’items)
php artisan scrapping:run \
  --entity=monster,item,spell \
  --max-items=0 \
  --max-pages=0 \
  --update-mode=force \
  --output=summary
```

Pour forcer un run sans frein (diagnostic uniquement) :

```bash
php artisan scrapping:run --entity=monster --no-quality-gate ...
```

Pipeline sorts dédié (import + gate effets) :

```bash
php artisan scrapping:effects:pipeline --max-items=0 --max-pages=0
```

## Après import

```bash
php artisan scrapping:effects:quality-gate --json
php artisan scrapping:effects:audit-autre --json
php artisan scrapping:audit --fail-on-review --json
```

Interprétation :

- Couverture conversion effets attendue ≥ 99 %, mappings `characteristic` sans clé = 0.
- Taux `autre` élevé est **normal** tant que glyphes / pièges / placeholders `#1` restent hors périmètre
  (voir [MAPPINGS_HORS_PERIMETRE.md](../effects/MAPPINGS_HORS_PERIMETRE.md)).
- Les téléports / échanges déjà mappés (`déplacer`) ne doivent plus rester en `autre` après **re-seed**
  des mappings + `scrapping:effects:reapply-mappings` (ou re-import des sorts concernés).

## Re-sync ciblé

Si seuls les mappings d’effets ont changé :

```bash
php artisan db:seed --class=Database\\Seeders\\DofusdbEffectMappingSeeder --force
php artisan scrapping:effects:reapply-mappings
# Optionnel si les params doivent être recalculés depuis DofusDB :
# php artisan scrapping:run --entity=spell --update-mode=force --max-items=0
```
