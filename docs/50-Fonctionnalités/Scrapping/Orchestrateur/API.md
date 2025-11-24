# API de l'Orchestrateur de Scrapping

## 📋 Vue d'ensemble

L'API de l'Orchestrateur de Scrapping fournit une interface REST complète pour gérer l'ensemble du processus de scrapping depuis des sites externes (comme DofusDB) vers KrosmozJDR. Cette API permet d'importer des entités individuelles, en lot, ou par catégorie, avec un suivi complet des processus et des métriques de performance.

## 🔌 Base URL

```
Base URL: /api/scrapping
Version: v1
Format: JSON
Authentification: Bearer Token (JWT)
```

## 📊 Endpoints d'import

### **Import d'entités individuelles**

#### **Import d'une classe**
```http
POST /api/scrapping/import/class/{dofusdb_id}
```

**Paramètres de chemin :**
- `dofusdb_id` (integer, requis) : ID de la classe dans DofusDB

**Corps de la requête :**
```json
{
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "notify_on_completion": true,
    "priority": "high",
    "timeout": 1800
  }
}
```

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "entity_type": "class",
    "dofusdb_id": 123,
    "status": "running",
    "progress": 0.0,
    "estimated_completion": "2025-01-15T10:05:00Z",
    "message": "Import de classe démarré avec succès"
  }
}
```

#### **Import d'un monstre**
```http
POST /api/scrapping/import/monster/{dofusdb_id}
```

**Paramètres de chemin :**
- `dofusdb_id` (integer, requis) : ID du monstre dans DofusDB

**Corps de la requête :**
```json
{
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "include_attributes": true,
    "include_capabilities": true,
    "priority": "normal"
  }
}
```

#### **Import d'un objet**
```http
POST /api/scrapping/import/item/{dofusdb_id}
```

**Paramètres de chemin :**
- `dofusdb_id` (integer, requis) : ID de l'objet dans DofusDB

**Corps de la requête :**
```json
{
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "detect_type": true,
    "priority": "normal"
  }
}
```

#### **Import d'un sort**
```http
POST /api/scrapping/import/spell/{dofusdb_id}
```

**Paramètres de chemin :**
- `dofusdb_id` (integer, requis) : ID du sort dans DofusDB

**Corps de la requête :**
```json
{
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "merge_levels": true,
    "priority": "normal"
  }
}
```

#### **Import d'un effet**
```http
POST /api/scrapping/import/effect/{dofusdb_id}
```

**Paramètres de chemin :**
- `dofusdb_id` (integer, requis) : ID de l'effet dans DofusDB

**Corps de la requête :**
```json
{
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "priority": "normal"
  }
}
```

### **Import en lot**

#### **Import de plusieurs entités**
```http
POST /api/scrapping/import/batch
```

**Corps de la requête :**
```json
{
  "entities": [
    {"type": "class", "id": 1},
    {"type": "class", "id": 2},
    {"type": "monster", "id": 100},
    {"type": "item", "id": 500},
    {"type": "spell", "id": 1000}
  ],
  "options": {
    "parallel_processing": true,
    "max_concurrent": 5,
    "stop_on_error": false,
    "batch_size": 10,
    "priority": "normal",
    "notify_on_completion": true
  }
}
```

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "type": "batch",
    "total_entities": 5,
    "status": "running",
    "progress": 0.0,
    "estimated_completion": "2025-01-15T10:30:00Z",
    "message": "Import en lot démarré avec succès"
  }
}
```

#### **Import par catégorie**

##### **Import de toutes les classes**
```http
POST /api/scrapping/import/classes
```

**Corps de la requête :**
```json
{
  "options": {
    "batch_size": 20,
    "max_concurrent": 3,
    "include_relations": true,
    "force_refresh": false,
    "priority": "low",
    "notify_on_completion": true
  }
}
```

##### **Import de tous les monstres**
```http
POST /api/scrapping/import/monsters
```

**Corps de la requête :**
```json
{
  "options": {
    "batch_size": 15,
    "max_concurrent": 3,
    "include_attributes": true,
    "include_capabilities": true,
    "priority": "low"
  }
}
```

##### **Import de tous les objets**
```http
POST /api/scrapping/import/items
```

