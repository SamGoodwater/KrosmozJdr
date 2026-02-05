# Tests — Bonnes pratiques
 
- Mettre en place des tests unitaires et d'intégration (backend, frontend).
- Outils recommandés : PHPUnit (Laravel), Vitest/Jest (Vue), Cypress (E2E).
- Les tests doivent vérifier la présence des logs attendus, la cohérence des artefacts générés, la communication inter-composants.
- Couvrir les helpers, les formules, les composants critiques.
- Voir aussi : [CODE_DOCUMENTATION.md](./CODE_DOCUMENTATION.md)

---

## 📊 État actuel des tests

### Tests Backend (PHPUnit)

**159 tests passent** (941 assertions) en ~19 secondes.

#### Tests Bulk Controllers (14 fichiers)
- Tests pour toutes les opérations bulk (mise à jour en masse)
- Couverture : autorisation, validation, transactions, gestion d'erreurs
- **Localisation** : `tests/Feature/Api/Bulk/*BulkControllerTest.php`

#### Tests Table Controllers (14 fichiers)
- Tests pour les endpoints table avec format `entities` et `cells`
- Couverture : format de réponse, permissions, pagination, relations
- **Localisation** : `tests/Feature/Api/Table/*TableControllerTest.php`

### Tests Frontend (Vitest)

#### Tests Adapters (12 fichiers)
- Tests pour la transformation des données backend → frontend
- Couverture : génération de cellules, gestion des valeurs nulles, relations
- **Localisation** : `tests/unit/adapters/*-adapter.test.js`

#### Tests Utils/Composables (4 fichiers)
- Tests pour les utilitaires de descriptors et les composables bulk
- **Localisation** : `tests/unit/utils/` et `tests/unit/composables/`

**Voir** : [TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md](../100-%20Done/TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md) pour les détails complets.

---

## 🎯 Bonnes pratiques spécifiques

### Tests Backend

- **Nommage** : `*ControllerTest.php` pour les tests de contrôleurs
- **Structure** : Un test par cas d'usage (succès, échec, validation, permissions)
- **Assertions** : Vérifier à la fois le code HTTP et les données retournées
- **Fixtures** : Utiliser les factories Laravel pour créer les données de test
- **Transactions** : Utiliser `RefreshDatabase` pour isoler les tests
- **Base de données** : En PHPUnit, une base dédiée aux tests est **obligatoire**. Dans `phpunit.xml`, les variables `DB_CONNECTION=sqlite` et `DB_DATABASE=:memory:` doivent être définies. Sinon, les tests utilisent la base du `.env` (MySQL) et `RefreshDatabase` vide la base de développement à chaque exécution de `php artisan test`.

### Tests Frontend

- **Nommage** : `*.test.js` pour les tests Vitest
- **Structure** : Grouper par fonctionnalité avec `describe()`
- **Mocks** : Mocker les dépendances externes (route, Inertia, etc.)
- **Assertions** : Vérifier la structure ET le contenu des données transformées

### Exemple de test backend

```php
public function test_admin_can_bulk_update_entities(): void
{
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $entity1 = Entity::factory()->create();
    $entity2 = Entity::factory()->create();

    $response = $this->actingAs($admin)
        ->patchJson('/api/entities/entities/bulk', [
            'ids' => [$entity1->id, $entity2->id],
            'read_level' => User::ROLE_ADMIN,
        ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('entities', [
        'id' => $entity1->id,
        'read_level' => User::ROLE_ADMIN,
    ]);
}
```

### Exemple de test frontend

```javascript
describe('entity-adapter', () => {
    describe('buildEntityCell', () => {
        it('génère une cellule route pour name', () => {
            const entity = { id: 1, name: 'Test Entity' };
            const cell = buildEntityCell('name', entity, {}, { context: 'table' });

            expect(cell.type).toBe('route');
            expect(cell.value).toBe('Test Entity');
            expect(cell.params.href).toContain('/entities/1');
        });
    });
});
``` 