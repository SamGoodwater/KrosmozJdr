const https = require('https');
const fs = require('fs');
const path = require('path');

// Configuration
const API_BASE = 'https://api.dofusdb.fr';
const TIMEOUT = 15000; // 15 secondes
const LANG = 'fr';
const OUTPUT_DIR = path.join(__dirname, 'dofusdb-analysis');

// Créer le dossier de sortie
if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}

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

// Analyser la structure complète des types d'objets
async function analyzeItemTypesStructure() {
    console.log('🔍 Analyse de la structure des types d\'objets...\n');
    
    const analysis = {
        timestamp: new Date().toISOString(),
        totalObjects: 0,
        types: {},
        superTypes: {},
        categories: {}
    };

    // Test de recherche globale pour obtenir le total
    try {
        const globalResult = await makeRequest(`/items?lang=${LANG}&$limit=1`);
        analysis.totalObjects = globalResult.data.total || 0;
        console.log(`📊 Total d'objets dans l'API: ${analysis.totalObjects}`);
    } catch (error) {
        console.log(`❌ Erreur recherche globale: ${error.message}`);
    }

    // Analyser chaque type d'objet
    const itemTypes = [
        { id: 1, name: 'Arme', expectedSuperType: 1 },
        { id: 2, name: 'Arc', expectedSuperType: 2 },
        { id: 3, name: 'Bouclier', expectedSuperType: 2 },
        { id: 4, name: 'Type 4', expectedSuperType: null },
        { id: 5, name: 'Type 5', expectedSuperType: null },
        { id: 6, name: 'Type 6', expectedSuperType: null },
        { id: 7, name: 'Type 7', expectedSuperType: null },
        { id: 8, name: 'Type 8', expectedSuperType: null },
        { id: 9, name: 'Anneau', expectedSuperType: 3 },
        { id: 10, name: 'Amulette', expectedSuperType: 4 },
        { id: 11, name: 'Ceinture', expectedSuperType: 5 },
        { id: 12, name: 'Potion', expectedSuperType: 6 },
        { id: 13, name: 'Bottes', expectedSuperType: 6 },
        { id: 14, name: 'Chapeau', expectedSuperType: 6 },
        { id: 15, name: 'Ressource', expectedSuperType: 9 },
        { id: 16, name: 'Équipement', expectedSuperType: 10 },
        { id: 17, name: 'Type 17', expectedSuperType: null },
        { id: 18, name: 'Type 18', expectedSuperType: null },
        { id: 19, name: 'Type 19', expectedSuperType: null },
        { id: 20, name: 'Type 20', expectedSuperType: null },
        { id: 35, name: 'Fleur', expectedSuperType: 9 },
        { id: 203, name: 'Cosmétique', expectedSuperType: 26 },
        { id: 204, name: 'Animal de compagnie', expectedSuperType: null },
        { id: 205, name: 'Monture', expectedSuperType: 14 }
    ];

    for (const itemType of itemTypes) {
        try {
            console.log(`📦 Analyse ${itemType.name} (type ${itemType.id})...`);
            const result = await makeRequest(`/items?typeId=${itemType.id}&lang=${LANG}&$limit=3`);
            
            if (result.data && result.data.data && result.data.data.length > 0) {
                const firstItem = result.data.data[0];
                const typeInfo = firstItem.type || {};
                
                analysis.types[itemType.id] = {
                    name: itemType.name,
                    total: result.data.total,
                    superTypeId: typeInfo.superTypeId,
                    categoryId: typeInfo.categoryId,
                    superTypeName: typeInfo.superType?.name?.fr || typeInfo.superType?.name?.en || 'Inconnu',
                    categoryName: typeInfo.name?.fr || typeInfo.name?.en || 'Inconnu',
                    sampleItems: result.data.data.slice(0, 2).map(item => ({
                        id: item.id,
                        name: item.name?.fr || item.name?.en || 'Nom non trouvé',
                        level: item.level,
                        iconId: item.iconId
                    }))
                };

                // Analyser le superType
                if (typeInfo.superTypeId) {
                    if (!analysis.superTypes[typeInfo.superTypeId]) {
                        analysis.superTypes[typeInfo.superTypeId] = {
                            name: typeInfo.superType?.name?.fr || typeInfo.superType?.name?.en || 'Inconnu',
                            types: []
                        };
                    }
                    analysis.superTypes[typeInfo.superTypeId].types.push(itemType.id);
                }

                // Analyser la catégorie
                if (typeInfo.categoryId !== undefined) {
                    if (!analysis.categories[typeInfo.categoryId]) {
                        analysis.categories[typeInfo.categoryId] = {
                            name: typeInfo.name?.fr || typeInfo.name?.en || 'Inconnu',
                            types: []
                        };
                    }
                    analysis.categories[typeInfo.categoryId].types.push(itemType.id);
                }

                console.log(`   ✅ ${result.data.total} objets trouvés`);
                console.log(`   🏷️  SuperType: ${typeInfo.superTypeId} (${typeInfo.superType?.name?.fr || 'Inconnu'})`);
                console.log(`   📂 Catégorie: ${typeInfo.categoryId} (${typeInfo.name?.fr || 'Inconnu'})`);
            } else {
                analysis.types[itemType.id] = {
                    name: itemType.name,
                    total: 0,
                    error: 'Aucun objet trouvé'
                };
                console.log(`   ⚠️  Aucun objet trouvé`);
            }
        } catch (error) {
            analysis.types[itemType.id] = {
                name: itemType.name,
                error: error.message
            };
            console.log(`   ❌ Erreur: ${error.message}`);
        }
        console.log('');
    }

    return analysis;
}

