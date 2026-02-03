# Dossier legacy — Ne pas utiliser pour le chargement

Les configs d'entités utilisées par le pipeline de scrapping (ConfigLoader, CollectService, ConversionService) se trouvent dans :

**`resources/scrapping/config/sources/dofusdb/`**

- `source.json` : configuration de la source DofusDB
- `entities/*.json` : configuration par entité (monster, spell, breed, item, panoply, etc.)

Ce dossier `resources/scrapping/sources/` est un ancien emplacement. Les fichiers qu'il contient ne sont **pas** chargés par l'application. Pour modifier ou ajouter une entité, éditer les JSON dans `resources/scrapping/config/sources/dofusdb/entities/`.
