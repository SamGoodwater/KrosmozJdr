# 🔐 Guide de Connexion Automatique Playwright - KrosmozJDR

## 📋 Vue d'ensemble

Le système de connexion automatique Playwright permet de se connecter facilement avec différents types d'utilisateurs pour tester les fonctionnalités authentifiées de KrosmozJDR.

## 🚀 Utilisation rapide

### Commandes de base
```bash
# Connexion avec l'utilisateur de test par défaut
node playwright/run.js login-test

# Connexion avec le super administrateur
node playwright/run.js login-admin

# Connexion avec un type d'utilisateur spécifique
node playwright/run.js login http://localhost:8000 super-admin
node playwright/run.js login http://localhost:8000 test-user
node playwright/run.js login http://localhost:8000 admin
node playwright/run.js login http://localhost:8000 game-master
node playwright/run.js login http://localhost:8000 player
```

### Script direct
```bash
# Utilisation directe du script auto-login
node playwright/tools/auto-login.js
node playwright/tools/auto-login.js http://localhost:8000 super-admin
node playwright/tools/auto-login.js --screenshot=false --wait=5000
```

## 👤 Types d'utilisateurs disponibles

| Type | Identifiant | Mot de passe | Description | Rôle |
|------|-------------|--------------|-------------|------|
| `super-admin` | `super-admin@test.fr` | `0000` | Super administrateur | `super_admin` |
| `test-user` | `test@example.com` | `password` | Utilisateur de test standard | `user` |
| `admin` | `admin@test.com` | `password` | Administrateur | `admin` |
| `game-master` | `gm@test.com` | `password` | Meneur de jeu | `game_master` |
| `player` | `player@test.com` | `password` | Joueur | `player` |

## ⚙️ Options de configuration

### Options disponibles 
- `--screenshot=true/false` : Prendre des captures d'écran (défaut: `true`)
- `--wait=ms` : Temps d'attente après connexion (défaut: `2000`)
- `--verify=true/false` : Vérifier la connexion réussie (défaut: `true`)
- `--headless=true/false` : Mode headless (défaut: `false`)

### Exemples d'utilisation
```bash
# Connexion sans captures d'écran
node playwright/tasks/auto-login.js --screenshot=false

# Connexion avec attente plus longue
node playwright/tasks/auto-login.js --wait=5000

# Connexion en mode headless
node playwright/tasks/auto-login.js --headless=true

# Connexion sans vérification
node playwright/tasks/auto-login.js --verify=false

# Combinaison d'options
node playwright/tasks/auto-login.js super-admin --screenshot=true --wait=3000 --verify=true
```

## 🧪 Tests de workflow authentifié

### Test simple
```bash
# Test avec l'utilisateur de test par défaut
node playwright/tasks/test-authenticated-workflow.js

# Test avec le super admin
node playwright/tasks/test-authenticated-workflow.js http://localhost:8000 super-admin

# Test de tous les rôles
node playwright/tasks/test-authenticated-workflow.js http://localhost:8000 all
```

### Test avec options
```bash
# Test sans captures d'écran
node playwright/tasks/test-authenticated-workflow.js --screenshot=false

# Test avec attente plus longue
node playwright/tasks/test-authenticated-workflow.js --wait=5000

# Test en mode headless
node playwright/tasks/test-authenticated-workflow.js --headless=true
```

## 🔧 Configuration avancée

### Fichier de configuration
Les utilisateurs de test sont configurés dans `playwright/config/test-users.js` :

```javascript
export const TEST_USERS = {
  'super-admin': {
    identifier: 'super-admin@test.fr',
    password: '0000',
    description: 'Super administrateur',
    role: 'super_admin',
    permissions: ['all']
  },
  // ... autres utilisateurs
};
```

### Environnements
```javascript
export const ENVIRONMENTS = {
  'local': {
    url: 'http://localhost:8000',
    database: 'krosmozDB',
    description: 'Environnement de développement local'
  },
  'staging': {
    url: 'https://staging.krosmozjdr.com',
    database: 'krosmozDB_staging',
    description: 'Environnement de staging'
  },
  'production': {
    url: 'https://krosmozjdr.com',
    database: 'krosmozDB_prod',
    description: 'Environnement de production'
  }
};
```

