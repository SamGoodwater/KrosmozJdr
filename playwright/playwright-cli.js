import { runPlaywrightTask } from './playwright-universal.js';
import { monitorConsole } from './tools/console-monitor.js';
import { monitorNetwork } from './tools/network-monitor.js';

// Récupération des arguments de ligne de commande
const args = process.argv.slice(2);
const command = args[0];
const url = args[1] || 'http://localhost:8000';

// Dictionnaire des tâches disponibles
const tasks = {
  'navigate': async (pw) => {
    await pw.navigate(url);
    await pw.screenshot('navigation.png');
  },
  
  'login': async (pw) => {
    const userType = args[2] || 'test-user';
    const { autoLogin } = await import('./tools/auto-login.js');
    
    const config = {
      url: url,
      userType: userType,
      screenshot: true,
      wait: 2000,
      verify: true,
      headless: false
    };
    
    await autoLogin(config);
  },
  
  'screenshot': async (pw) => {
    await pw.navigate(url);
    const filename = args[2] || 'screenshot.png';
    await pw.screenshot(filename);
  },
  
  'test-form': async (pw) => {
    await pw.navigate(url);
    await pw.screenshot('before-form.png');
    
    // Exemple de remplissage de formulaire
    try {
      await pw.fill('input[name="name"]', 'Test User');
      await pw.fill('input[name="email"]', 'test@example.com');
      await pw.click('button[type="submit"]');
      await pw.waitForNavigation();
      await pw.screenshot('after-form.png');
    } catch (error) {
      console.log('Formulaire non trouvé ou déjà rempli');
    }
  },
  
  'test-validation': async (pw) => {
    // Importer et exécuter le test de validation
    const { runPlaywrightTask: runValidationTest } = await import('./tasks/test-validation-register.js');
    await runValidationTest('Test validation Register', async (pw) => {
      // Navigation vers la page register
      await pw.navigate('http://localhost:8000/register');
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      console.log('📄 Page chargée:', await pw.page.title());
      
      // Capture initiale
      await pw.screenshot('register-initial.png');
      
      // Test 1: Focus sur le champ name (ne doit PAS déclencher de validation)
      console.log('🔍 Test 1: Focus sur le champ name (pas de validation immédiate)');
      await pw.click('input[name="name"]');
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Vérifier qu'il n'y a pas de message d'erreur visible
      const hasErrorAfterFocus = await pw.page.locator('.text-error, .validator, [class*="error"]').count();
      console.log('❌ Messages d\'erreur après focus:', hasErrorAfterFocus);
      
      // Test 2: Blur sur le champ name vide (doit déclencher la validation)
      console.log('🔍 Test 2: Blur sur le champ name vide');
      await pw.click('input[name="email"]'); // Déplacer le focus
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Vérifier qu'il y a maintenant un message d'erreur
      const hasErrorAfterBlur = await pw.page.locator('.text-error, .validator, [class*="error"]').count();
      console.log('❌ Messages d\'erreur après blur:', hasErrorAfterBlur);
      
      // Test 3: Saisie d'un nom valide
      console.log('🔍 Test 3: Saisie d\'un nom valide');
      await pw.click('input[name="name"]');
      await pw.type('input[name="name"]', 'John');
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Vérifier que l'erreur a disparu
      const hasErrorAfterValidInput = await pw.page.locator('.text-error, .validator, [class*="error"]').count();
      console.log('❌ Messages d\'erreur après saisie valide:', hasErrorAfterValidInput);
      
      // Test 4: Test du champ email
      console.log('🔍 Test 4: Test du champ email');
      await pw.click('input[name="email"]');
      await pw.type('input[name="email"]', 'invalid-email');
      await pw.click('input[name="password"]'); // Blur
      await new Promise(resolve => setTimeout(resolve, 500));
      
      const emailErrors = await pw.page.locator('.text-error, .validator, [class*="error"]').count();
      console.log('❌ Messages d\'erreur email:', emailErrors);
      
      // Test 5: Test du mot de passe
      console.log('🔍 Test 5: Test du mot de passe');
      await pw.click('input[name="password"]');
      await pw.type('input[name="password"]', '123');
      await pw.click('input[name="password_confirmation"]'); // Blur
      await new Promise(resolve => setTimeout(resolve, 500));
      
      const passwordErrors = await pw.page.locator('.text-error, .validator, [class*="error"]').count();
      console.log('❌ Messages d\'erreur mot de passe:', passwordErrors);
      
      // Capture finale
      await pw.screenshot('register-validation-test.png');
      
      console.log('✅ Tests de validation terminés');
    });
  },
  
  'console': async () => {
    // Extraire les options de la ligne de commande
    const options = args.slice(2);
    const config = {
      url: url,
      filter: 'error,warn,info',
      output: null,
      timeout: 30000,
      wait: 2000
    };
    
    // Parser les options
    for (const option of options) {
      if (option.startsWith('--filter=')) {
        config.filter = option.split('=')[1];
      } else if (option.startsWith('--output=')) {
        config.output = option.split('=')[1];
      } else if (option.startsWith('--timeout=')) {
        config.timeout = parseInt(option.split('=')[1]);
      } else if (option.startsWith('--wait=')) {
        config.wait = parseInt(option.split('=')[1]);
      }
    }
    
    await monitorConsole(config);
  },
  
  'network': async () => {
    // Extraire les options de la ligne de commande
    const options = args.slice(2);
    const config = {
      url: url,
      filter: 'GET,POST,PUT,DELETE,PATCH',
      status: '200,201,204,400,401,403,404,500',
      urlFilter: '',
      output: null,
      timeout: 30000,
      wait: 2000
    };
    
    // Parser les options
    for (const option of options) {
      if (option.startsWith('--filter=')) {
        config.filter = option.split('=')[1];
      } else if (option.startsWith('--status=')) {
        config.status = option.split('=')[1];
      } else if (option.startsWith('--url=')) {
        config.urlFilter = option.split('=')[1];
      } else if (option.startsWith('--output=')) {
        config.output = option.split('=')[1];
      } else if (option.startsWith('--timeout=')) {
        config.timeout = parseInt(option.split('=')[1]);
      } else if (option.startsWith('--wait=')) {
        config.wait = parseInt(option.split('=')[1]);
      }
    }
    
    await monitorNetwork(config);
  },
  
  'help': () => {
    console.log(`
🎯 Playwright CLI - Utilisation:

  node playwright-cli.js <commande> [url] [paramètres...]

📋 Commandes disponibles:

  navigate [url]                    - Navigation vers une URL
  login [url] [user-type]           - Connexion automatique avec différents types d'utilisateurs
  screenshot [url] [filename]       - Capture d'écran
  test-form [url]                   - Test de formulaire
  test-validation [url]             - Test du système de validation
  console [url] [options]           - Monitoring de la console
  network [url] [options]           - Monitoring des requêtes réseau
  help                              - Afficher cette aide

👤 Types d'utilisateurs pour login:
  super-admin                       - contact@jdr.iota21.fr / 0000
  test-user                         - test@example.com / password
  admin                             - admin@test.com / password
  game-master                       - gm@test.com / password
  player                            - player@test.com / password

📝 Exemples:

  node playwright-cli.js navigate http://localhost:8000
  node playwright-cli.js login http://localhost:8000 super-admin
  node playwright-cli.js login http://localhost:8000 test-user
  node playwright-cli.js screenshot http://localhost:8000 ma-capture.png
  node playwright-cli.js test-form http://localhost:8000
  node playwright-cli.js console http://localhost:8000 --filter=error,warn
  node playwright-cli.js console http://localhost:8000 --output=console.log --timeout=60000
  node playwright-cli.js network http://localhost:8000 --filter=GET,POST
  node playwright-cli.js network http://localhost:8000 --status=200,404 --output=network.log
    `);
  }
};

// Exécution de la tâche
if (command && tasks[command]) {
  if (command === 'help') {
    tasks[command]();
  } else if (command === 'console' || command === 'network') {
    tasks[command]();
  } else {
    runPlaywrightTask(command, tasks[command]);
  }
} else {
  console.log('❌ Commande non reconnue. Utilisez "help" pour voir les commandes disponibles.');
  console.log('💡 Exemple: node playwright-cli.js help');
} 