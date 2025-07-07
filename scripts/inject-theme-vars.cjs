const fs = require('fs');
const path = require('path');

const themeCssPath = path.join(__dirname, '../resources/css/theme.css');
const customCssPath = path.join(__dirname, '../resources/css/custom.css');
const appCssPath = path.join(__dirname, '../resources/css/app.css');
const appSaveCssPath = path.join(__dirname, '../resources/scss/themes/_app.save.css');

const APP_THEME_VARS_START = 'INJECTION_THEME_VARS_START';
const APP_THEME_VARS_END = 'INJECTION_THEME_VARS_END';

const THEME_VARS_START = '/*! THEME_VARS_START */';
const THEME_VARS_END = '/*! THEME_VARS_END */';

// Configuration des options
const options = {
    watch: process.argv.includes('--watch'),
    wait: process.argv.includes('--wait'),
    verbose: process.argv.includes('--verbose'),
    help: process.argv.includes('--help') || process.argv.includes('-h')
};

// Afficher l'aide si demandé
if (options.help) {
    console.log(`
🎨 Script d'injection des variables de thème

Usage: node scripts/inject-theme-vars.cjs [options]

Options:
  --watch     Mode surveillance continue (par défaut: injection unique)
  --wait      Attendre que les fichiers CSS soient générés (par défaut: true)
  --verbose   Afficher plus de détails
  --help, -h  Afficher cette aide

Exemples:
  node scripts/inject-theme-vars.cjs                    # Injection unique avec attente
  node scripts/inject-theme-vars.cjs --watch            # Mode surveillance
  node scripts/inject-theme-vars.cjs --watch --verbose  # Surveillance avec détails
  node scripts/inject-theme-vars.cjs --no-wait          # Injection immédiate (erreur si fichiers manquants)
`);
    process.exit(0);
}

// Variables de contrôle
let isWatching = false;
let lastInjectionTime = 0;
let isInjecting = false;

function log(message, type = 'info') {
    const prefix = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌',
        waiting: '⏳'
    }[type] || 'ℹ️';
    
    if (options.verbose || type !== 'info') {
        console.log(`${prefix} ${message}`);
    }
}

function waitForFiles(maxAttempts = 30, interval = 1000) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        
        const checkFiles = () => {
            attempts++;
            log(`Tentative ${attempts}/${maxAttempts} : Vérification des fichiers CSS...`, 'waiting');
            
            if (fs.existsSync(themeCssPath) && fs.existsSync(customCssPath)) {
                log('Fichiers CSS détectés, injection des variables...', 'success');
                resolve();
            } else if (attempts >= maxAttempts) {
                reject(new Error('Timeout : Les fichiers CSS ne sont pas apparus dans le délai imparti'));
            } else {
                setTimeout(checkFiles, interval);
            }
        };
        
        checkFiles();
    });
}