**Corps de la requête :**
```json
{
  "options": {
    "batch_size": 50,
    "max_concurrent": 5,
    "detect_types": true,
    "include_effects": true,
    "priority": "low"
  }
}
```

##### **Import de tous les sorts**
```http
POST /api/scrapping/import/spells
```

**Corps de la requête :**
```json
{
  "options": {
    "batch_size": 30,
    "max_concurrent": 3,
    "merge_levels": true,
    "include_effects": true,
    "priority": "low"
  }
}
```

##### **Import de tous les effets**
```http
POST /api/scrapping/import/effects
```

**Corps de la requête :**
```json
{
  "options": {
    "batch_size": 100,
    "max_concurrent": 5,
    "priority": "low"
  }
}
```

## 📊 Endpoints de gestion des processus

### **Statut d'un processus**
```http
GET /api/scrapping/status/{process_id}
```

**Paramètres de chemin :**
- `process_id` (string, requis) : ID du processus

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "type": "individual",
    "entity_type": "class",
    "dofusdb_id": 123,
    "status": "running",
    "progress": 0.75,
    "current_step": "conversion",
    "started_at": "2025-01-15T10:00:00Z",
    "estimated_completion": "2025-01-15T10:05:00Z",
    "steps_completed": ["validation", "translation", "collection", "restructuring"],
    "steps_remaining": ["conversion", "integration", "saving", "cleanup"],
    "errors": [],
    "warnings": []
  }
}
```

### **Progression d'un processus**
```http
GET /api/scrapping/progress/{process_id}
```

**Paramètres de chemin :**
- `process_id` (string, requis) : ID du processus

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "overall_progress": 0.75,
    "current_step": "conversion",
    "step_progress": {
      "validation": {"status": "completed", "duration": 0.1, "progress": 1.0},
      "translation": {"status": "completed", "duration": 0.2, "progress": 1.0},
      "collection": {"status": "completed", "duration": 2.5, "progress": 1.0},
      "restructuring": {"status": "completed", "duration": 0.8, "progress": 1.0},
      "conversion": {"status": "running", "duration": 1.2, "progress": 0.6},
      "integration": {"status": "pending", "duration": 0.0, "progress": 0.0},
      "saving": {"status": "pending", "duration": 0.0, "progress": 0.0},
      "cleanup": {"status": "pending", "duration": 0.0, "progress": 0.0}
    },
    "time_metrics": {
      "elapsed_time": 5.6,
      "estimated_remaining": 1.9,
      "efficiency_score": 0.87
    }
  }
}
```

### **Contrôle des processus**

#### **Mettre en pause un processus**
```http
POST /api/scrapping/pause/{process_id}
```

**Paramètres de chemin :**
- `process_id` (string, requis) : ID du processus

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "status": "paused",
    "message": "Processus mis en pause avec succès"
  }
}
```

#### **Reprendre un processus**
```http
POST /api/scrapping/resume/{process_id}
```

**Paramètres de chemin :**
- `process_id` (string, requis) : ID du processus

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "status": "running",
    "message": "Processus repris avec succès"
  }
}
```

#### **Annuler un processus**
```http
POST /api/scrapping/cancel/{process_id}
```

**Paramètres de chemin :**
- `process_id` (string, requis) : ID du processus

**Corps de la requête :**
```json
{
  "reason": "Demande utilisateur",
  "force": false
}
```

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "process_id": "uuid-v4",
    "status": "cancelled",
    "message": "Processus annulé avec succès"
  }
}
```

## 📈 Endpoints de monitoring

### **Historique des processus**
```http
GET /api/scrapping/history
```

**Paramètres de requête :**
- `page` (integer, optionnel) : Numéro de page (défaut: 1)
- `per_page` (integer, optionnel) : Nombre d'éléments par page (défaut: 20)
- `status` (string, optionnel) : Filtrer par statut (completed, failed, cancelled, etc.)
- `type` (string, optionnel) : Filtrer par type (individual, batch, category)
- `entity_type` (string, optionnel) : Filtrer par type d'entité
- `start_date` (date, optionnel) : Date de début pour le filtrage
- `end_date` (date, optionnel) : Date de fin pour le filtrage

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "processes": [
      {
        "process_id": "uuid-v4",
        "type": "individual",
        "entity_type": "class",
        "dofusdb_id": 123,
        "status": "completed",
        "progress": 1.0,
        "started_at": "2025-01-15T10:00:00Z",
        "completed_at": "2025-01-15T10:05:00Z",
        "duration": 300,
        "entities_processed": 1,
        "entities_successful": 1,
        "entities_failed": 0
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 150,
      "last_page": 8
    }
  }
}
```

