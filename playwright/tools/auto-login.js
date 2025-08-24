#!/usr/bin/env node

/**
 * Auto Login - Script de connexion automatique pour les tests Playwright
 * 
 * Ce script permet de se connecter automatiquement avec différents types d'utilisateurs
 * pour faciliter les tests E2E sur KrosmozJDR.
 * 
 * Usage :
 * node playwright/tasks/auto-login.js [url] [user-type] [options]
 * 
 * Types d'utilisateurs disponibles :
 * - super-admin : contact@jdr.iota21.fr / 0000
 * - test-user : test@example.com / password
 * - custom : avec identifiants personnalisés
 * 
 * Options :
 * --screenshot=true/false    Prendre des captures d'écran
 * --wait=2000               Temps d'attente après connexion
 * --verify=true/false       Vérifier la connexion réussie
 */

import { runPlaywrightTask } from '../playwright-universal.js';
import { TEST_USERS } from '../config/test-users.js';

// Configuration par défaut
const DEFAULT_CONFIG = {
  url: 'http://localhost:8000',
  userType: 'test-user',
  customUser: null,
  screenshot: true,
  wait: 2000,
  verify: true,
  headless: false
};

// Parse les arguments de ligne de commande
function parseArgs() {
  const args = process.argv.slice(2);
  const config = { ...DEFAULT_CONFIG };
  
  let urlSet = false;
  let userTypeSet = false;
  
  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    
    if (arg.startsWith('--')) {
      const [key, value] = arg.slice(2).split('=');
      if (key === 'screenshot') {
        config.screenshot = value === 'true';
      } else if (key === 'wait') {
        config.wait = parseInt(value);
      } else if (key === 'verify') {
        config.verify = value === 'true';
      } else if (key === 'headless') {
        config.headless = value === 'true';
      }
    } else if (!urlSet && (arg.startsWith('http://') || arg.startsWith('https://'))) {
      config.url = arg;
      urlSet = true;
    } else if (!userTypeSet && TEST_USERS[arg]) {
      config.userType = arg;
      userTypeSet = true;
    }
  }
  
  return config;
}

// Vérifie si l'utilisateur est connecté
async function verifyLogin(pw) {
  try {
    // Vérifier la présence d'éléments indiquant une connexion réussie
    const isLoggedIn = await pw.evaluate(() => {
      // Chercher des éléments qui indiquent une connexion réussie
      const indicators = [
        'a[href*="logout"]', // Lien de déconnexion
        '[data-testid="user-menu"]', // Menu utilisateur
        '.user-avatar', // Avatar utilisateur
        'a[href*="profile"]', // Lien vers le profil
        'button[onclick*="logout"]', // Bouton de déconnexion
        '.authenticated-user', // Classe CSS pour utilisateur connecté
        'nav a[href*="dashboard"]', // Navigation dashboard
        '.user-name', // Nom d'utilisateur affiché
        '[data-user-id]' // Attribut data avec ID utilisateur
      ];
      
      for (const selector of indicators) {
        if (document.querySelector(selector)) {
          return true;
        }
      }
      
      // Vérifier l'URL (redirection après connexion)
      const currentUrl = window.location.pathname;
      const loginUrl = '/login';
      
      return currentUrl !== loginUrl && !currentUrl.includes('login');
    });
    
    return isLoggedIn;
  } catch (error) {
    console.log('⚠️ Erreur lors de la vérification de connexion:', error.message);
    return false;
  }
}

