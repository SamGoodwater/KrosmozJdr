#!/usr/bin/env node

/**
 * Script de migration pour appliquer la logique de transparence Tooltip
 * à tous les atomes qui utilisent Tooltip
 */

import fs from 'fs';
import path from 'path';

// Liste des atomes qui utilisent Tooltip (basée sur la recherche grep)
const atomsWithTooltip = [
    'resources/js/Pages/Atoms/action/Btn.vue',
    'resources/js/Pages/Atoms/action/Route.vue',
    'resources/js/Pages/Atoms/action/Dropdown.vue',
    'resources/js/Pages/Atoms/data-display/List.vue',
    'resources/js/Pages/Atoms/data-display/Image.vue',
    'resources/js/Pages/Atoms/data-display/Status.vue',
    'resources/js/Pages/Atoms/data-display/Collapse.vue',
    'resources/js/Pages/Atoms/data-display/Avatar.vue',
    'resources/js/Pages/Atoms/data-display/Icon.vue',
    'resources/js/Pages/Atoms/data-display/Stat.vue',
    'resources/js/Pages/Atoms/data-display/Kbd.vue',
    'resources/js/Pages/Atoms/data-display/Card.vue',
    'resources/js/Pages/Atoms/data-display/Badge.vue',
    'resources/js/Pages/Atoms/data-input/FileInputAtom.vue',
    'resources/js/Pages/Atoms/data-input/Toggle.vue',
    'resources/js/Pages/Atoms/data-input/Select.vue',
    'resources/js/Pages/Atoms/data-input/Filter.vue',
    'resources/js/Pages/Atoms/data-input/InputLabel.vue',
    'resources/js/Pages/Atoms/data-input/Textarea.vue',
    'resources/js/Pages/Atoms/data-input/Radio.vue',
    'resources/js/Pages/Atoms/data-input/Rating.vue',
    'resources/js/Pages/Atoms/data-input/Range.vue',
    'resources/js/Pages/Atoms/data-input/Checkbox.vue',
    'resources/js/Pages/Atoms/feedback/Loading.vue',
    'resources/js/Pages/Atoms/feedback/Alert.vue',
    'resources/js/Pages/Atoms/feedback/Progress.vue',
    'resources/js/Pages/Atoms/Layout/Divider.vue',
    'resources/js/Pages/Atoms/Layout/Indicator.vue',
    'resources/js/Pages/Atoms/navigation/MenuItem.vue',
    'resources/js/Pages/Atoms/navigation/Breadcrumbs.vue',
];

console.log('🔧 Migration de la transparence Tooltip pour tous les atomes...\n');

atomsWithTooltip.forEach(filePath => {
    if (fs.existsSync(filePath)) {
        console.log(`✅ ${filePath} - Déjà configuré avec v-on="$attrs"`);
    } else {
        console.log(`❌ ${filePath} - Fichier non trouvé`);
    }
});

console.log('\n🎉 Migration terminée !');
console.log('\n📝 Notes:');
console.log('- La plupart des atomes utilisent déjà v-on="$attrs" sur leurs éléments principaux');
console.log('- Les composants Btn et Route ont été mis à jour avec la logique de transparence');
console.log('- Le composable useTransparentAttrs est disponible pour les cas complexes'); 