// Analyser la structure des autres entités
async function analyzeOtherEntities() {
    console.log('🔍 Analyse des autres entités...\n');
    
    const analysis = {
        entities: {}
    };

    const entities = [
        { path: '/breeds', name: 'Classes (Breeds)', key: 'breeds' },
        { path: '/monsters', name: 'Monstres', key: 'monsters' },
        { path: '/spells', name: 'Sorts', key: 'spells' },
        { path: '/spell-levels', name: 'Niveaux de sorts', key: 'spellLevels' },
        { path: '/effects', name: 'Effets', key: 'effects' },
        { path: '/item-sets', name: 'Ensembles d\'items', key: 'itemSets' }
    ];

    for (const entity of entities) {
        try {
            console.log(`📦 Analyse ${entity.name}...`);
            const result = await makeRequest(`${entity.path}?lang=${LANG}&$limit=3`);
            
            if (result.data && result.data.data && result.data.data.length > 0) {
                const firstEntity = result.data.data[0];
                
                analysis.entities[entity.key] = {
                    total: result.data.total,
                    sampleData: firstEntity,
                    structure: {
                        fields: Object.keys(firstEntity),
                        hasName: !!(firstEntity.name),
                        hasDescription: !!(firstEntity.description),
                        hasId: !!(firstEntity.id || firstEntity._id)
                    }
                };

                console.log(`   ✅ ${result.data.total} entités trouvées`);
                console.log(`   📝 Champs: ${Object.keys(firstEntity).length}`);
                console.log(`   🏷️  Nom: ${firstEntity.name ? 'Oui' : 'Non'}`);
                console.log(`   📖 Description: ${firstEntity.description ? 'Oui' : 'Non'}`);
            } else {
                analysis.entities[entity.key] = {
                    total: 0,
                    error: 'Aucune entité trouvée'
                };
                console.log(`   ⚠️  Aucune entité trouvée`);
            }
        } catch (error) {
            analysis.entities[entity.key] = {
                error: error.message
            };
            console.log(`   ❌ Erreur: ${error.message}`);
        }
        console.log('');
    }

    return analysis;
}