function injectThemeVars() {
    // Protection contre les injections simultanées
    if (isInjecting) {
        return false;
    }
    
    // Protection contre les injections trop fréquentes en mode watch (min 2 secondes)
    if (options.watch) {
        const now = Date.now();
        if (now - lastInjectionTime < 2000) {
            return false;
        }
    }
    
    isInjecting = true;
    
    try {
        // Vérifier que les fichiers nécessaires existent
        if (!fs.existsSync(themeCssPath)) {
            if (options.watch) {
                log('En attente de theme.css...', 'waiting');
                return false;
            } else {
                throw new Error('theme.css introuvable');
            }
        }

        if (!fs.existsSync(customCssPath)) {
            if (options.watch) {
                log('En attente de custom.css...', 'waiting');
                return false;
            } else {
                throw new Error('custom.css introuvable');
            }
        }

        // Si app.css n'existe pas, le créer à partir de app.save.css
        if (!fs.existsSync(appCssPath)) {
            if (fs.existsSync(appSaveCssPath)) {
                fs.copyFileSync(appSaveCssPath, appCssPath);
                log('Fichier app.css créé à partir de app.save.css', 'info');
            } else {
                throw new Error('Ni app.css ni app.save.css trouvés');
            }
        }

        const themeCss = fs.readFileSync(themeCssPath, 'utf8');
        let appCss = fs.readFileSync(appCssPath, 'utf8');

        function extractVars(themeCss, start, end) {
            const block = themeCss.split(start)[1]?.split(end)[0];
            if (!block) return '';
            
            // Décompresser le contenu CSS (remplacer les ; par des sauts de ligne)
            const decompressed = block.replace(/;/g, ';\n');
            
            const match = decompressed.match(/:root\s*{([\s\S]*?)}/);
            return match ? match[1].trim() : decompressed.trim();
        }

        const themeVars = extractVars(themeCss, THEME_VARS_START, THEME_VARS_END);

        function replaceBetweenMarkers(css, startMarker, endMarker, content) {
            const regex = new RegExp(
                `(\/\\*\\s*${startMarker}\\s*\\*\/)([\\s\\S]*?)(\/\\*\\s*${endMarker}\\s*\\*\/)`,
                'm'
            );
            
            // Formater le contenu avec des sauts de ligne pour la lisibilité
            const formattedContent = content
                .split(';')
                .filter(rule => rule.trim())
                .map(rule => `  ${rule.trim()};`)
                .join('\n');
            
            return css.replace(regex, `$1\n${formattedContent}\n$3`);
        }

        const newAppCss = replaceBetweenMarkers(appCss, APP_THEME_VARS_START, APP_THEME_VARS_END, themeVars);
        
        // Ne modifier que si le contenu a réellement changé
        if (newAppCss !== appCss) {
            fs.writeFileSync(appCssPath, newAppCss, 'utf8');
            lastInjectionTime = Date.now();
            log('Variables de thème injectées dans app.css', 'success');
            return true;
        }
        
        return false;
        
    } catch (error) {
        log(`Erreur lors de l'injection : ${error.message}`, 'error');
        return false;
    } finally {
        isInjecting = false;
    }
}

function startWatching() {
    if (isWatching) return;
    isWatching = true;
    
    log('Mode surveillance activé - Surveillance des fichiers CSS...', 'info');
    
    // Première injection
    if (injectThemeVars()) {
        log('Injection initiale réussie !', 'success');
    }
    
    // Surveiller les changements avec debounce
    let watchTimeout;
    const cssDir = path.dirname(themeCssPath);
    
    fs.watch(cssDir, (eventType, filename) => {
        if (filename && (filename.endsWith('.css') || filename.endsWith('.css.map'))) {
            // Ignorer les changements sur app.css pour éviter la boucle
            if (filename === 'app.css') {
                return;
            }
            
            // Debounce : attendre 500ms avant de traiter
            clearTimeout(watchTimeout);
            watchTimeout = setTimeout(() => {
                if (injectThemeVars()) {
                    log('Variables de thème mises à jour', 'success');
                }
            }, 500);
        }
    });
    
    log('Surveillance active - Les variables seront injectées automatiquement', 'success');
}

async function runInjection() {
    try {
        if (options.watch) {
            // Mode surveillance
            startWatching();
            
            // Gestion de l'arrêt propre
            process.on('SIGINT', () => {
                log('Arrêt de la surveillance des variables de thème', 'info');
                process.exit(0);
            });
            
        } else {
            // Mode injection unique
            log('Mode injection unique - Attente des fichiers CSS...', 'info');
            
            // Attendre que les fichiers CSS soient générés si l'option wait est activée
            if (options.wait !== false) {
                await waitForFiles();
            }
            
            // Injecter une seule fois
            if (injectThemeVars()) {
                log('Injection réussie ! Script terminé - Libération des ressources', 'success');
            } else {
                log('Aucune modification nécessaire', 'info');
            }
        }
        
    } catch (error) {
        log(`Erreur fatale : ${error.message}`, 'error');
        process.exit(1);
    }
}

// Démarrer l'injection
runInjection(); 