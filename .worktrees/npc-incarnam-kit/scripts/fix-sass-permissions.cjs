const fs = require('fs');
const path = require('path');

// Chemins des fichiers problématiques
const problematicFiles = [
    path.join(__dirname, '../public/css/custom.css.map'),
    path.join(__dirname, '../public/css/theme.css.map'),
    path.join(__dirname, '../public/css/custom.css'),
    path.join(__dirname, '../public/css/theme.css')
];

// Configuration des options
const options = {
    verbose: process.argv.includes('--verbose'),
    help: process.argv.includes('--help') || process.argv.includes('-h')
};

// Afficher l'aide si demandé
if (options.help) {
    console.log(`
🔧 Script de correction des permissions Sass

Usage: node scripts/fix-sass-permissions.cjs [options]

Options:
  --verbose   Afficher plus de détails
  --help, -h  Afficher cette aide

Ce script :
  - Supprime les fichiers CSS problématiques dans public/css/
  - Corrige les permissions si nécessaire
  - Nettoie les fichiers .map qui causent des erreurs
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

function fixSassPermissions() {
    log('Début de la correction des permissions Sass...', 'info');
    
    let fixedCount = 0;
    let errorCount = 0;
    
    problematicFiles.forEach(filePath => {
        if (fs.existsSync(filePath)) {
            try {
                // Essayer de supprimer le fichier
                fs.unlinkSync(filePath);
                log(`Supprimé : ${path.basename(filePath)}`, 'success');
                fixedCount++;
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
    
    // Vérifier que le dossier resources/css existe
    const resourcesCssDir = path.join(__dirname, '../resources/css');
    if (!fs.existsSync(resourcesCssDir)) {
        try {
            fs.mkdirSync(resourcesCssDir, { recursive: true });
            log('Dossier resources/css créé', 'success');
        } catch (error) {
            log(`Erreur lors de la création du dossier resources/css : ${error.message}`, 'error');
            errorCount++;
        }
    }
    
    if (fixedCount > 0) {
        log(`${fixedCount} fichier(s) nettoyé(s) avec succès`, 'success');
    } else {
        log('Aucun fichier à nettoyer', 'info');
    }
    
    if (errorCount > 0) {
        log(`${errorCount} erreur(s) lors du nettoyage`, 'error');
        return false;
    }
    
    return true;
}

// Exécuter la correction
if (fixSassPermissions()) {
    log('Correction des permissions terminée avec succès', 'success');
} else {
    log('Correction terminée avec des erreurs', 'error');
    process.exit(1);
} 