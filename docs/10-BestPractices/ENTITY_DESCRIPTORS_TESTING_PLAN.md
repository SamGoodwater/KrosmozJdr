# Plan de tests — Entity Descriptors System

**Date** : 2025-01-27  
**Périmètre** : Tests pour le système de descriptors (Option B)

---

## 🎯 Objectifs

1. **Valider le système descriptor** sur toutes les entités migrées
2. **Prévenir les régressions** lors des modifications futures
3. **Documenter le comportement attendu** (tests = spécification vivante)
4. **Assurer la cohérence** entre frontend et backend

---

## 📋 Pack de tests (priorisé)

### **P1 - Critique : Controllers Bulk (Backend)**

#### Feature Tests (`tests/Feature/Api/Bulk/`)

| Test | Description | Effort |
|------|-------------|--------|
| `CreatureBulkControllerTest::test_admin_can_bulk_update_creatures()` | Admin peut mettre à jour plusieurs créatures | 20min |
| `CreatureBulkControllerTest::test_validation_fails_with_invalid_ids()` | Validation échoue avec IDs invalides | 15min |
| `CreatureBulkControllerTest::test_only_provided_fields_are_updated()` | Seuls les champs fournis sont modifiés | 20min |
| `CreatureBulkControllerTest::test_nullable_fields_can_be_cleared()` | Champs nullable peuvent être vidés | 15min |
| `CreatureBulkControllerTest::test_transaction_rollback_on_error()` | Rollback transaction en cas d'erreur | 20min |
| `NpcBulkControllerTest::test_admin_can_bulk_update_npcs()` | Admin peut mettre à jour plusieurs NPCs | 20min |
| `NpcBulkControllerTest::test_foreign_key_validation()` | Validation des clés étrangères (classe_id, specialization_id) | 20min |
| `ClasseBulkControllerTest::test_admin_can_bulk_update_classes()` | Admin peut mettre à jour plusieurs classes | 20min |
| `ConsumableBulkControllerTest::test_admin_can_bulk_update_consumables()` | Admin peut mettre à jour plusieurs consommables | 20min |
| `ConsumableBulkControllerTest::test_consumable_type_validation()` | Validation du type de consommable | 15min |

**Total P1** : ~3h (10 tests pour 4 controllers, à étendre aux 11 autres)

---

### **P2 - Important : TableControllers format=entities (Backend)**

#### Feature Tests (`tests/Feature/Api/Table/`)

| Test | Description | Effort |
|------|-------------|--------|
| `SpellTableControllerTest::test_format_entities_returns_raw_data()` | `?format=entities` retourne données brutes | 15min |
| `SpellTableControllerTest::test_format_cells_returns_formatted_cells()` | Format par défaut retourne cells formatées | 15min |
| `SpellTableControllerTest::test_entities_format_includes_relations()` | Format entities inclut les relations | 20min |
| `SpellTableControllerTest::test_entities_format_respects_permissions()` | Permissions respectées dans format entities | 15min |
| `SpellTableControllerTest::test_entities_format_pagination()` | Pagination fonctionne avec format entities | 15min |

**Total P2** : ~2h (5 tests par entité, à créer pour 16 entités = 80 tests, mais on peut commencer par 1-2 entités)

---

### **P3 - Important : Adapters Frontend (Vitest)**

#### Unit Tests (`tests/unit/adapters/`)

| Test | Description | Effort |
|------|-------------|--------|
| `spell-adapter.test.js::buildSpellCell()` | Génération de cellules pour différents champs | 30min |
| `spell-adapter.test.js::adaptSpellEntitiesTableResponse()` | Adaptation de réponse entities → TableResponse | 20min |
| `spell-adapter.test.js::handles_null_values()` | Gestion des valeurs nulles | 15min |
| `spell-adapter.test.js::handles_relations()` | Gestion des relations (ex: createdBy) | 20min |
| `spell-adapter.test.js::formatting_dates()` | Formatage des dates | 15min |
| `spell-adapter.test.js::formatting_numbers()` | Formatage des nombres | 15min |

**Total P3** : ~2h par entité (à créer pour 16 entités, mais on peut commencer par 2-3 entités représentatives)

---

### **P4 - Utile : Descriptors Frontend (Vitest)**

#### Unit Tests (`tests/unit/descriptors/`)

| Test | Description | Effort |
|------|-------------|--------|
| `spell-descriptors.test.js::getSpellFieldDescriptors_structure()` | Structure des descriptors | 20min |
| `spell-descriptors.test.js::visibleIf_respects_permissions()` | `visibleIf` respecte les permissions | 15min |
| `spell-descriptors.test.js::editableIf_respects_permissions()` | `editableIf` respecte les permissions | 15min |
| `spell-descriptors.test.js::bulk_configuration()` | Configuration bulk correcte | 20min |
| `spell-descriptors.test.js::field_groups()` | Groupes de champs | 15min |

**Total P4** : ~1h30 par entité (à créer pour 16 entités, mais on peut commencer par 2-3 entités)

---

### **P5 - Utile : Utils et Composables (Vitest)**

#### Unit Tests

