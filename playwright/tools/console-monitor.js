#!/usr/bin/env node

/**
 * Console Monitor - Script universel pour capturer les messages de console
 * 
 * Ce script permet de :
 * - Capturer tous les messages de console (log, warn, error, info)
 * - Filtrer par type de message
 * - Sauvegarder les résultats dans un fichier
 * - Afficher un résumé en temps réel
 * 
 * Usage :
 * node playwright/tasks/console-monitor.js [url] [options]
 * 
 * Options :
 * --filter=error,warn    Filtrer par type de message
 * --output=console.log   Fichier de sortie
 * --timeout=30000        Timeout en ms
 * --wait=2000           Temps d'attente après chargement
 */

import { runPlaywrightTask } from '../playwright-universal.js';
import fs from 'fs';
import path from 'path';

// Configuration par défaut
const DEFAULT_CONFIG = {
  url: 'http://localhost:8000',
  filter: 'error,warn,info', // Types de messages à capturer
  output: null, // Fichier de sortie (optionnel)
  timeout: 30000, // Timeout en ms
  wait: 2000, // Temps d'attente après chargement
  verbose: false // Mode verbeux
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
      } else if (key === 'output') {
        config.output = value;
      } else if (key === 'timeout') {
        config.timeout = parseInt(value);
      } else if (key === 'wait') {
        config.wait = parseInt(value);
      } else if (key === 'verbose') {
        config.verbose = true;
      }
    } else if (!config.url || config.url === DEFAULT_CONFIG.url) {
      config.url = arg;
    }
  }
  
  return config;
}

// Filtre les messages selon la configuration
function filterMessages(messages, filterTypes) {
  const types = filterTypes.split(',').map(t => t.trim());
  return messages.filter(msg => types.includes(msg.type));
}

// Formate un message pour l'affichage
function formatMessage(msg) {
  const timestamp = new Date(msg.timestamp).toLocaleTimeString();
  const type = msg.type.toUpperCase().padEnd(5);
  const source = msg.location ? `${msg.location.url}:${msg.location.lineNumber}` : 'unknown';
  
  return `[${timestamp}] ${type} | ${source} | ${msg.text}`;
}

// Sauvegarde les messages dans un fichier
function saveToFile(messages, outputPath) {
  if (!outputPath) return;
  
  const outputDir = path.dirname(outputPath);
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
  }
  
  const content = messages.map(msg => formatMessage(msg)).join('\n');
  fs.writeFileSync(outputPath, content);
  console.log(`📄 Messages sauvegardés dans : ${outputPath}`);
}

// Affiche un résumé des messages
function displaySummary(messages) {
  const summary = messages.reduce((acc, msg) => {
    acc[msg.type] = (acc[msg.type] || 0) + 1;
    return acc;
  }, {});
  
  console.log('\n📊 Résumé des messages de console :');
  Object.entries(summary).forEach(([type, count]) => {
    const icon = type === 'error' ? '❌' : type === 'warn' ? '⚠️' : type === 'info' ? 'ℹ️' : '📝';
    console.log(`${icon} ${type.toUpperCase()}: ${count}`);
  });
  
  if (messages.length === 0) {
    console.log('✅ Aucun message capturé');
  }
}

