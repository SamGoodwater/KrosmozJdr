import { runPlaywrightTask } from './playwright-universal.js';

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
    const email = args[2] || 'test@example.com';
    const password = args[3] || 'password123';
    
    await pw.navigate(`${url}/login`);
    await pw.screenshot('login-page.png');
    await pw.login(email, password);
    await pw.waitForNavigation();
    await pw.screenshot('after-login.png');
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
  
  'help': () => {
    console.log(`
🎯 Playwright CLI - Utilisation:

  node playwright-cli.js <commande> [url] [paramètres...]

📋 Commandes disponibles:

  navigate [url]                    - Navigation vers une URL
  login [url] [email] [password]    - Test de connexion
  screenshot [url] [filename]       - Capture d'écran
  test-form [url]                   - Test de formulaire
  help                              - Afficher cette aide

📝 Exemples:

  node playwright-cli.js navigate http://localhost:8000
  node playwright-cli.js login http://localhost:8000 user@test.com pass123
  node playwright-cli.js screenshot http://localhost:8000 ma-capture.png
  node playwright-cli.js test-form http://localhost:8000
    `);
  }
};

// Exécution de la tâche
if (command && tasks[command]) {
  if (command === 'help') {
    tasks[command]();
  } else {
    runPlaywrightTask(command, tasks[command]);
  }
} else {
  console.log('❌ Commande non reconnue. Utilisez "help" pour voir les commandes disponibles.');
  console.log('💡 Exemple: node playwright-cli.js help');
} 