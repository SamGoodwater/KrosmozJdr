# Répertoire des routes

Les routes sont organisées par thème pour garder des fichiers de taille raisonnable.

- **`web.php`** : point d'entrée web ; inclut auth, **web/** (statics, user, notifications, file, page), **admin/**\*, **entities/**\*, **services/**.
- **`api.php`** : point d'entrée API ; inclut **api/** (auth, scrapping, types, entity-table, tables, entities).

Documentation détaillée : [docs/10-BestPractices/ROUTES_ARCHITECTURE.md](../docs/10-BestPractices/ROUTES_ARCHITECTURE.md).

## Ajouter des routes

- **Web (page statique, user, notifications, médias, pages/sections)** : ajouter dans le fichier `web/<thème>.php` adapté, ou créer `web/<nouveau>.php` et l’inclure dans `web.php`.
- **Web (CRUD entité)** : ajouter dans `entities/<entité>.php` existant ou créer un fichier et l’inclure dans `web.php`.
- **API** : ajouter dans le fichier `api/<thème>.php` adapté, ou créer `api/<nouveau-thème>.php` et l’inclure dans `api.php`.
