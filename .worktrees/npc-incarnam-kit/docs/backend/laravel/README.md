# Laravel

L'application utilise la structure Laravel classique : modèles Eloquent, contrôleurs HTTP, services métier, policies et commandes Artisan.

## Organisation

- `app/Models/` : modèles (`User`, `Page`, `Section`, `Entity/*`, `Type/*`, `Characteristic*`, `Effect*`).
- `app/Http/Controllers/` : `Entity`, `Admin`, `Auth`, `Api`, `Scrapping`, `Type`.
- `app/Http/Requests/` : Form Requests dédiées.
- `app/Services/` : logique métier.
- `app/Policies/` : autorisations.
- `app/Jobs/` et `app/Console/Commands/` : traitements asynchrones et CLI.

## Exemple

Pour une action d'entité : route → contrôleur `app/Http/Controllers/Entity/*Controller.php` → Form Request → modèle/service → Resource Inertia/API.