// Fonction principale de connexion automatique
async function autoLogin(config) {
  console.log(`🔐 Connexion automatique - KrosmozJDR`);
  console.log(`🌐 URL: ${config.url}`);
  console.log(`👤 Type d'utilisateur: ${config.userType}`);
  console.log(`📸 Screenshots: ${config.screenshot ? 'Activés' : 'Désactivés'}`);
  console.log(`⏳ Attente: ${config.wait}ms`);
  console.log(`✅ Vérification: ${config.verify ? 'Activée' : 'Désactivée'}`);
  
  // Récupérer les identifiants
  let credentials;
  if (config.userType === 'custom' && config.customUser) {
    credentials = config.customUser;
    console.log(`👤 Utilisateur personnalisé: ${credentials.identifier}`);
  } else if (TEST_USERS[config.userType]) {
    credentials = TEST_USERS[config.userType];
    console.log(`👤 ${credentials.description}: ${credentials.identifier}`);
  } else {
    console.error(`❌ Type d'utilisateur non reconnu: ${config.userType}`);
    console.log(`📋 Types disponibles: ${Object.keys(TEST_USERS).join(', ')}`);
    process.exit(1);
  }
  
  try {
    await runPlaywrightTask('Auto Login', async (pw) => {
      // Navigation vers la page de connexion
      console.log(`\n🚀 Navigation vers la page de connexion...`);
      await pw.navigate(`${config.url}/login`);
      
      if (config.screenshot) {
        await pw.screenshot('login-page.png');
      }
      
      // Attendre que la page soit chargée
      await pw.waitForSelector('form', { timeout: 10000 });
      
      // Remplir le formulaire de connexion
      console.log(`📝 Remplissage du formulaire...`);
      await pw.fill('input[name="identifier"]', credentials.identifier);
      await pw.fill('input[name="password"]', credentials.password);
      
      // Optionnel : cocher "Se souvenir de moi"
      const rememberCheckbox = await pw.page.$('input[name="remember"]');
      if (rememberCheckbox) {
        await pw.click('input[name="remember"]');
        console.log(`✅ "Se souvenir de moi" activé`);
      }
      
      if (config.screenshot) {
        await pw.screenshot('login-form-filled.png');
      }
      
      // Soumettre le formulaire
      console.log(`🔐 Soumission du formulaire...`);
      await pw.click('button[type="submit"]');
      
      // Attendre la redirection
      console.log(`⏳ Attente de la redirection...`);
      await pw.waitForTimeout(config.wait);
      
      // Vérifier la connexion si demandé
      if (config.verify) {
        console.log(`🔍 Vérification de la connexion...`);
        const isLoggedIn = await verifyLogin(pw);
        
        if (isLoggedIn) {
          console.log(`✅ Connexion réussie !`);
          
          // Récupérer des informations sur l'utilisateur connecté
          const userInfo = await pw.evaluate(() => {
            const userName = document.querySelector('.user-name, [data-user-name], .navbar .user-info')?.textContent?.trim();
            const userEmail = document.querySelector('[data-user-email]')?.getAttribute('data-user-email');
            const currentUrl = window.location.href;
            
            return {
              userName: userName || 'Non trouvé',
              userEmail: userEmail || 'Non trouvé',
              currentUrl: currentUrl
            };
          });
          
          console.log(`👤 Utilisateur connecté: ${userInfo.userName}`);
          console.log(`📧 Email: ${userInfo.userEmail}`);
          console.log(`🔗 URL actuelle: ${userInfo.currentUrl}`);
        } else {
          console.log(`❌ Échec de la connexion - Vérification échouée`);
          
          // Vérifier s'il y a des erreurs
          const errors = await pw.evaluate(() => {
            const errorElements = document.querySelectorAll('.text-error, .error-message, .alert-error, [class*="error"]');
            return Array.from(errorElements).map(el => el.textContent.trim()).filter(text => text.length > 0);
          });
          
          if (errors.length > 0) {
            console.log(`❌ Erreurs détectées:`);
            errors.forEach(error => console.log(`   - ${error}`));
          }
        }
      } else {
        console.log(`✅ Formulaire soumis (vérification désactivée)`);
      }
      
      if (config.screenshot) {
        await pw.screenshot('after-login.png');
      }
      
      console.log(`\n🎯 Connexion automatique terminée`);
    });
    
  } catch (error) {
    console.error(`❌ Erreur lors de la connexion automatique:`, error.message);
    process.exit(1);
  }
}

