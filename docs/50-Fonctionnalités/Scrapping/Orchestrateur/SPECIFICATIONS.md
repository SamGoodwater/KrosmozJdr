## Spécifications — Orchestrateur (Scrapping)

### But
Décrire les exigences de coordination du pipeline scrapping.

### Exigences
- **Point d’entrée unique** : l’orchestrateur coordonne collect/conversion/intégration.
- **Stratégie config-driven** : utiliser les configs JSON quand elles existent, fallback legacy sinon.
- **Propagation d’options** : `skip_cache`, `dry_run`, `validate_only`, `force_update`, `with_images`, `include_relations`.
- **Relations** :
  - import en cascade si `include_relations=true`,
  - garde-fous anti-boucle (désactiver la cascade dans les appels récursifs).
- **Résultats cohérents** : structure stable pour UI/CLI/tests.

### Références
- API : `Orchestrateur/API.md`
- Définitions : `Orchestrateur/DEFINITIONS.md`

