#!/usr/bin/env node

/**
 * Script de raccourci pour les outils Playwright
 * Usage: node playwright/run.js <commande> [paramètres...]
 */

import { spawn } from 'child_process';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Récupération des arguments
const args = process.argv.slice(2);

if (args.length === 0) {
  console.log(`
🎭 Outils Playwright - Krosmoz-JDR

Usage: node playwright/run.js <commande> [paramètres...]

📋 Commandes disponibles:
  help                    - Afficher l'aide complète
  navigate [url]          - Navigation vers une URL
  login [url] [email] [password] - Test de connexion
  screenshot [url] [filename] - Capture d'écran
  test-form [url]         - Test de formulaire
  nav                     - Navigation rapide vers localhost:8000
  ss                      - Capture d'écran rapide de localhost:8000

📝 Exemples:
  node playwright/run.js nav
  node playwright/run.js ss ma-capture.png
  node playwright/run.js navigate http://localhost:8000
  node playwright/run.js help
  `);
  process.exit(0);
}

// Commandes de raccourci
const shortcuts = {
  'nav': ['navigate', 'http://localhost:8000'],
  'ss': ['screenshot', 'http://localhost:8000']
};

const command = args[0];
let cliArgs = args;

// Appliquer les raccourcis
if (shortcuts[command]) {
  cliArgs = [...shortcuts[command], ...args.slice(1)];
}

// Exécuter le CLI Playwright
const cliPath = join(__dirname, 'playwright-cli.js');
const child = spawn('node', [cliPath, ...cliArgs], {
  stdio: 'inherit',
  cwd: __dirname
});

child.on('close', (code) => {
  process.exit(code);
});

child.on('error', (error) => {
  console.error('❌ Erreur lors de l\'exécution:', error.message);
  process.exit(1);
}); 