### **Métriques de performance**
```http
GET /api/scrapping/metrics
```

**Paramètres de requête :**
- `period` (string, optionnel) : Période (day, week, month, year, défaut: day)
- `start_date` (date, optionnel) : Date de début personnalisée
- `end_date` (date, optionnel) : Date de fin personnalisée

**Réponse de succès (200) :**
```json
{
  "success": true,
  "data": {
    "period": "day",
    "start_date": "2025-01-15T00:00:00Z",
    "end_date": "2025-01-15T23:59:59Z",
    "overall_metrics": {
      "total_processes": 25,
      "completed_processes": 23,
      "failed_processes": 1,
      "cancelled_processes": 1,
      "success_rate": 0.92,
      "average_duration": 1800
    },
    "performance_metrics": {
      "entities_imported": 150,
      "entities_per_hour": 6.25,
      "peak_concurrent_processes": 3,
      "average_memory_usage": 256.5,
      "average_cpu_usage": 45.8
    },
    "error_metrics": {
      "total_errors": 5,
      "error_rate": 0.08,
      "error_distribution": {
        "validation": 1,
        "collection": 2,
        "conversion": 1,
        "integration": 1
      }
    },
    "type_metrics": {
      "individual": {"count": 15, "success_rate": 0.93, "avg_duration": 1200},
      "batch": {"count": 8, "success_rate": 0.88, "avg_duration": 3600},
      "category": {"count": 2, "success_rate": 1.0, "avg_duration": 7200}
    }
  }
}
```

## ⚙️ Options d'import

### **Options communes**

#### **Options de validation**
```json
{
  "validate_before_save": true,        // Valider les données avant sauvegarde
  "create_relations": true,            // Créer les relations entre entités
  "notify_on_completion": true,        // Notifier à la fin du processus
  "priority": "normal",                // Priorité: low, normal, high, urgent
  "timeout": 1800                     // Timeout en secondes
}
```

#### **Options de traitement**
```json
{
  "parallel_processing": true,         // Traitement parallèle (pour les lots)
  "max_concurrent": 5,                // Nombre maximum de processus simultanés
  "stop_on_error": false,             // Arrêter en cas d'erreur
  "batch_size": 20,                   // Taille des lots
  "force_refresh": false               // Forcer la mise à jour
}
```

#### **Options spécifiques aux entités**
```json
{
  "include_attributes": true,          // Inclure les attributs (monstres)
  "include_capabilities": true,        // Inclure les capacités (monstres)
  "detect_types": true,                // Détection automatique des types (objets)
  "include_effects": true,             // Inclure les effets
  "merge_levels": true                 // Fusion des niveaux (sorts)
}
```

### **Priorités disponibles**
- **`low`** : Priorité basse, exécution en arrière-plan
- **`normal`** : Priorité normale, exécution standard
- **`high`** : Priorité élevée, exécution prioritaire
- **`urgent`** : Priorité urgente, exécution immédiate

## 🚨 Gestion des erreurs

### **Codes d'erreur HTTP**

- **`400 Bad Request`** : Paramètres invalides ou manquants
- **`401 Unauthorized`** : Authentification requise
- **`403 Forbidden`** : Permissions insuffisantes
- **`404 Not Found`** : Processus ou ressource non trouvé
- **`409 Conflict`** : Conflit avec un processus existant
- **`422 Unprocessable Entity`** : Données de validation échouées
- **`429 Too Many Requests`** : Limite de taux dépassée
- **`500 Internal Server Error`** : Erreur interne du serveur
- **`503 Service Unavailable`** : Service temporairement indisponible

### **Structure des erreurs**

```json
{
  "success": false,
  "error": {
    "code": "PROCESS_ALREADY_RUNNING",
    "message": "Un processus est déjà en cours pour cette entité",
    "details": {
      "entity_type": "class",
      "dofusdb_id": 123,
      "existing_process_id": "uuid-v4"
    },
    "suggestions": [
      "Attendre la fin du processus existant",
      "Annuler le processus existant avant de relancer"
    ]
  }
}
```

