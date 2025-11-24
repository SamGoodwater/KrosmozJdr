# Définitions du Service Data-integration

## 📋 Vue d'ensemble

Ce document définit les structures de données, les règles d'intégration et les stratégies de gestion des conflits que le service Data-integration utilise pour intégrer les données converties dans la base de données KrosmozJDR.

## 🏗️ Architecture des données

### **Structure des entités intégrées**

#### **Créatures (Creatures)**
```json
{
  "id": "integer",
  "name": "string",
  "level": "integer",
  "health_points": "integer",
  "strength": "integer",
  "intelligence": "integer",
  "agility": "integer",
  "luck": "integer",
  "wisdom": "integer",
  "chance": "integer",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

#### **Objets (Items)**
```json
{
  "id": "integer",
  "name": "string",
  "type": "string",
  "level": "integer",
  "rarity": "string",
  "effects": "array",
  "bonus": "array",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

#### **Sorts (Spells)**
```json
{
  "id": "integer",
  "name": "string",
  "class": "string",
  "level": "integer",
  "cost": "integer",
  "effects": "array",
  "conditions": "array",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

#### **Ressources (Resources)**
```json
{
  "id": "integer",
  "name": "string",
  "type": "string",
  "rarity": "string",
  "properties": "array",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

## 🔗 Relations entre entités

### **Types de relations**

#### **Relations One-to-Many**
- **Campagne → Scénarios** : Une campagne peut avoir plusieurs scénarios
- **Scénario → Pages** : Un scénario peut avoir plusieurs pages
- **Page → Sections** : Une page peut avoir plusieurs sections
- **Classe → Spécialisations** : Une classe peut avoir plusieurs spécialisations

#### **Relations Many-to-Many**
- **Créatures ↔ Capacités** : Une créature peut avoir plusieurs capacités
- **Objets ↔ Ressources** : Un objet peut nécessiter plusieurs ressources
- **Scénarios ↔ PNJ** : Un scénario peut impliquer plusieurs PNJ
- **Campagnes ↔ Utilisateurs** : Une campagne peut avoir plusieurs utilisateurs

#### **Relations One-to-One**
- **PNJ ↔ Créature** : Un PNJ est lié à une créature
- **Monstre ↔ Créature** : Un monstre est lié à une créature
- **Boutique ↔ PNJ** : Une boutique est liée à un PNJ

### **Gestion des relations**

#### **Création des relations**
```php
// Exemple de création d'une relation Many-to-Many
$creature->capabilities()->attach($capabilityId, [
    'level_required' => $level,
    'created_at' => now()
]);
```

#### **Mise à jour des relations**
```php
// Exemple de mise à jour d'une relation
$creature->capabilities()->sync($capabilityIds);
```

#### **Suppression des relations**
```php
// Exemple de suppression d'une relation
$creature->capabilities()->detach($capabilityId);
```

## ⚠️ Gestion des conflits

### **Types de conflits**

#### **Conflits de clés primaires**
- **Définition** : Tentative d'insertion d'une entité avec un ID déjà existant
- **Stratégies** : Ignorer, mettre à jour, remplacer, fusionner
- **Exemple** : Deux créatures avec le même ID Dofus

#### **Conflits de contenu**
- **Définition** : Données différentes pour la même entité
- **Stratégies** : Ignorer, mettre à jour, remplacer, fusionner
- **Exemple** : Statistiques différentes pour la même créature

#### **Conflits de relations**
- **Définition** : Relations incohérentes entre entités
- **Stratégies** : Validation, correction automatique, rejet
- **Exemple** : Créature référençant une capacité inexistante

### **Stratégies de résolution**

#### **Ignorer (ignore)**
```php
// Ne pas traiter l'entité en conflit
if ($conflictStrategy === 'ignore') {
    continue; // Passer à l'entité suivante
}
```

#### **Mettre à jour (update)**
```php
// Mettre à jour l'entité existante
if ($conflictStrategy === 'update') {
    $existingEntity->update($newData);
}
```

#### **Remplacer (replace)**
```php
// Remplacer complètement l'entité existante
if ($conflictStrategy === 'replace') {
    $existingEntity->delete();
    $newEntity = Entity::create($newData);
}
```

#### **Fusionner (merge)**
```php
// Fusionner les données existantes et nouvelles
if ($conflictStrategy === 'merge') {
    $mergedData = array_merge($existingEntity->toArray(), $newData);
    $existingEntity->update($mergedData);
}
```

## 🔍 Validation des données

### **Règles de validation**

#### **Validation structurelle**
- **Champs obligatoires** : Vérification de la présence des champs requis
- **Types de données** : Vérification du type des valeurs
- **Format des données** : Vérification du format des chaînes, dates, etc.

#### **Validation métier**
- **Contraintes de domaine** : Vérification des règles métier
- **Cohérence des relations** : Vérification de l'intégrité référentielle
- **Validation des caractéristiques** : Vérification des limites et formules

#### **Validation d'intégrité**
- **Clés étrangères** : Vérification de l'existence des entités référencées
- **Contraintes uniques** : Vérification de l'unicité des valeurs
- **Contraintes de vérification** : Vérification des conditions métier

### **Exemples de validation**

#### **Validation d'une créature**
```php
// Validation des caractéristiques
$rules = [
    'name' => 'required|string|max:255',
    'level' => 'required|integer|min:1|max:200',
    'health_points' => 'required|integer|min:1',
    'strength' => 'required|integer|min:0',
    'intelligence' => 'required|integer|min:0',
    'agility' => 'required|integer|min:0',
    'luck' => 'required|integer|min:0',
    'wisdom' => 'required|integer|min:0',
    'chance' => 'required|integer|min:0',
];

$validator = Validator::make($creatureData, $rules);
```

#### **Validation des relations**
```php
// Vérification de l'existence des capacités référencées
foreach ($creatureData['capabilities'] as $capabilityId) {
    if (!Capability::find($capabilityId)) {
        throw new ValidationException("Capacité {$capabilityId} introuvable");
    }
}
```

## 📊 Gestion des transactions

### **Types de transactions**

#### **Transaction simple**
```php
DB::transaction(function () use ($entityData) {
    $entity = Entity::create($entityData);
    $entity->relations()->createMany($relationsData);
});
```

#### **Transaction avec rollback conditionnel**
```php
DB::transaction(function () use ($entityData) {
    $entity = Entity::create($entityData);
    
    if (!$this->validateRelations($entity, $relationsData)) {
        throw new ValidationException("Relations invalides");
    }
    
    $entity->relations()->createMany($relationsData);
}, 5); // 5 tentatives de retry
```

#### **Transaction en lot**
```php
DB::transaction(function () use ($entitiesData) {
    foreach (array_chunk($entitiesData, 100) as $chunk) {
        Entity::insert($chunk);
    }
});
```

### **Gestion des erreurs**

#### **Rollback automatique**
```php
try {
    DB::transaction(function () use ($data) {
        // Opérations d'intégration
    });
} catch (Exception $e) {
    Log::error('Erreur d\'intégration', [
        'error' => $e->getMessage(),
        'data' => $data
    ]);
    
    // Le rollback est automatique dans une transaction
    throw $e;
}
```

#### **Reprise après erreur**
```php
$maxRetries = 3;
$retryCount = 0;

while ($retryCount < $maxRetries) {
    try {
        DB::transaction(function () use ($data) {
            // Opérations d'intégration
        });
        break; // Succès, sortir de la boucle
    } catch (Exception $e) {
        $retryCount++;
        
        if ($retryCount >= $maxRetries) {
            throw $e; // Échec définitif
        }
        
        // Attendre avant de réessayer
        sleep(pow(2, $retryCount));
    }
}
```

## 🔒 Sécurité et permissions

### **Vérification des permissions**

#### **Permissions utilisateur**
```php
// Vérification du rôle de l'utilisateur
if (!auth()->user()->can('integrate_data')) {
    throw new AuthorizationException('Permission insuffisante');
}
```

#### **Validation des données d'entrée**
```php
// Sanitisation des données
$sanitizedData = $this->sanitizeInput($inputData);

// Validation des données
$validator = Validator::make($sanitizedData, $rules);
if ($validator->fails()) {
    throw new ValidationException($validator);
}
```

### **Protection contre les injections**

#### **Requêtes préparées**
```php
// Utilisation de requêtes préparées
$entity = Entity::where('external_id', $externalId)->first();

// Éviter les injections SQL
$entity = Entity::where('name', 'LIKE', "%{$searchTerm}%")->get();
```

#### **Validation des types**
```php
// Validation stricte des types
$level = filter_var($inputLevel, FILTER_VALIDATE_INT);
if ($level === false) {
    throw new ValidationException("Niveau invalide");
}
```

## 📈 Performance et optimisation

### **Optimisations de base de données**

#### **Indexation**
```sql
-- Index sur les champs de recherche fréquents
CREATE INDEX idx_creatures_level ON creatures(level);
CREATE INDEX idx_creatures_name ON creatures(name);
CREATE INDEX idx_items_type ON items(type);
```

#### **Requêtes optimisées**
```php
// Chargement eager des relations
$creatures = Creature::with(['capabilities', 'items'])->get();

// Requêtes en lot
Entity::insert($entitiesData);
```

### **Gestion de la mémoire**

#### **Traitement par lots**
```php
// Traitement par lots pour éviter la surcharge mémoire
foreach (array_chunk($entities, 100) as $chunk) {
    $this->processChunk($chunk);
    
    // Libération de la mémoire
    unset($chunk);
    gc_collect_cycles();
}
```

#### **Cache des données**
```php
// Mise en cache des données fréquemment utilisées
$characteristics = Cache::remember('characteristics', 3600, function () {
    return Characteristic::all();
});
```

## 🧪 Tests et validation

### **Tests unitaires**

#### **Test d'intégration d'entité**
```php
public function test_entity_integration()
{
    $entityData = [
        'name' => 'Test Creature',
        'level' => 50,
        'health_points' => 100
    ];
    
    $result = $this->dataIntegrationService->integrate($entityData);
    
    $this->assertTrue($result->success);
    $this->assertDatabaseHas('creatures', $entityData);
}
```

#### **Test de gestion des conflits**
```php
public function test_conflict_resolution()
{
    // Créer une entité existante
    $existingEntity = Creature::create(['name' => 'Test', 'level' => 50]);
    
    // Tenter d'intégrer la même entité
    $result = $this->dataIntegrationService->integrate([
        'name' => 'Test',
        'level' => 50,
        'health_points' => 200
    ], ['conflict_strategy' => 'update']);
    
    $this->assertTrue($result->success);
    $this->assertEquals(200, $existingEntity->fresh()->health_points);
}
```

### **Tests d'intégration**

#### **Test de flux complet**
```php
public function test_complete_integration_flow()
{
    // 1. Collecter les données
    $rawData = $this->dataCollectService->getCreatures();
    
    // 2. Convertir les données
    $convertedData = $this->dataConversionService->convert($rawData);
    
    // 3. Intégrer les données
    $result = $this->dataIntegrationService->integrate($convertedData);
    
    $this->assertTrue($result->success);
    $this->assertGreaterThan(0, $result->entities_processed);
}
```

---

*Définitions du service Data-integration - Projet KrosmozJDR*