// Fonction principale
async function monitorConsole(config) {
  console.log(`🔍 Monitoring console pour : ${config.url}`);
  console.log(`📋 Filtres : ${config.filter}`);
  console.log(`⏱️  Timeout : ${config.timeout}ms`);
  console.log(`⏳ Attente : ${config.wait}ms`);
  
  if (config.output) {
    console.log(`💾 Sortie : ${config.output}`);
  }
  
  console.log('\n🚀 Démarrage du monitoring...\n');
  
  const messages = [];
  
  try {
    await runPlaywrightTask('Console Monitor', async (pw) => {
      // Naviguer vers l'URL
      await pw.navigate(config.url);
      
      // Attendre le chargement
      await new Promise(resolve => setTimeout(resolve, config.wait));
      
      // Évaluer le script pour capturer les messages
      const consoleMessages = await pw.evaluate(() => {
        // Stocker les messages capturés
        window.__consoleMessages = [];
        
        // Fonction pour capturer les messages
        function captureMessage(type, args) {
          const message = {
            type: type,
            text: args.map(arg => {
              if (typeof arg === 'object') {
                try {
                  return JSON.stringify(arg);
                } catch {
                  return arg.toString();
                }
              }
              return String(arg);
            }).join(' '),
            timestamp: new Date().toISOString(),
            location: null
          };
          
          // Essayer de capturer la stack trace
          try {
            const stack = new Error().stack;
            const lines = stack.split('\n');
            // Chercher la ligne qui n'est pas dans notre script
            for (const line of lines) {
              if (line.includes('http') && !line.includes('playwright')) {
                const match = line.match(/(https?:\/\/[^:]+):(\d+):(\d+)/);
                if (match) {
                  message.location = {
                    url: match[1],
                    lineNumber: parseInt(match[2]),
                    columnNumber: parseInt(match[3])
                  };
                  break;
                }
              }
            }
          } catch (e) {
            // Ignorer les erreurs de stack trace
          }
          
          window.__consoleMessages.push(message);
        }
        
        // Intercepter les méthodes de console
        const originalLog = console.log;
        const originalWarn = console.warn;
        const originalError = console.error;
        const originalInfo = console.info;
        const originalDebug = console.debug;
        
        console.log = (...args) => {
          captureMessage('log', args);
          originalLog.apply(console, args);
        };
        
        console.warn = (...args) => {
          captureMessage('warn', args);
          originalWarn.apply(console, args);
        };
        
        console.error = (...args) => {
          captureMessage('error', args);
          originalError.apply(console, args);
        };
        
        console.info = (...args) => {
          captureMessage('info', args);
          originalInfo.apply(console, args);
        };
        
        console.debug = (...args) => {
          captureMessage('debug', args);
          originalDebug.apply(console, args);
        };
        
        // Capturer les erreurs non gérées
        window.addEventListener('error', (event) => {
          captureMessage('error', [`Uncaught Error: ${event.message}`, event.filename, event.lineno]);
        });
        
        window.addEventListener('unhandledrejection', (event) => {
          captureMessage('error', [`Unhandled Promise Rejection: ${event.reason}`]);
        });
        
        return 'Console monitoring activé';
      });
      
      console.log('✅ Console monitoring activé');
      
      // Attendre et capturer les messages
      const startTime = Date.now();
      let lastMessageCount = 0;
      
      while (Date.now() - startTime < config.timeout) {
        // Récupérer les nouveaux messages
        const newMessages = await pw.evaluate(() => {
          const messages = window.__consoleMessages || [];
          window.__consoleMessages = []; // Vider le buffer
          return messages;
        });
        
        // Ajouter les nouveaux messages
        messages.push(...newMessages);
        
        // Afficher les nouveaux messages en temps réel
        if (newMessages.length > 0) {
          const filteredMessages = filterMessages(newMessages, config.filter);
          filteredMessages.forEach(msg => {
            console.log(formatMessage(msg));
          });
        }
        
        // Vérifier s'il y a eu de l'activité
        if (newMessages.length > 0) {
          lastMessageCount = newMessages.length;
        }
        
        // Attendre un peu avant la prochaine vérification
        await new Promise(resolve => setTimeout(resolve, 100));
      }
      
      console.log(`\n⏰ Timeout atteint (${config.timeout}ms)`);
    });
    
  } catch (error) {
    console.error('❌ Erreur lors du monitoring :', error.message);
  }
  
  // Filtrer les messages selon la configuration
  const filteredMessages = filterMessages(messages, config.filter);
  
  // Afficher le résumé
  displaySummary(filteredMessages);
  
  // Sauvegarder si demandé
  if (config.output) {
    saveToFile(filteredMessages, config.output);
  }
  
  return filteredMessages;
}

// Point d'entrée
if (import.meta.url === `file://${process.argv[1]}`) {
  const config = parseArgs();
  
  // Afficher l'aide si demandé
  if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(`
🔍 Console Monitor - Script universel pour capturer les messages de console

Usage :
  node playwright/tasks/console-monitor.js [url] [options]

Arguments :
  url                    URL à monitorer (défaut: http://localhost:8000)

Options :
  --filter=types         Types de messages à capturer (défaut: error,warn,info)
                        Types disponibles: log, warn, error, info, debug
  --output=file          Fichier de sortie pour sauvegarder les messages
  --timeout=ms           Timeout en millisecondes (défaut: 30000)
  --wait=ms             Temps d'attente après chargement (défaut: 2000)
  --verbose              Mode verbeux
  --help, -h            Afficher cette aide

Exemples :
  # Monitorer localhost avec filtres par défaut
  node playwright/tasks/console-monitor.js

  # Monitorer une URL spécifique
  node playwright/tasks/console-monitor.js https://example.com

  # Capturer seulement les erreurs et warnings
  node playwright/tasks/console-monitor.js --filter=error,warn

  # Sauvegarder dans un fichier
  node playwright/tasks/console-monitor.js --output=console.log

  # Timeout plus long
  node playwright/tasks/console-monitor.js --timeout=60000

  # Combinaison d'options
  node playwright/tasks/console-monitor.js https://example.com --filter=error --output=errors.log --timeout=45000
`);
    process.exit(0);
  }
  
  monitorConsole(config).catch(console.error);
}

// Export pour utilisation dans d'autres scripts
export { monitorConsole, parseArgs, filterMessages, formatMessage, displaySummary };
