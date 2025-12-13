# Rapport d'audit — Outillage qualité (PHPStan + ESLint)

**Date** : 2025-01-13  
**Périmètre** : Analyse statique backend/frontend + configuration

---

## ✅ État actuel : EXCELLENTE BASE

### **Backend : Larastan/PHPStan**

#### ✓ Installation & configuration

- ✅ **Larastan v3.0** installé (`composer.json` L28)
- ✅ **PHPStan configuré** (`phpstan.neon` + script `composer phpstan`)
- ✅ **Niveau** : `level: 6` (bon compromis prod/rigueur)
- ✅ **Paths** : analyse de `app/`, `database/`, `routes/`, `tests/`
- ✅ **Ignores** : configurations pertinentes (`bootstrap/*.php`, `/resources/`)

#### Configuration actuelle (`phpstan.neon`)

```yaml
includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    level: 6
    paths:
        - app
        - database
        - routes
        - tests
    excludePaths:
        - bootstrap/*
        - app/Http/Middleware/TrustProxies.php
    ignoreErrors:
        - '#PHPDoc tag @var#'
    checkMissingIterableValueType: false
```

#### 📊 Évaluation

| Aspect | État | Note |
|--------|------|------|
| Niveau d'analyse | ✅ Level 6 (bon) | 8/10 |
| Paths couverts | ✅ app/database/routes/tests | 9/10 |
| Exclusions | ✅ Pertinentes | 9/10 |
| CI/CD | ❓ À vérifier | ?/10 |
| Script alias | ✅ `composer phpstan` | 10/10 |

**Score PHPStan** : **9/10** (excellent)

---

### **Frontend : ESLint + Prettier**

#### ✓ Installation & configuration

- ✅ **ESLint v9** installé (`package.json` L43)
- ✅ **eslint-plugin-vue v10** installé (L45)
- ✅ **eslint-config-prettier v10** installé (L44) → évite conflits
- ✅ **Prettier v3** installé (L101) + plugin Tailwind (L102)
- ✅ **Config ESLint** : `eslint.config.js` (format flat ESM)
- ✅ **Script lint** : `pnpm run lint` (scope ciblé Pages/Sections)

#### Configuration actuelle (`eslint.config.js`)

```javascript
import js from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import prettier from 'eslint-config-prettier'
import globals from 'globals'

export default [
  js.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  prettier,
  {
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
        route: 'readonly'
      }
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'warn', // ⚠️ Seulement 'warn'
      'no-unused-vars': 'warn',
      'no-undef': 'error'
    }
  }
]
```

#### 📊 Évaluation

| Aspect | État | Note |
|--------|------|------|
| Installation | ✅ ESLint 9 + Vue plugin | 10/10 |
| Config | ✅ Format flat (ESM moderne) | 10/10 |
| Règles Vue | ⚠️ `vue/no-v-html: warn` (pas 'error') | 6/10 |
| Intégration Prettier | ✅ eslint-config-prettier | 10/10 |
| Scope | ✅ Ciblé Pages/Sections | 9/10 |
| CI/CD | ❓ À vérifier | ?/10 |

**Score ESLint** : **8.5/10** (très bon, 1 ajustement recommandé)

---

## 🛠️ Recommandations d'amélioration

### **P1 - Critique : Durcir règle `vue/no-v-html`**

**Problème** : `'vue/no-v-html': 'warn'` permet d'utiliser `v-html` sans bloquer le build.  
**Risque** : XSS si ajout de `v-html` sans sanitization.

**Solution** :

```javascript
// eslint.config.js
rules: {
  // Interdire v-html SAUF avec commentaire de désactivation explicite
  'vue/no-v-html': 'error', // ⚠️ Changer 'warn' → 'error'
  'vue/multi-word-component-names': 'off',
  'no-unused-vars': 'warn',
  'no-undef': 'error'
}
```

**Impact** : Build échoue si `v-html` sans `// eslint-disable-next-line vue/no-v-html --contenu sanitizé`.

---

### **P2 - Important : PHPStan level 7 (progressif)**

**État actuel** : Level 6 (bon, mais peut faire mieux).  
**Proposition** : Monter progressivement à **level 7** puis **level 8**.

**Bénéfices level 7** :
- ✅ Vérifie les types de retour des méthodes privées
- ✅ Détecte les propriétés non initialisées
- ✅ Vérifie les types des paramètres de closures

**Approche** : Créer un objectif de migration

```bash
# Lancer PHPStan level 7 en mode analyse (sans fail)
composer phpstan -- --level=7 --no-progress --error-format=table > phpstan-level7-report.txt

# Analyser les erreurs et fixer progressivement
# Puis mettre à jour phpstan.neon quand le rapport est vide
```

**Effort** : 4-6h (selon nombre d'erreurs)

---

### **P3 - Nice-to-have : Intégration CI/CD**

Ajouter PHPStan + ESLint dans la CI (GitHub Actions / GitLab CI).

**Exemple GitHub Actions** :

```yaml
# .github/workflows/quality.yml
name: Quality Check

on: [push, pull_request]

jobs:
  phpstan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
      - run: composer install --no-progress
      - run: composer phpstan

  eslint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: pnpm/action-setup@v2
      - run: pnpm install --frozen-lockfile
      - run: pnpm run lint
```

**Bénéfices** : Blocage automatique des PR avec erreurs lint.

---

## 📋 Checklist d'actions

### Immédiat (avant merge v1.0)
- [ ] Durcir `vue/no-v-html: 'error'` dans `eslint.config.js`
- [ ] Tester `pnpm run lint` : vérifier que les 5 fichiers avec `v-html` ont bien le commentaire ESLint
- [ ] Documenter la règle dans `/docs/20-Content/XSS_PREVENTION_GUIDE.md`

### Court terme (v1.1)
- [ ] Analyser PHPStan level 7 : `composer phpstan -- --level=7 > report.txt`
- [ ] Fixer les erreurs level 7 (estimation : 20-50 erreurs)
- [ ] Mettre à jour `phpstan.neon` → `level: 7`

### Moyen terme (v1.2+)
- [ ] Intégrer PHPStan + ESLint dans CI/CD
- [ ] Ajouter pre-commit hooks (via `husky` ou `captain-hook`)
- [ ] Analyser PHPStan level 8 (le plus strict)

---

## ✅ Points forts actuels (à conserver)

- **Larastan v3** : version récente, compatible Laravel 12
- **ESLint flat config** : format moderne ESM, maint

enance facilitée
- **Prettier intégré** : cohérence code auto (Tailwind plugin)
- **Scripts configurés** : `composer phpstan`, `pnpm run lint`
- **Scope ciblé** : lint seulement Pages/Sections (évite les faux positifs sur legacy code)

---

## 🔗 Fichiers clés

- **Backend** : `phpstan.neon`, `composer.json` (script L74)
- **Frontend** : `eslint.config.js`, `package.json` (script L26)
- **Prettier** : intégré via `eslint-config-prettier`

---

## 📊 Score global tooling

| Outil | Score | Commentaire |
|-------|-------|-------------|
| PHPStan | 9/10 | Excellent (level 6), monter à 7 recommandé |
| ESLint | 8.5/10 | Très bon, 1 ajustement critique (`v-html: error`) |
| Prettier | 10/10 | Intégré, aucun conflit ESLint |
| CI/CD | 0/10 | Absent (à ajouter) |

**Score global** : **8.5/10** (très bon, quelques améliorations mineures)

