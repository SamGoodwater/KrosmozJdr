#!/usr/bin/env node

/**
 * Network Monitor - Script universel pour capturer les requêtes réseau
 * 
 * Ce script permet de :
 * - Capturer toutes les requêtes HTTP (GET, POST, PUT, DELETE, etc.)
 * - Filtrer par type de requête, URL, statut
 * - Sauvegarder les résultats dans un fichier
 * - Afficher un résumé en temps réel
 * - Analyser les performances
 * 
 * Usage :
 * node playwright/tasks/network-monitor.js [url] [options]
 * 
 * Options :
 * --filter=GET,POST    Filtrer par type de requête
 * --status=200,404     Filtrer par statut HTTP
 * --url=api           Filtrer par URL contenant
 * --output=network.log Fichier de sortie
 * --timeout=30000     Timeout en ms
 * --wait=2000        Temps d'attente après chargement
 */

import { runPlaywrightTask } from '../playwright-universal.js';
import fs from 'fs';
import path from 'path';

// Configuration par défaut
const DEFAULT_CONFIG = {
  url: 'http://localhost:8000',
  filter: 'GET,POST,PUT,DELETE,PATCH', // Types de requêtes à capturer
  status: '200,201,204,400,401,403,404,500', // Statuts à capturer
  urlFilter: '', // Filtre d'URL (optionnel)
  output: null, // Fichier de sortie (optionnel)
  timeout: 30000, // Timeout en ms
  wait: 2000, // Temps d'attente après chargement
  verbose: false, // Mode verbeux
  performance: false // Mode performance
};

// Parse les arguments de ligne de commande
function parseArgs() {
  const args = process.argv.slice(2);
  const config = { ...DEFAULT_CONFIG };
  
  for (const arg of args) {
    if (arg.startsWith('--')) {
      const [key, value] = arg.slice(2).split('=');
      if (key === 'filter') {
        config.filter = value;
      } else if (key === 'status') {
        config.status = value;
      } else if (key === 'url') {
        config.urlFilter = value;
      } else if (key === 'output') {
        config.output = value;
      } else if (key === 'timeout') {
        config.timeout = parseInt(value);
      } else if (key === 'wait') {
        config.wait = parseInt(value);
      } else if (key === 'verbose') {
        config.verbose = true;
      } else if (key === 'performance') {
        config.performance = true;
      }
    } else if (!config.url || config.url === DEFAULT_CONFIG.url) {
      config.url = arg;
    }
  }
  
  return config;
}

// Filtre les requêtes selon la configuration
function filterRequests(requests, config) {
  const methods = config.filter.split(',').map(m => m.trim().toUpperCase());
  const statuses = config.status.split(',').map(s => parseInt(s.trim()));
  
  return requests.filter(req => {
    // Filtre par méthode
    if (!methods.includes(req.method)) return false;
    
    // Filtre par statut
    if (req.response && !statuses.includes(req.response.status)) return false;
    
    // Filtre par URL
    if (config.urlFilter && !req.url.includes(config.urlFilter)) return false;
    
    return true;
  });
}

// Formate une requête pour l'affichage
function formatRequest(req, config) {
  const timestamp = new Date(req.timestamp).toLocaleTimeString();
  const method = req.method.padEnd(6);
  const status = req.response ? req.response.status.toString().padStart(3) : '---';
  const size = req.response ? formatBytes(req.response.bodySize || 0) : '---';
  const duration = req.response ? `${req.response.timing.duration}ms` : '---';
  const url = req.url.length > 50 ? req.url.substring(0, 47) + '...' : req.url;
  
  let statusColor = '';
  if (req.response) {
    if (req.response.status >= 200 && req.response.status < 300) statusColor = '✅';
    else if (req.response.status >= 400) statusColor = '❌';
    else statusColor = '⚠️';
  }
  
  return `[${timestamp}] ${method} | ${statusColor}${status} | ${size.padStart(8)} | ${duration.padStart(8)} | ${url}`;
}

