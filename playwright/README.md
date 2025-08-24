# 🎭 Scripts Playwright - Outils de développement

Ce dossier contient les outils Playwright pour l'automatisation des tests et des tâches de développement sur le projet Krosmoz-JDR.

## 📁 Structure

```
playwright/
├── README.md                 # Ce fichier
├── playwright-universal.js   # Classe universelle Playwright
├── playwright-cli.js         # Interface CLI pour les tâches
├── run.js                   # Script de raccourci simplifié
├── tools/                   # Outils de base
│   ├── auto-login.js        # Connexion automatique
│   ├── console-monitor.js   # Monitoring de la console
│   └── network-monitor.js   # Monitoring du réseau
├── tasks/                   # Scripts de tâches spécifiques
│   ├── test-login.js        # Test de connexion
│   ├── test-navigation.js   # Test de navigation
│   └── test-authenticated-workflow.js # Tests de workflow authentifié
├── config/                  # Configuration
│   └── test-users.js        # Configuration des utilisateurs de test
├── docs/                    # Documentation
│   └── AUTO_LOGIN_GUIDE.md  # Guide de connexion automatique
├── screenshots/             # Captures d'écran générées
└── temp/                   # Fichiers temporaires (logs, scripts temporaires, screenshots temporaires)
```

## 🚀 Utilisation rapide

### Interface CLI
```bash
# Depuis la racine du projet
node playwright/playwright-cli.js help

# Navigation vers localhost:8000
node playwright/playwright-cli.js navigate http://localhost:8000

# Connexion automatique avec différents types d'utilisateurs
node playwright/playwright-cli.js login http://localhost:8000 super-admin
node playwright/playwright-cli.js login http://localhost:8000 test-user
node playwright/playwright-cli.js login http://localhost:8000 admin

# Capture d'écran
node playwright/playwright-cli.js screenshot http://localhost:8000 ma-capture.png
```

### Scripts personnalisés
```bash
# Test de navigation
node playwright/tasks/test-navigation.js

# Connexion automatique
node playwright/tools/auto-login.js

# Test de workflow authentifié
node playwright/tasks/test-authenticated-workflow.js
```

## 🛠️ Développement

### Organisation des fichiers
- **`tools/`** : Outils de base réutilisables (connexion, monitoring, etc.)
- **`tasks/`** : Scripts de tâches spécifiques qui peuvent servir de temps en temps
- **`temp/`** : Fichiers temporaires (logs, scripts temporaires, screenshots temporaires)
- **`config/`** : Configuration centralisée (utilisateurs de test, etc.)

### Créer un nouveau script de tâche
```javascript
import { runPlaywrightTask } from '../playwright-universal.js';

runPlaywrightTask('Ma nouvelle tâche', async (pw) => {
  await pw.navigate('http://localhost:8000');
  await pw.click('#mon-bouton');
  await pw.screenshot('resultat.png');
});
```

### Utiliser la classe PlaywrightUniversal
```javascript
import { PlaywrightUniversal } from './playwright-universal.js';

const pw = new PlaywrightUniversal({ headless: false });
await pw.init();
await pw.navigate('http://localhost:8000');
await pw.screenshot('test.png');
await pw.close();
```

## 📋 Commandes disponibles

| Commande      | Description                        | Exemple                                         |
|---------------|------------------------------------|-------------------------------------------------|
| `navigate`    | Navigation vers une URL            | `navigate http://localhost:8000`                |
| `login`       | Connexion automatique              | `login http://localhost:8000 super-admin`       |
| `screenshot`  | Capture d'écran                    | `screenshot http://localhost:8000 capture.png`  |
| `test-form`   | Test de formulaire                 | `test-form http://localhost:8000`               |
| `console`     | Monitoring de la console navigateur| `console http://localhost:8000 --output=console.log --timeout=60000` |
| `network`     | Monitoring du réseau (requêtes)    | `network http://localhost:8000 --filter=GET,POST --output=network.log` |
| `help`        | Afficher l'aide                    | `help`                                          |

## 🔧 Configuration

### Variables d'environnement
- `PLAYWRIGHT_DEFAULT_BROWSER=webkit` - Navigateur par défaut (WebKit recommandé pour ARM64)
- `BROWSER=webkit` - Navigateur à utiliser

### Options de la classe PlaywrightUniversal
```javascript
const pw = new PlaywrightUniversal({
  headless: false,           // Mode headless
  screenshotPath: './screenshots', // Dossier des captures
  timeout: 30000             // Timeout en ms
});
```

## 🎯 Cas d'usage

### Tests de développement
- Vérification rapide de l'interface
- Test des formulaires
- Validation des workflows utilisateur
- Capture d'écrans pour documentation

### Tests d'authentification
- Connexion automatique avec différents rôles
- Test des workflows authentifiés
- Validation des permissions utilisateur
- Test de déconnexion

### Debug
- Reproduction de bugs
- Test de nouvelles fonctionnalités
- Validation des interactions utilisateur

## 📝 Notes techniques

- **Navigateur** : WebKit (compatible ARM64/WSL2)
- **Architecture** : Basé sur Playwright 1.54.2
- **Compatibilité** : WSL2 ARM64, Linux ARM64
- **Dépendances** : Node.js 18+, Playwright

## 🔗 Liens utiles

- [Documentation Playwright](https://playwright.dev/docs/)
- [Configuration des tests](https://playwright.dev/docs/test-configuration)
- [Scripts standalone](https://dev.to/philipfong/adding-standalone-or-one-off-scripts-in-your-playwright-suite-3kng)
- [Guide de connexion automatique](docs/AUTO_LOGIN_GUIDE.md)
- [Configuration des utilisateurs de test](config/test-users.js) 

