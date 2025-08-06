# Helpers — Bonnes pratiques

- Nom explicite, en anglais, en camelCase (ex : `formatDate`, `getUserRole`).
- Chaque helper doit avoir un docbloc en français expliquant :
  - Le but du helper
  - Les paramètres attendus (types, valeurs par défaut)
  - La valeur de retour
  - Un exemple d’utilisation
- Chaque helper doit être testé (PHPUnit pour Laravel, Vitest/Jest pour Vue).
- Pas de logique métier complexe dans les helpers : ils servent à factoriser des utilitaires simples et réutilisables.

### Exemple (Laravel)
```php
/**
 * Formate une date au format français.
 *
 * @param  string|\DateTimeInterface  $date  Date à formater
 * @return string  Date formatée (ex : 01/01/2024)
 *
 * @example
 *   formatDate('2024-01-01'); // "01/01/2024"
 */
function formatDate($date) { /* ... */ }
``` 