// Formate les bytes en format lisible
function formatBytes(bytes) {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Sauvegarde les requêtes dans un fichier
function saveToFile(requests, outputPath) {
  if (!outputPath) return;
  
  const outputDir = path.dirname(outputPath);
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
  }
  
  const content = requests.map(req => formatRequest(req, {})).join('\n');
  fs.writeFileSync(outputPath, content);
  console.log(`📄 Requêtes sauvegardées dans : ${outputPath}`);
}

// Affiche un résumé des requêtes
function displaySummary(requests, config) {
  const summary = {
    total: requests.length,
    byMethod: {},
    byStatus: {},
    bySize: { small: 0, medium: 0, large: 0 },
    performance: { fast: 0, medium: 0, slow: 0 }
  };
  
  requests.forEach(req => {
    // Par méthode
    summary.byMethod[req.method] = (summary.byMethod[req.method] || 0) + 1;
    
    // Par statut
    if (req.response) {
      summary.byStatus[req.response.status] = (summary.byStatus[req.response.status] || 0) + 1;
    }
    
    // Par taille
    if (req.response) {
      const size = req.response.bodySize || 0;
      if (size < 1024) summary.bySize.small++;
      else if (size < 1024 * 1024) summary.bySize.medium++;
      else summary.bySize.large++;
    }
    
    // Par performance
    if (req.response) {
      const duration = req.response.timing.duration;
      if (duration < 100) summary.performance.fast++;
      else if (duration < 1000) summary.performance.medium++;
      else summary.performance.slow++;
    }
  });
  
  console.log('\n📊 Résumé des requêtes réseau :');
  console.log(`📈 Total: ${summary.total} requêtes`);
  
  if (Object.keys(summary.byMethod).length > 0) {
    console.log('\n🔧 Par méthode :');
    Object.entries(summary.byMethod).forEach(([method, count]) => {
      console.log(`  ${method}: ${count}`);
    });
  }
  
  if (Object.keys(summary.byStatus).length > 0) {
    console.log('\n📋 Par statut :');
    Object.entries(summary.byStatus).forEach(([status, count]) => {
      const icon = status >= 200 && status < 300 ? '✅' : status >= 400 ? '❌' : '⚠️';
      console.log(`  ${icon} ${status}: ${count}`);
    });
  }
  
  if (summary.bySize.small > 0 || summary.bySize.medium > 0 || summary.bySize.large > 0) {
    console.log('\n📦 Par taille :');
    console.log(`  🔹 < 1KB: ${summary.bySize.small}`);
    console.log(`  🔸 1KB-1MB: ${summary.bySize.medium}`);
    console.log(`  🔺 > 1MB: ${summary.bySize.large}`);
  }
  
  if (summary.performance.fast > 0 || summary.performance.medium > 0 || summary.performance.slow > 0) {
    console.log('\n⚡ Par performance :');
    console.log(`  🟢 < 100ms: ${summary.performance.fast}`);
    console.log(`  🟡 100ms-1s: ${summary.performance.medium}`);
    console.log(`  🔴 > 1s: ${summary.performance.slow}`);
  }
  
  if (requests.length === 0) {
    console.log('✅ Aucune requête capturée');
  }
}

