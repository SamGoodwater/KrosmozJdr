const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// Configuration des options
const options = {
    clean: process.argv.includes('--clean'),
    minify: process.argv.includes('--minify'),
    inject: process.argv.includes('--inject'),
    watch: process.argv.includes('--watch'),
    verbose: process.argv.includes('--verbose'),
    help: process.argv.includes('--help') || process.argv.includes('-h')
};

// Afficher l'aide si demandé
if (options.help) {
    console.log(`
🎨 Build CSS complet

Usage: node scripts/build-css.cjs [options]

Options:
  --clean     Nettoyer les fichiers CSS avant le build
  --minify    Minifier les fichiers CSS après compilation
  --inject    Injecter les variables de thème
  --watch     Mode surveillance continue
  --verbose   Afficher plus de détails
  --help, -h  Afficher cette aide

Exemples:
  node scripts/build-css.cjs --clean --minify --inject                    # Build complet
  node scripts/build-css.cjs --clean --minify --inject --watch            # Build avec surveillance
  node scripts/build-css.cjs --minify --inject                            # Build sans nettoyage
  node scripts/build-css.cjs --clean --verbose                            # Nettoyage avec détails
`);
    process.exit(0);
}

function log(message, type = 'info') {
    const prefix = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌'
    }[type] || 'ℹ️';
    
    if (options.verbose || type !== 'info') {
        console.log(`${prefix} ${message}`);
    }
}

function runCommand(command, description) {
    try {
        log(`Exécution : ${description}`, 'info');
        execSync(command, { stdio: 'pipe' });
        log(`${description} terminé avec succès`, 'success');
        return true;
    } catch (error) {
        log(`Erreur lors de ${description} : ${error.message}`, 'error');
        return false;
    }
}

async function buildCss() {
    let success = true;
    
    log('Début du build CSS...', 'info');
    
    // Étape 1 : Correction des permissions Sass (optionnel)
    if (options.clean) {
        success = runCommand('node scripts/fix-sass-permissions.cjs', 'Correction des permissions Sass') && success;
        success = runCommand('node scripts/clean-css.cjs', 'Nettoyage des fichiers CSS') && success;
    }
    
    // Étape 2 : Compilation Sass
    success = runCommand('pnpm run sass:build', 'Compilation Sass') && success;
    
    // Étape 2.5 : Attendre un peu pour éviter les conflits de fichiers
    if (success) {
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    // Étape 3 : Injection des variables de thème (optionnel)
    if (options.inject && success) {
        success = runCommand('node scripts/inject-theme-vars.cjs', 'Injection des variables de thème') && success;
    }
    
    // Étape 4 : Minification (optionnel)
    if (options.minify && success) {
        // Attendre un peu pour éviter les conflits de fichiers
        await new Promise(resolve => setTimeout(resolve, 1000));
        success = runCommand('node scripts/css-processor.cjs --minify', 'Minification CSS') && success;
    }
    
    if (success) {
        log('Build CSS terminé avec succès !', 'success');
    } else {
        log('Build CSS terminé avec des erreurs', 'error');
        process.exit(1);
    }
}

async function startWatching() {
    log('Mode surveillance activé - Surveillance des fichiers Sass...', 'info');
    
    // Première exécution
    await buildCss();
    
    // Surveiller les changements
    const sassDir = path.join(__dirname, '../resources/scss');
    let isProcessing = false;
    
    fs.watch(sassDir, { recursive: true }, async (eventType, filename) => {
        if (filename && filename.endsWith('.scss') && !isProcessing) {
            isProcessing = true;
            
            // Debounce : attendre 1 seconde avant de traiter
            setTimeout(async () => {
                log(`Fichier modifié : ${filename}`, 'info');
                await buildCss();
                isProcessing = false;
            }, 1000);
        }
    });
    
    log('Surveillance active - Le build sera exécuté automatiquement', 'success');
    
    // Gestion de l'arrêt propre
    process.on('SIGINT', () => {
        log('Arrêt du build CSS', 'info');
        process.exit(0);
    });
}

// Démarrer le build
if (options.watch) {
    startWatching();
} else {
    buildCss();
} 