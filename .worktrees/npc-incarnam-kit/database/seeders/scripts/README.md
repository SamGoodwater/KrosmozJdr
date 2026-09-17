# Scripts (`database/seeders/scripts/`)

Les anciens scripts de maintenance ciblant les fichiers PHP de caractéristiques (`characteristics.php`, `characteristic_icons_colors.php`, etc.) ont été **retirés** : la source versionnée est désormais **`../data/characteristic-definitions/**/*.json`**.

Pour régénérer les définitions depuis la BDD après modification en admin :  
`php artisan scrapping:seeders:export --characteristics`
