# 🎯 Scraping DofusDB - Documentation

## 📋 Vue d'ensemble

Ce dossier contient la documentation et les scripts pour l'analyse et la collecte automatique des données depuis l'API de [dofusdb.fr](https://dofusdb.fr/fr/).

## 🎯 Objectifs

1. **Analyser l'API DofusDB** pour comprendre sa structure
2. **Identifier tous les endpoints** et leurs paramètres
3. **Mapper les relations** entre les différentes entités
4. **Créer des scripts de collecte** automatique
5. **Intégrer les données** dans KrosmozJDR

## 📁 Structure du dossier

```
22-Scraping/
├── README.md                           # Cette documentation
├── API_ANALYSIS.md                     # Analyse détaillée de l'API
├── ENDPOINTS_MAPPING.md                # Mapping des endpoints
├── DATA_STRUCTURE.md                   # Structure des données
├── SCRAPING_SCRIPTS/                   # Scripts de collecte
│   ├── dofusdb-explorer.php           # Script d'exploration Laravel
│   ├── playwright-analyzer.js         # Script d'analyse Playwright
│   └── data-collector.php             # Collecteur de données
└── PROGRESS.md                         # Suivi des avancées
```

## 🔍 Endpoints API Identifiés

### Endpoints Principaux
- **Objets/Items** : `https://api.dofusdb.fr/items`
- **Monstres** : `https://api.dofusdb.fr/monsters`
- **Sorts** : `https://api.dofusdb.fr/spells`
- **Effets** : `https://api.dofusdb.fr/effects`
- **Niveaux de sorts** : `https://api.dofusdb.fr/spell-levels`
- **Types d'objets** : `https://api.dofusdb.fr/item-types`
- **Caractéristiques** : `https://api.dofusdb.fr/characteristics`
- **Critères** : `https://api.dofusdb.fr/criterion`

### Paramètres de Requête
- `$sort[id]=-1` : Tri par ID décroissant
- `$skip=X` : Pagination (skip X éléments)
- `$limit=Y` : Limite de résultats
- `lang=fr` : Langue française
- `typeId[$ne]=203` : Exclure le type 203
- `typeId[$in][]=1` : Inclure le type 1
- `level[$gte]=0&level[$lte]=200` : Filtre par niveau
- `$populate=false` : Ne pas peupler les relations

## 🗺️ Mapping avec KrosmozJDR

| DofusDB | KrosmozJDR | Statut |
|---------|------------|--------|
| Monsters | Creatures | ✅ Identifié |
| Items | Items | ✅ Identifié |
| Spells | Spells | ✅ Identifié |
| Effects | Attributes/Capabilities | 🔄 À analyser |
| Item Types | Item Types | 🔄 À analyser |
| Characteristics | Attributes | 🔄 À analyser |

## 🚀 Prochaines Étapes

1. **Phase 1** : Analyse complète de l'API ✅
2. **Phase 2** : Création des scripts d'exploration 🔄
3. **Phase 3** : Collecte des données de test
4. **Phase 4** : Mapping complet des entités
5. **Phase 5** : Scripts de collecte automatique
6. **Phase 6** : Intégration dans KrosmozJDR

## 📊 Progression

- [x] Identification des endpoints principaux
- [x] Analyse des paramètres de requête
- [x] Création de la documentation
- [ ] Analyse détaillée de chaque endpoint
- [ ] Mapping complet des relations
- [ ] Scripts de collecte automatique
- [ ] Tests de collecte
- [ ] Intégration dans KrosmozJDR

## 🔧 Outils Utilisés

- **Playwright** : Analyse des requêtes réseau et navigation
- **Laravel Artisan** : Scripts de collecte et traitement
- **cURL/wget** : Tests d'endpoints API
- **Documentation Markdown** : Structuration des connaissances

## 📝 Notes Importantes

- Respecter les limites de l'API (rate limiting)
- Implémenter des délais entre les requêtes
- Sauvegarder les données collectées
- Documenter les changements d'API
- Tester régulièrement la collecte

---

*Dernière mise à jour : $(date)*