// Fonction pour créer un utilisateur de test
async function createTestUser(config) {
  console.log(`\n🔧 Création d'un utilisateur de test...`);
  
  try {
    await runPlaywrightTask('Create Test User', async (pw) => {
      // Navigation vers la page d'inscription
      await pw.navigate(`${config.url}/register`);
      
      if (config.screenshot) {
        await pw.screenshot('register-page.png');
      }
      
      // Remplir le formulaire d'inscription
      const testUser = {
        name: 'TestUser_' + Date.now(),
        email: `test${Date.now()}@example.com`,
        password: 'password123',
        password_confirmation: 'password123'
      };
      
      console.log(`📝 Création de l'utilisateur: ${testUser.name} (${testUser.email})`);
      
      await pw.fill('input[name="name"]', testUser.name);
      await pw.fill('input[name="email"]', testUser.email);
      await pw.fill('input[name="password"]', testUser.password);
      await pw.fill('input[name="password_confirmation"]', testUser.password_confirmation);
      
      if (config.screenshot) {
        await pw.screenshot('register-form-filled.png');
      }
      
      // Soumettre le formulaire
      await pw.click('button[type="submit"]');
      await pw.waitForTimeout(config.wait);
      
      if (config.screenshot) {
        await pw.screenshot('after-register.png');
      }
      
      console.log(`✅ Utilisateur créé avec succès !`);
      console.log(`📋 Identifiants:`);
      console.log(`   Nom: ${testUser.name}`);
      console.log(`   Email: ${testUser.email}`);
      console.log(`   Mot de passe: ${testUser.password}`);
      
      return testUser;
    });
    
  } catch (error) {
    console.error(`❌ Erreur lors de la création d'utilisateur:`, error.message);
  }
}

// Point d'entrée principal
if (import.meta.url === `file://${process.argv[1]}`) {
  const config = parseArgs();
  
  // Afficher l'aide si demandé
  if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(`
🔐 Auto Login - Script de connexion automatique pour KrosmozJDR

Usage :
  node playwright/tools/auto-login.js [url] [user-type] [options]

Arguments :
  url                    URL de l'application (défaut: http://localhost:8000)
  user-type              Type d'utilisateur (défaut: test-user)

Types d'utilisateurs disponibles :
  super-admin            super-admin@test.fr / 0000 (Super administrateur)
  test-user              test-user@test.fr / password (Utilisateur de test)
  admin                  admin@test.fr / password (Administrateur)
  game-master            gm@test.fr / password (Meneur de jeu)
  player                 player@test.fr / password (Joueur)
  custom                 Utilisateur personnalisé (avec --custom-user)

Options :
  --screenshot=true/false    Prendre des captures d'écran (défaut: true)
  --wait=ms                  Temps d'attente après connexion (défaut: 2000)
  --verify=true/false        Vérifier la connexion réussie (défaut: true)
  --headless=true/false      Mode headless (défaut: false)
  --help, -h                 Afficher cette aide

Exemples :
  # Connexion avec l'utilisateur de test par défaut
  node playwright/tools/auto-login.js

  # Connexion avec le super admin
  node playwright/tools/auto-login.js http://localhost:8000 super-admin

  # Connexion sans captures d'écran
  node playwright/tools/auto-login.js --screenshot=false

  # Connexion avec attente plus longue
  node playwright/tools/auto-login.js --wait=5000

  # Connexion en mode headless
  node playwright/tools/auto-login.js --headless=true

  # Connexion sans vérification
  node playwright/tools/auto-login.js --verify=false

  # Combinaison d'options
  node playwright/tools/auto-login.js http://localhost:8000 super-admin --screenshot=true --wait=3000 --verify=true
`);
    process.exit(0);
  }
  
  // Exécuter la connexion automatique
  autoLogin(config).catch(console.error);
}

// Export pour utilisation dans d'autres scripts
export { autoLogin, createTestUser, TEST_USERS, verifyLogin };
