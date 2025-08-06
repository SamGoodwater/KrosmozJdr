# 🎭 Scripts Playwright - Outils de développement

Ce dossier contient les outils Playwright pour l'automatisation des tests et des tâches de développement sur le projet Krosmoz-JDR.

## 📁 Structure

```
playwright/
├── README.md                 # Ce fichier
├── playwright-universal.js   # Classe universelle Playwright
├── playwright-cli.js         # Interface CLI pour les tâches
├── tasks/                    # Scripts de tâches spécifiques
│   ├── test-login.js        # Test de connexion
│   └── test-navigation.js   # Test de navigation
└── screenshots/              # Captures d'écran générées
```

## 🚀 Utilisation rapide

### Interface CLI
```bash
# Depuis la racine du projet
node playwright/playwright-cli.js help

# Navigation vers localhost:8000
node playwright/playwright-cli.js navigate http://localhost:8000

# Test de connexion
node playwright/playwright-cli.js login http://localhost:8000 user@test.com password123

# Capture d'écran
node playwright/playwright-cli.js screenshot http://localhost:8000 ma-capture.png
```

### Scripts personnalisés
```bash
# Test de navigation
node playwright/tasks/test-navigation.js

# Test de connexion
node playwright/tasks/test-login.js
```

## 🛠️ Développement

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

| Commande | Description | Exemple |
|----------|-------------|---------|
| `navigate` | Navigation vers une URL | `navigate http://localhost:8000` |
| `login` | Test de connexion | `login http://localhost:8000 user@test.com pass123` |
| `screenshot` | Capture d'écran | `screenshot http://localhost:8000 capture.png` |
| `test-form` | Test de formulaire | `test-form http://localhost:8000` |
| `help` | Afficher l'aide | `help` |

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