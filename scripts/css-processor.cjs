const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Chemins des fichiers
const customCssPath = path.join(__dirname, '../resources/css/custom.css');
const themeCssPath = path.join(__dirname, '../resources/css/theme.css');
const appCssPath = path.join(__dirname, '../resources/css/app.css');

// Configuration des options
const options = {
    minify: process.argv.includes('--minify'),
    inject: process.argv.includes('--inject'),
    watch: process.argv.includes('--watch'),
    verbose: process.argv.includes('--verbose'),
    help: process.argv.includes('--help') || process.argv.includes('-h')
};

// Afficher l'aide si demandé
if (options.help) {
    console.log(`
🎨 Processeur CSS - Coordination de la minification et injection

Usage: node scripts/css-processor.cjs [options]

Options:
  --minify    Minifier les fichiers CSS avec csso
  --inject    Injecter les variables de thème
  --watch     Mode surveillance continue
  --verbose   Afficher plus de détails
  --help, -h  Afficher cette aide

Exemples:
  node scripts/css-processor.cjs --minify --inject                    # Minification + injection unique
  node scripts/css-processor.cjs --minify --inject --watch            # Mode surveillance
  node scripts/css-processor.cjs --minify --verbose                   # Minification seule avec détails
`);
    process.exit(0);
}

// Variables de contrôle
let isProcessing = false;
let lastProcessTime = 0;

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

function waitForSassFiles(maxAttempts = 60, interval = 1000) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        
        const checkFiles = () => {
            attempts++;
            log(`Tentative ${attempts}/${maxAttempts} : Vérification des fichiers Sass...`, 'waiting');
            
            // Vérifier que les fichiers CSS générés par Sass existent
            if (fs.existsSync(customCssPath) && fs.existsSync(themeCssPath)) {
                // Attendre un peu pour s'assurer que les fichiers sont complètement écrits
                setTimeout(() => {
                    log('Fichiers Sass détectés, traitement en cours...', 'success');
                    resolve();
                }, 500);
            } else if (attempts >= maxAttempts) {
                reject(new Error('Timeout : Les fichiers Sass ne sont pas apparus dans le délai imparti'));
            } else {
                setTimeout(checkFiles, interval);
            }
        };
        
        checkFiles();
    });
}

function minifyCss() {
    if (!options.minify) return false;
    
    try {
        log('Début de la minification CSS...', 'info');
        
        // Minifier custom.css et remplacer l'original
        if (fs.existsSync(customCssPath)) {
            const customCssMinPath = customCssPath.replace('.css', '.min.css');
            execSync(`npx csso ${customCssPath} -o ${customCssMinPath}`, { stdio: 'pipe' });
            
            // Vérifier que le fichier minifié a été créé
            if (fs.existsSync(customCssMinPath)) {
                // Supprimer l'original et renommer le minifié
                fs.unlinkSync(customCssPath);
                fs.renameSync(customCssMinPath, customCssPath);
                log('custom.css minifié et remplacé avec succès', 'success');
            } else {
                log('Erreur : Le fichier minifié n\'a pas été créé', 'error');
                return false;
            }
        }
        
        // Minifier app.css et remplacer l'original (seulement s'il existe)
        if (fs.existsSync(appCssPath)) {
            const appCssMinPath = appCssPath.replace('.css', '.min.css');
            execSync(`npx csso ${appCssPath} -o ${appCssMinPath}`, { stdio: 'pipe' });
            
            // Vérifier que le fichier minifié a été créé
            if (fs.existsSync(appCssMinPath)) {
                // Supprimer l'original et renommer le minifié
                fs.unlinkSync(appCssPath);
                fs.renameSync(appCssMinPath, appCssPath);
                log('app.css minifié et remplacé avec succès', 'success');
            } else {
                log('Erreur : Le fichier app.css minifié n\'a pas été créé', 'error');
                return false;
            }
        }
        
        return true;
    } catch (error) {
        log(`Erreur lors de la minification : ${error.message}`, 'error');
        return false;
    }
}

function injectThemeVars() {
    if (!options.inject) return false;
    
    try {
        log('Début de l\'injection des variables de thème...', 'info');
        
        // Exécuter le script d'injection
        execSync('node scripts/inject-theme-vars.cjs', { stdio: 'pipe' });
        log('Variables de thème injectées avec succès', 'success');
        
        return true;
    } catch (error) {
        log(`Erreur lors de l'injection : ${error.message}`, 'error');
        return false;
    }
}

function processCss() {
    // Protection contre les traitements simultanés
    if (isProcessing) {
        return false;
    }
    
    // Protection contre les traitements trop fréquents en mode watch (min 3 secondes)
    if (options.watch) {
        const now = Date.now();
        if (now - lastProcessTime < 3000) {
            return false;
        }
    }
    
    isProcessing = true;
    
    try {
        let success = true;
        
        // Minification
        if (options.minify) {
            success = minifyCss() && success;
        }
        
        // Injection
        if (options.inject) {
            success = injectThemeVars() && success;
        }
        
        if (success) {
            lastProcessTime = Date.now();
            log('Traitement CSS terminé avec succès', 'success');
        }
        
        return success;
        
    } catch (error) {
        log(`Erreur lors du traitement CSS : ${error.message}`, 'error');
        return false;
    } finally {
        isProcessing = false;
    }
}

function startWatching() {
    if (!options.watch) return;
    
    log('Mode surveillance activé - Surveillance des fichiers CSS...', 'info');
    
    // Première exécution
    if (processCss()) {
        log('Traitement initial réussi !', 'success');
    }
    
    // Surveiller les changements avec debounce
    let watchTimeout;
    const cssDir = path.dirname(customCssPath);
    
    fs.watch(cssDir, (eventType, filename) => {
        if (filename && (filename.endsWith('.css') || filename.endsWith('.css.map'))) {
            // Ignorer les fichiers minifiés pour éviter la boucle
            if (filename.includes('.min.css')) {
                return;
            }
            
            // Debounce : attendre 1 seconde avant de traiter
            clearTimeout(watchTimeout);
            watchTimeout = setTimeout(() => {
                if (processCss()) {
                    log('Traitement CSS mis à jour', 'success');
                }
            }, 1000);
        }
    });
    
    log('Surveillance active - Le traitement sera exécuté automatiquement', 'success');
}

async function runProcessor() {
    try {
        if (options.watch) {
            // Mode surveillance
            startWatching();
            
            // Gestion de l'arrêt propre
            process.on('SIGINT', () => {
                log('Arrêt du processeur CSS', 'info');
                process.exit(0);
            });
            
        } else {
            // Mode traitement unique
            log('Mode traitement unique - Attente des fichiers Sass...', 'info');
            
            // Attendre que Sass ait terminé
            await waitForSassFiles();
            
            // Traiter une seule fois
            if (processCss()) {
                log('Traitement réussi ! Script terminé', 'success');
            } else {
                log('Aucun traitement effectué', 'info');
            }
        }
        
    } catch (error) {
        log(`Erreur fatale : ${error.message}`, 'error');
        process.exit(1);
    }
}

// Démarrer le processeur
runProcessor(); 