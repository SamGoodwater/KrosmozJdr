const fs = require('fs');
const path = require('path');

// Chemins des fichiers à nettoyer
// Ne pas supprimer app.css / custom.css : sources versionnées requises par Vite (entry points).
const filesToClean = [
    path.join(__dirname, '../resources/css/custom.css.map'),
    path.join(__dirname, '../resources/css/theme.css.map'),
    path.join(__dirname, '../resources/css/app.css.map'),
];

// Configuration des options
const options = {
    verbose: process.argv.includes('--verbose'),
    help: process.argv.includes('--help') || process.argv.includes('-h')
};

// Afficher l'aide si demandé
if (options.help) {
    console.log(`
🧹 Nettoyage des fichiers CSS

Usage: node scripts/clean-css.cjs [options]

Options:
  --verbose   Afficher plus de détails
  --help, -h  Afficher cette aide

Ce script supprime les fichiers *.css.map (pas app.css ni custom.css).
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

function cleanCssFiles() {
    let deletedCount = 0;
    let errorCount = 0;
    
    log('Début du nettoyage des fichiers CSS...', 'info');
    
    filesToClean.forEach(filePath => {
        if (fs.existsSync(filePath)) {
            try {
                fs.unlinkSync(filePath);
                log(`Supprimé : ${path.basename(filePath)}`, 'success');
                deletedCount++;
            } catch (error) {
                log(`Erreur lors de la suppression de ${path.basename(filePath)} : ${error.message}`, 'error');
                errorCount++;
            }
        } else {
            if (options.verbose) {
                log(`Fichier inexistant : ${path.basename(filePath)}`, 'info');
            }
        }
    });
    
    if (deletedCount > 0) {
        log(`${deletedCount} fichier(s) supprimé(s) avec succès`, 'success');
    } else {
        log('Aucun fichier à supprimer', 'info');
    }
    
    if (errorCount > 0) {
        log(`${errorCount} erreur(s) lors du nettoyage`, 'error');
        return false;
    }
    
    return true;
}

// Exécuter le nettoyage
if (cleanCssFiles()) {
    log('Nettoyage terminé avec succès', 'success');
} else {
    log('Nettoyage terminé avec des erreurs', 'error');
    process.exit(1);
} 