### Types de configuration de test
```javascript
export const TEST_CONFIG = {
  default: {
    screenshot: true,
    wait: 2000,
    verify: true,
    headless: false,
    timeout: 30000
  },
  fast: {
    screenshot: false,
    wait: 1000,
    verify: false,
    headless: true,
    timeout: 15000
  },
  thorough: {
    screenshot: true,
    wait: 5000,
    verify: true,
    headless: false,
    timeout: 60000
  }
};
```

## 📝 Intégration dans vos scripts

### Utilisation dans un script personnalisé
```javascript
import { autoLogin } from './playwright/tools/auto-login.js';

// Dans votre script de test
async function monTest() {
  const config = {
    url: 'http://localhost:8000',
    userType: 'super-admin',
    screenshot: true,
    wait: 2000,
    verify: true
  };
  
  await autoLogin(config);
  
  // Votre logique de test ici...
}
```

### Utilisation avec la classe PlaywrightUniversal
```javascript
import { PlaywrightUniversal } from './playwright/playwright-universal.js';
import { TEST_USERS } from './playwright/config/test-users.js';

const pw = new PlaywrightUniversal();
await pw.init();

// Connexion manuelle
const user = TEST_USERS['super-admin'];
await pw.navigate('http://localhost:8000/login');
await pw.fill('input[name="identifier"]', user.identifier);
await pw.fill('input[name="password"]', user.password);
await pw.click('button[type="submit"]');

// Votre logique de test...
```

## 🔍 Vérification de connexion

Le système vérifie automatiquement si la connexion a réussi en cherchant :

1. **Éléments d'interface** :
   - Liens de déconnexion (`a[href*="logout"]`)
   - Menu utilisateur (`[data-testid="user-menu"]`)
   - Avatar utilisateur (`.user-avatar`)
   - Liens vers le profil (`a[href*="profile"]`)

2. **URL de redirection** :
   - Vérifie que l'utilisateur n'est plus sur `/login`
   - Confirme la redirection vers la page d'accueil

3. **Messages d'erreur** :
   - Détecte les erreurs d'authentification
   - Affiche les messages d'erreur trouvés

## 📸 Captures d'écran

Le système prend automatiquement des captures d'écran à chaque étape :

- `login-page.png` : Page de connexion
- `login-form-filled.png` : Formulaire rempli
- `after-login.png` : Après connexion
- `workflow-step1-logged-in.png` : Première étape du workflow
- `workflow-step2-profile.png` : Page de profil
- `workflow-step4-logged-out.png` : Après déconnexion

## 🚨 Gestion des erreurs

### Erreurs courantes
1. **Utilisateur non trouvé** : Vérifiez que l'utilisateur existe dans la base de données
2. **Mot de passe incorrect** : Vérifiez les identifiants dans la configuration
3. **Page de connexion inaccessible** : Vérifiez que le serveur Laravel fonctionne
4. **Timeout** : Augmentez le temps d'attente avec `--wait=5000`

### Debug
```bash
# Mode verbeux avec captures d'écran
node playwright/tools/auto-login.js super-admin --screenshot=true --wait=5000

# Mode headless pour tests rapides
node playwright/tools/auto-login.js test-user --headless=true --screenshot=false
```

## 🔄 Workflow complet

### Exemple de workflow de test
```bash
# 1. Connexion automatique
node playwright/run.js login-admin

# 2. Test de navigation
node playwright/run.js nav

# 3. Capture d'écran
node playwright/run.js ss test-result.png

# 4. Monitoring console
node playwright/run.js monitor

# 5. Test de workflow complet
node playwright/tasks/test-authenticated-workflow.js super-admin
```

## 📚 Ressources supplémentaires

- [Documentation Playwright officielle](https://playwright.dev/docs/)
- [Guide des outils Playwright locaux](../../README.md)
- [Configuration des utilisateurs de test](../config/test-users.js)
- [Scripts de tâches](../tasks/)

## 🤝 Contribution

Pour ajouter de nouveaux types d'utilisateurs ou modifier la configuration :

1. Éditez `playwright/config/test-users.js`
2. Ajoutez les nouveaux utilisateurs dans `TEST_USERS`
3. Mettez à jour ce guide
4. Testez avec `node playwright/tools/auto-login.js --help`