### **Codes d'erreur courants**

#### **Erreurs de validation**
- **`INVALID_DOFUSDB_ID`** : ID DofusDB invalide
- **`INVALID_ENTITY_TYPE`** : Type d'entité non supporté
- **`INVALID_OPTIONS`** : Options d'import invalides
- **`MISSING_REQUIRED_FIELD`** : Champ requis manquant

#### **Erreurs de processus**
- **`PROCESS_ALREADY_RUNNING`** : Processus déjà en cours
- **`PROCESS_NOT_FOUND`** : Processus non trouvé
- **`PROCESS_ALREADY_COMPLETED`** : Processus déjà terminé
- **`PROCESS_CANNOT_BE_CANCELLED`** : Processus non annulable

#### **Erreurs de ressources**
- **`RESOURCE_LIMIT_EXCEEDED`** : Limite de ressources dépassée
- **`TOO_MANY_CONCURRENT_PROCESSES`** : Trop de processus simultanés
- **`MEMORY_LIMIT_EXCEEDED`** : Limite mémoire dépassée
- **`TIMEOUT_EXCEEDED`** : Timeout dépassé

## 📝 Exemples d'utilisation

### **Import d'une classe avec options avancées**
```bash
curl -X POST "https://api.krosmozjdr.com/api/scrapping/import/class/123" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "options": {
      "validate_before_save": true,
      "create_relations": true,
      "notify_on_completion": true,
      "priority": "high",
      "timeout": 1800
    }
  }'
```

### **Import en lot de plusieurs entités**
```bash
curl -X POST "https://api.krosmozjdr.com/api/scrapping/import/batch" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "entities": [
      {"type": "class", "id": 1},
      {"type": "class", "id": 2},
      {"type": "monster", "id": 100}
    ],
    "options": {
      "parallel_processing": true,
      "max_concurrent": 3,
      "stop_on_error": false,
      "priority": "normal"
    }
  }'
```

### **Suivi de la progression d'un processus**
```bash
curl -X GET "https://api.krosmozjdr.com/api/scrapping/progress/UUID-V4" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### **Annulation d'un processus**
```bash
curl -X POST "https://api.krosmozjdr.com/api/scrapping/cancel/UUID-V4" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "reason": "Demande utilisateur",
    "force": false
  }'
```

## 🔒 Sécurité et authentification

### **Authentification**
- **Type** : JWT Bearer Token
- **Header** : `Authorization: Bearer <token>`
- **Expiration** : Token valide pendant 24h
- **Renouvellement** : Via endpoint de refresh

### **Autorisations**
- **`scrapping.import`** : Permet d'importer des entités
- **`scrapping.view`** : Permet de consulter les processus
- **`scrapping.control`** : Permet de contrôler les processus
- **`scrapping.admin`** : Permet l'accès administrateur complet

### **Rate Limiting**
- **Limite par utilisateur** : 10 requêtes par minute
- **Limite par IP** : 100 requêtes par minute
- **Limite globale** : 1000 requêtes par minute

### **Quotas**
- **Processus simultanés** : 3 par utilisateur
- **Processus par jour** : 50 par utilisateur
- **Taille des lots** : Maximum 100 entités par lot

## 📊 Webhooks et notifications

### **Configuration des webhooks**
```json
{
  "webhook_url": "https://votre-app.com/webhooks/scrapping",
  "events": ["process_started", "process_completed", "process_failed"],
  "secret": "votre_secret_webhook"
}
```

### **Événements disponibles**
- **`process_started`** : Processus démarré
- **`process_progress`** : Progression mise à jour
- **`process_completed`** : Processus terminé avec succès
- **`process_failed`** : Processus échoué
- **`process_cancelled`** : Processus annulé

### **Structure des webhooks**
```json
{
  "event": "process_completed",
  "timestamp": "2025-01-15T10:05:00Z",
  "process_id": "uuid-v4",
  "data": {
    "entity_type": "class",
    "dofusdb_id": 123,
    "duration": 300,
    "entities_processed": 1,
    "entities_successful": 1
  }
}
```

---

*API de l'orchestrateur de scrapping - Projet KrosmozJDR*