// Sauvegarder l'analyse
function saveAnalysis(itemTypesAnalysis, entitiesAnalysis) {
    const fullAnalysis = {
        timestamp: new Date().toISOString(),
        apiBase: API_BASE,
        language: LANG,
        itemTypes: itemTypesAnalysis,
        entities: entitiesAnalysis
    };

    const filename = `dofusdb-structure-analysis-${new Date().toISOString().split('T')[0]}.json`;
    const filepath = path.join(OUTPUT_DIR, filename);
    
    fs.writeFileSync(filepath, JSON.stringify(fullAnalysis, null, 2));
    console.log(`💾 Analyse sauvegardée dans: ${filepath}`);
    
    // Créer un résumé lisible
    const summaryPath = path.join(OUTPUT_DIR, 'summary.md');
    const summary = generateSummary(fullAnalysis);
    fs.writeFileSync(summaryPath, summary);
    console.log(`📝 Résumé créé dans: ${summaryPath}`);
}

// Générer un résumé en Markdown
function generateSummary(analysis) {
    let summary = `# Analyse de l'API DofusDB\n\n`;
    summary += `**Date d'analyse:** ${analysis.timestamp}\n`;
    summary += `**API Base:** ${analysis.apiBase}\n`;
    summary += `**Langue:** ${analysis.language}\n\n`;

    summary += `## 📊 Résumé des types d'objets\n\n`;
    summary += `**Total d'objets:** ${analysis.itemTypes.totalObjects || 'Inconnu'}\n\n`;

    summary += `### 🏷️ Types d'objets identifiés\n\n`;
    summary += `| Type ID | Nom | Total | SuperType | Catégorie |\n`;
    summary += `|---------|-----|-------|-----------|-----------|\n`;

    Object.entries(analysis.itemTypes.types).forEach(([typeId, typeInfo]) => {
        if (typeInfo.total !== undefined) {
            summary += `| ${typeId} | ${typeInfo.name} | ${typeInfo.total} | ${typeInfo.superTypeId || '-'} | ${typeInfo.categoryId || '-'} |\n`;
        }
    });

    summary += `\n### 🔗 Hiérarchie des SuperTypes\n\n`;
    Object.entries(analysis.itemTypes.superTypes).forEach(([superTypeId, superTypeInfo]) => {
        summary += `- **SuperType ${superTypeId}:** ${superTypeInfo.name}\n`;
        summary += `  - Types: ${superTypeInfo.types.join(', ')}\n\n`;
    });

    summary += `### 📂 Catégories\n\n`;
    Object.entries(analysis.itemTypes.categories).forEach(([categoryId, categoryInfo]) => {
        summary += `- **Catégorie ${categoryId}:** ${categoryInfo.name}\n`;
        summary += `  - Types: ${categoryInfo.types.join(', ')}\n\n`;
    });

    summary += `## 🏗️ Structure des entités\n\n`;
    Object.entries(analysis.entities).forEach(([entityKey, entityInfo]) => {
        if (entityInfo.total !== undefined) {
            summary += `### ${entityKey.charAt(0).toUpperCase() + entityKey.slice(1)}\n`;
            summary += `- **Total:** ${entityInfo.total}\n`;
            summary += `- **Champs:** ${entityInfo.structure?.fields?.length || 0}\n`;
            summary += `- **Nom:** ${entityInfo.structure?.hasName ? 'Oui' : 'Non'}\n`;
            summary += `- **Description:** ${entityInfo.structure?.hasDescription ? 'Oui' : 'Non'}\n\n`;
        }
    });

    return summary;
}

// Fonction principale
async function main() {
    console.log('🚀 Démarrage de l\'analyse complète de l\'API DofusDB...\n');
    
    try {
        const itemTypesAnalysis = await analyzeItemTypesStructure();
        const entitiesAnalysis = await analyzeOtherEntities();
        
        saveAnalysis(itemTypesAnalysis, entitiesAnalysis);
        
        console.log('✅ Analyse complète terminée !');
        console.log(`📁 Résultats sauvegardés dans: ${OUTPUT_DIR}`);
    } catch (error) {
        console.error('❌ Erreur générale:', error.message);
    }
}

// Exécution
main();
