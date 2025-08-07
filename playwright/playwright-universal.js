import { webkit } from 'playwright';
import { readFileSync, writeFileSync } from 'fs';
import { join } from 'path';

class PlaywrightUniversal {
  constructor(options = {}) {
    this.options = {
      headless: false,
      screenshotPath: './screenshots',
      timeout: 30000,
      ...options
    };
    this.browser = null;
    this.page = null;
  }

  async init() {
    console.log('🚀 Initialisation de Playwright WebKit...');
    this.browser = await webkit.launch({ 
      headless: this.options.headless 
    });
    this.page = await this.browser.newPage();
    
    // Configuration de base
    await this.page.setDefaultTimeout(this.options.timeout);
    
    console.log('✅ Playwright initialisé avec succès');
    return this;
  }

  async navigate(url) {
    console.log(`🌐 Navigation vers: ${url}`);
    await this.page.goto(url);
    console.log(`📄 Titre de la page: ${await this.page.title()}`);
    return this;
  }

  async click(selector) {
    console.log(`🖱️ Clic sur: ${selector}`);
    await this.page.click(selector);
    return this;
  }

  async fill(selector, value) {
    console.log(`✏️ Remplissage de ${selector} avec: ${value}`);
    await this.page.fill(selector, value);
    return this;
  }

  async type(selector, value) {
    console.log(`⌨️ Saisie dans ${selector}: ${value}`);
    await this.page.type(selector, value);
    return this;
  }

  async waitForSelector(selector, options = {}) {
    console.log(`⏳ Attente de l'élément: ${selector}`);
    await this.page.waitForSelector(selector, options);
    return this;
  }

  async screenshot(filename = null) {
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
    const screenshotName = filename || `screenshot-${timestamp}.png`;
    const fullPath = join(this.options.screenshotPath, screenshotName);
    
    console.log(`📸 Capture d'écran: ${fullPath}`);
    await this.page.screenshot({ 
      path: fullPath, 
      fullPage: true 
    });
    return this;
  }

  async getText(selector) {
    const text = await this.page.textContent(selector);
    console.log(`📝 Texte de ${selector}: ${text}`);
    return text;
  }

  async getTitle() {
    const title = await this.page.title();
    console.log(`📄 Titre de la page: ${title}`);
    return title;
  }

  async getURL() {
    const url = this.page.url();
    console.log(`🔗 URL actuelle: ${url}`);
    return url;
  }

  async waitForTimeout(ms) {
    console.log(`⏰ Attente de ${ms}ms`);
    await this.page.waitForTimeout(ms);
    return this;
  }

  async evaluate(fn) {
    console.log(`🔍 Évaluation de script dans la page`);
    return await this.page.evaluate(fn);
  }

  async route(pattern, handler) {
    console.log(`🌐 Configuration de route pour: ${pattern}`);
    await this.page.route(pattern, handler);
  }

  async close() {
    if (this.browser) {
      console.log('🔒 Fermeture du navigateur');
      await this.browser.close();
    }
  }

  // Méthodes utilitaires pour des tâches communes
  async login(email, password, emailSelector = 'input[type="email"]', passwordSelector = 'input[type="password"]', submitSelector = 'button[type="submit"]') {
    console.log('🔐 Tentative de connexion...');
    await this.fill(emailSelector, email);
    await this.fill(passwordSelector, password);
    await this.click(submitSelector);
    return this;
  }

  async waitForNavigation() {
    console.log('⏳ Attente de la navigation...');
    await this.page.waitForLoadState('networkidle');
    return this;
  }
}

// Fonction principale pour exécuter des tâches
async function runPlaywrightTask(taskName, taskFunction) {
  const playwright = new PlaywrightUniversal();
  
  try {
    await playwright.init();
    await taskFunction(playwright);
    console.log(`✅ Tâche "${taskName}" terminée avec succès`);
  } catch (error) {
    console.error(`❌ Erreur lors de l'exécution de "${taskName}":`, error.message);
  } finally {
    await playwright.close();
  }
}

// Export pour utilisation dans d'autres scripts
export { PlaywrightUniversal, runPlaywrightTask };

// Exemple d'utilisation si le script est exécuté directement
if (import.meta.url === `file://${process.argv[1]}`) {
  // Tâche par défaut : navigation vers localhost:8000
  runPlaywrightTask('Navigation de test', async (pw) => {
    await pw.navigate('http://localhost:8000');
    await pw.screenshot('test-navigation.png');
  });
} 