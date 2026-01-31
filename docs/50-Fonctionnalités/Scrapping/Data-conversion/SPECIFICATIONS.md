## Spécifications — Data-conversion (Conversion)

### But
Décrire les exigences de la conversion “config-driven” (DofusDB → KrosmozJDR).

### Exigences
- **Déclaratif** : la conversion est décrite par `mappings[]` dans les configs JSON d’entités.
- **Whitelist stricte** : seules les fonctions présentes dans `resources/scrapping/formatters/registry.json` sont autorisées.
- **Pas de code arbitraire** : aucun `eval`, aucune formule libre, aucune expression dynamique dans les configs.
- **Déterminisme** :
  - les formatters `pure` doivent être déterministes,
  - les `side_effect` doivent être rares et explicitement contrôlés par options (ex: images).
- **Multi-modèles** : une entité source peut produire un payload multi-modèles (ex: monstre → creatures + monsters).

### Références
- Définitions (mapping + formatters) : `Data-conversion/DEFINITIONS.md`
- API preview : `Data-conversion/API.md`

