# Routes

Les routes sont modulaires. `routes/web.php` et `routes/api.php` incluent des fichiers spécialisés.

## Groupes

- `routes/web/` : pages, utilisateurs, notifications, fichiers, légal, feedback.
- `routes/entities/` : CRUD web des entités JDR.
- `routes/admin/` : back-office.
- `routes/api/` : tables, scrapping, CMS, global search, effets, caractéristiques.

Les APIs utilisées par Inertia sont souvent sous middleware `web` + `auth` : elles utilisent la session et la protection CSRF.