| Test | Description | Effort |
|------|-------------|--------|
| `descriptor-form.test.js::createFieldsConfigFromDescriptors()` | Génération de fieldsConfig | 20min |
| `descriptor-form.test.js::createBulkFieldMetaFromDescriptors()` | Génération de fieldMeta | 20min |
| `descriptor-form.test.js::createDefaultEntityFromDescriptors()` | Génération de defaultEntity | 15min |
| `entity-registry.test.js::getEntityConfig()` | Récupération de config par type | 15min |
| `entity-registry.test.js::normalizeEntityType()` | Normalisation des types d'entités | 15min |
| `useBulkEditPanel.test.js::aggregation()` | Agrégation des valeurs | 30min |
| `useBulkEditPanel.test.js::buildPayload()` | Construction du payload | 20min |
| `useBulkEditPanel.test.js::dirty_tracking()` | Suivi des champs modifiés | 20min |
| `useBulkRequest.test.js::success_handling()` | Gestion des succès | 15min |
| `useBulkRequest.test.js::error_handling()` | Gestion des erreurs | 15min |

**Total P5** : ~3h

---

## 📊 Estimation totale

### Phase 1 : Tests critiques (P1 + P2 partiel)
- **P1** : 10 tests, ~3h (4 controllers)
- **P2** : 5 tests, ~1h (1 entité représentative)
- **Total Phase 1** : ~4h

### Phase 2 : Tests frontend essentiels (P3 partiel + P5)
- **P3** : 6 tests, ~2h (1 entité représentative)
- **P5** : 10 tests, ~3h
- **Total Phase 2** : ~5h

### Phase 3 : Tests complets (P2 + P3 + P4 pour toutes les entités)
- **P2** : 80 tests, ~16h (16 entités)
- **P3** : 96 tests, ~32h (16 entités)
- **P4** : 80 tests, ~24h (16 entités)
- **Total Phase 3** : ~72h

**Recommandation** : Commencer par Phase 1 + Phase 2, puis étendre progressivement.

---

## 🚀 Implémentation recommandée

### Étape 1 : Tests critiques (1-2 jours)
1. Créer `CreatureBulkControllerTest` (5 tests)
2. Créer `SpellTableControllerTest` avec format=entities (5 tests)
3. Créer `spell-adapter.test.js` (6 tests)
4. Créer tests utils/composables (10 tests)

### Étape 2 : Étendre aux autres entités (progressif)
- Ajouter tests bulk pour les autres controllers (1 par semaine)
- Ajouter tests adapters pour les autres entités (1 par semaine)

### Étape 3 : Tests E2E (optionnel)
- Tests Playwright pour valider le workflow complet

---

## ✅ Critères de succès

1. **Coverage** : ≥70% sur les controllers bulk et adapters
2. **Non-régression** : Tous les tests passent après modifications
3. **CI/CD** : Tests lancés automatiquement sur chaque PR
4. **Documentation** : Chaque test documente un comportement attendu

---

## 📝 Exemple de test

### Backend (PHPUnit)

```php
<?php

namespace Tests\Feature\Api\Bulk;

use App\Models\User;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatureBulkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_update_creatures(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $creature1 = Creature::factory()->create(['level' => '10']);
        $creature2 = Creature::factory()->create(['level' => '20']);

        $response = $this->actingAs($admin)
            ->patchJson('/api/entities/creatures/bulk', [
                'ids' => [$creature1->id, $creature2->id],
                'level' => '50',
                'usable' => true,
            ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJson(['summary' => ['updated' => 2]]);

        $this->assertDatabaseHas('creatures', [
            'id' => $creature1->id,
            'level' => '50',
        ]);
        $this->assertDatabaseHas('creatures', [
            'id' => $creature2->id,
            'level' => '50',
        ]);
    }
}
```

### Frontend (Vitest)

```javascript
import { describe, it, expect } from 'vitest';
import { buildSpellCell, adaptSpellEntitiesTableResponse } from '@/Entities/spell/spell-adapter';

describe('spell-adapter', () => {
  it('buildSpellCell génère une cellule route pour name', () => {
    const entity = { id: 1, name: 'Test Spell' };
    const cell = buildSpellCell('name', entity, {}, { context: 'table' });
    
    expect(cell.type).toBe('route');
    expect(cell.value).toBe('Test Spell');
    expect(cell.params.href).toContain('/spells/1');
  });

  it('adaptSpellEntitiesTableResponse transforme entities en TableResponse', () => {
    const response = {
      meta: { entityType: 'spells' },
      entities: [
        { id: 1, name: 'Spell 1', level: '10' },
        { id: 2, name: 'Spell 2', level: '20' },
      ],
    };
    
    const result = adaptSpellEntitiesTableResponse(response);
    
    expect(result.meta.entityType).toBe('spells');
    expect(result.rows).toHaveLength(2);
    expect(result.rows[0].cells.name.type).toBe('route');
  });
});
```

---

## 🔗 Fichiers de tests (structure)

```
tests/
├── Feature/
│   └── Api/
│       ├── Bulk/
│       │   ├── CreatureBulkControllerTest.php
│       │   ├── NpcBulkControllerTest.php
│       │   ├── ClasseBulkControllerTest.php
│       │   └── ConsumableBulkControllerTest.php
│       └── Table/
│           ├── SpellTableControllerTest.php
│           └── ...
├── unit/
│   ├── adapters/
│   │   ├── spell-adapter.test.js
│   │   └── ...
│   ├── descriptors/
│   │   ├── spell-descriptors.test.js
│   │   └── ...
│   ├── utils/
│   │   ├── descriptor-form.test.js
│   │   └── entity-registry.test.js
│   └── composables/
│       ├── useBulkEditPanel.test.js
│       └── useBulkRequest.test.js
```

---

## 📚 Références

- [TESTING_PRACTICES.md](./TESTING_PRACTICES.md)
- [ENTITY_FIELD_DESCRIPTORS.md](../../30-UI/ENTITY_FIELD_DESCRIPTORS.md)
- [PLAN_MIGRATION_DESCRIPTORS.md](../../30-UI/PLAN_MIGRATION_DESCRIPTORS.md)

