const https = require('https');

// Configuration
const API_BASE = 'https://api.dofusdb.fr';
const TIMEOUT = 10000; // 10 secondes
const LANG = 'fr';

// Fonction pour faire une requête avec timeout
function makeRequest(path, timeout = TIMEOUT) {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => {
            reject(new Error(`Timeout après ${timeout}ms`));
        }, timeout);

        const req = https.get(`${API_BASE}${path}`, (res) => {
            clearTimeout(timer);
            
            let data = '';
            res.on('data', (chunk) => {
                data += chunk;
            });
            
            res.on('end', () => {
                try {
                    const jsonData = JSON.parse(data);
                    resolve({
                        status: res.statusCode,
                        headers: res.headers,
                        data: jsonData
                    });
                } catch (error) {
                    reject(new Error(`Erreur parsing JSON: ${error.message}`));
                }
            });
        });

        req.on('error', (error) => {
            clearTimeout(timer);
            reject(error);
        });

        req.on('timeout', () => {
            clearTimeout(timer);
            req.destroy();
            reject(new Error('Timeout de la requête'));
        });

        req.setTimeout(timeout);
    });
}

// Tests des différents types d'objets
async function testItemTypes() {
    console.log('🔍 Test des types d\'objets DofusDB...\n');
    
    const itemTypes = [
        { id: 1, name: 'Arme (type 1)' },
        { id: 2, name: 'Arc (type 2)' },
        { id: 3, name: 'Bouclier (type 3)' },
        { id: 9, name: 'Anneau (type 9)' },
        { id: 10, name: 'Amulette (type 10)' },
        { id: 11, name: 'Ceinture (type 11)' },
        { id: 12, name: 'Potion (type 12)' },
        { id: 13, name: 'Bottes (type 13)' },
        { id: 14, name: 'Chapeau (type 14)' },
        { id: 15, name: 'Ressource (type 15)' },
        { id: 16, name: 'Équipement (type 16)' },
        { id: 35, name: 'Fleur (type 35)' },
        { id: 203, name: 'Cosmétique (type 203)' },
        { id: 204, name: 'Animal de compagnie (type 204)' },
        { id: 205, name: 'Monture (type 205)' }
    ];

    for (const itemType of itemTypes) {
        try {
            console.log(`📦 Test ${itemType.name}...`);
            const result = await makeRequest(`/items?typeId=${itemType.id}&lang=${LANG}&$limit=2`);
            
            if (result.data && result.data.data && result.data.data.length > 0) {
                const firstItem = result.data.data[0];
                console.log(`   ✅ ${itemType.name}: ${result.data.total} objets trouvés`);
                console.log(`   📝 Premier objet: ${firstItem.name?.fr || firstItem.name?.en || 'Nom non trouvé'}`);
                console.log(`   🏷️  Type: ${firstItem.type?.id} (superType: ${firstItem.type?.superTypeId}, category: ${firstItem.type?.categoryId})`);
            } else {
                console.log(`   ⚠️  ${itemType.name}: Aucun objet trouvé`);
            }
        } catch (error) {
            console.log(`   ❌ ${itemType.name}: Erreur - ${error.message}`);
        }
        console.log('');
    }
}

// Test des autres entités
async function testOtherEntities() {
    console.log('🔍 Test des autres entités DofusDB...\n');
    
    const entities = [
        { path: '/breeds?lang=fr&$limit=2', name: 'Classes (Breeds)' },
        { path: '/monsters?lang=fr&$limit=2', name: 'Monstres' },
        { path: '/spells?lang=fr&$limit=2', name: 'Sorts' },
        { path: '/spell-levels?lang=fr&$limit=2', name: 'Niveaux de sorts' },
        { path: '/effects?lang=fr&$limit=2', name: 'Effets' },
        { path: '/item-sets?lang=fr&$limit=2', name: 'Ensembles d\'items' }
    ];

    for (const entity of entities) {
        try {
            console.log(`📦 Test ${entity.name}...`);
            const result = await makeRequest(entity.path);
            
            if (result.data && result.data.data && result.data.data.length > 0) {
                console.log(`   ✅ ${entity.name}: ${result.data.total} entités trouvées`);
                const firstEntity = result.data.data[0];
                console.log(`   📝 Premier élément: ID ${firstEntity.id || firstEntity._id}`);
            } else {
                console.log(`   ⚠️  ${entity.name}: Aucune entité trouvée`);
            }
        } catch (error) {
            console.log(`   ❌ ${entity.name}: Erreur - ${error.message}`);
        }
        console.log('');
    }
}

// Test de recherche globale
async function testGlobalSearch() {
    console.log('🔍 Test de recherche globale...\n');
    
    try {
        console.log('📦 Test recherche globale d\'objets...');
        const result = await makeRequest(`/items?lang=${LANG}&$limit=10`);
        
        if (result.data && result.data.data && result.data.data.length > 0) {
            console.log(`   ✅ Recherche globale: ${result.data.total} objets trouvés`);
            
            // Analyser les types trouvés
            const types = new Set();
            result.data.data.forEach(item => {
                if (item.type && item.type.id) {
                    types.add(item.type.id);
                }
            });
            
            console.log(`   🏷️  Types d'objets trouvés: ${Array.from(types).sort((a, b) => a - b).join(', ')}`);
        }
    } catch (error) {
        console.log(`   ❌ Recherche globale: Erreur - ${error.message}`);
    }
}

// Fonction principale
async function main() {
    console.log('🚀 Démarrage des tests de l\'API DofusDB...\n');
    
    try {
        await testItemTypes();
        await testOtherEntities();
        await testGlobalSearch();
        
        console.log('✅ Tous les tests terminés !');
    } catch (error) {
        console.error('❌ Erreur générale:', error.message);
    }
}

// Exécution
main();