// Fonction principale
async function monitorNetwork(config) {
  console.log(`🌐 Monitoring réseau pour : ${config.url}`);
  console.log(`📋 Filtres : ${config.filter}`);
  console.log(`📊 Statuts : ${config.status}`);
  if (config.urlFilter) {
    console.log(`🔗 URL filter : ${config.urlFilter}`);
  }
  console.log(`⏱️  Timeout : ${config.timeout}ms`);
  console.log(`⏳ Attente : ${config.wait}ms`);
  
  if (config.output) {
    console.log(`💾 Sortie : ${config.output}`);
  }
  
  console.log('\n🚀 Démarrage du monitoring réseau...\n');
  
  const requests = [];
  
  try {
    await runPlaywrightTask('Network Monitor', async (pw) => {
      // Activer le monitoring réseau
      await pw.page.route('**/*', route => {
        // Capturer la requête
        const request = {
          url: route.request().url(),
          method: route.request().method(),
          headers: route.request().headers(),
          postData: route.request().postData(),
          timestamp: new Date().toISOString()
        };
        
        // Continuer la requête
        route.continue();
        
        // Attendre la réponse
        route.request().response().then(response => {
          if (response) {
            request.response = {
              status: response.status(),
              headers: response.headers(),
              bodySize: response.body().then(body => body.length).catch(() => 0),
              timing: {
                startTime: response.request().timing().requestStart,
                endTime: response.request().timing().responseEnd,
                duration: response.request().timing().responseEnd - response.request().timing().requestStart
              }
            };
          }
        }).catch(() => {
          // Requête échouée
        });
        
        requests.push(request);
      });
      
      // Naviguer vers l'URL
      await pw.navigate(config.url);
      
      // Attendre le chargement
      await new Promise(resolve => setTimeout(resolve, config.wait));
      
      console.log('✅ Monitoring réseau activé');
      
      // Attendre et capturer les requêtes
      const startTime = Date.now();
      let lastRequestCount = 0;
      
      while (Date.now() - startTime < config.timeout) {
        // Afficher les nouvelles requêtes
        if (requests.length > lastRequestCount) {
          const newRequests = requests.slice(lastRequestCount);
          const filteredRequests = filterRequests(newRequests, config);
          
          filteredRequests.forEach(req => {
            console.log(formatRequest(req, config));
          });
          
          lastRequestCount = requests.length;
        }
        
        // Attendre un peu avant la prochaine vérification
        await new Promise(resolve => setTimeout(resolve, 100));
      }
      
      console.log(`\n⏰ Timeout atteint (${config.timeout}ms)`);
    });
    
  } catch (error) {
    console.error('❌ Erreur lors du monitoring réseau :', error.message);
  }
  
  // Filtrer les requêtes selon la configuration
  const filteredRequests = filterRequests(requests, config);
  
  // Afficher le résumé
  displaySummary(filteredRequests, config);
  
  // Sauvegarder si demandé
  if (config.output) {
    saveToFile(filteredRequests, config.output);
  }
  
  return filteredRequests;
}

// Point d'entrée
if (import.meta.url === `file://${process.argv[1]}`) {
  const config = parseArgs();
  
  // Afficher l'aide si demandé
  if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(`
🌐 Network Monitor - Script universel pour capturer les requêtes réseau

Usage :
  node playwright/tasks/network-monitor.js [url] [options]

Arguments :
  url                    URL à monitorer (défaut: http://localhost:8000)

Options :
  --filter=methods       Types de requêtes à capturer (défaut: GET,POST,PUT,DELETE,PATCH)
                        Méthodes disponibles: GET, POST, PUT, DELETE, PATCH, HEAD, OPTIONS
  --status=codes         Statuts HTTP à capturer (défaut: 200,201,204,400,401,403,404,500)
  --url=pattern          Filtre d'URL (requêtes contenant ce pattern)
  --output=file          Fichier de sortie pour sauvegarder les requêtes
  --timeout=ms           Timeout en millisecondes (défaut: 30000)
  --wait=ms             Temps d'attente après chargement (défaut: 2000)
  --verbose              Mode verbeux
  --performance          Mode performance (analyse des temps de réponse)
  --help, -h            Afficher cette aide

Exemples :
  # Monitorer localhost avec filtres par défaut
  node playwright/tasks/network-monitor.js

  # Monitorer une URL spécifique
  node playwright/tasks/network-monitor.js https://example.com

  # Capturer seulement les requêtes GET et POST
  node playwright/tasks/network-monitor.js --filter=GET,POST

  # Capturer seulement les erreurs
  node playwright/tasks/network-monitor.js --status=400,401,403,404,500

  # Filtrer par URL
  node playwright/tasks/network-monitor.js --url=api

  # Sauvegarder dans un fichier
  node playwright/tasks/network-monitor.js --output=network.log

  # Timeout plus long
  node playwright/tasks/network-monitor.js --timeout=60000

  # Combinaison d'options
  node playwright/tasks/network-monitor.js https://example.com --filter=GET,POST --status=200,404 --url=api --output=requests.log --timeout=45000
`);
    process.exit(0);
  }
  
  monitorNetwork(config).catch(console.error);
}

// Export pour utilisation dans d'autres scripts
export { monitorNetwork, parseArgs, filterRequests, formatRequest, displaySummary };
