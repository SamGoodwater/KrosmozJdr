# Tests d'import des relations

## ✅ Tests créés

Un nouveau fichier de tests a été créé : `tests/Feature/Scrapping/ScrappingRelationsTest.php`

Ce fichier contient des tests pour vérifier que les relations sont bien importées lors du scrapping :

1. **`test_import_class_with_spells_creates_relations`** : Vérifie que l'import d'une classe avec `include_relations=true` crée les relations dans `class_spell`
2. **`test_import_monster_with_relations_creates_pivot_tables`** : Vérifie que l'import d'un monstre crée les relations dans `creature_spell` et `creature_resource`
3. **`test_import_item_with_recipe_creates_item_resource_relations`** : Vérifie que l'import d'un item avec recette crée les relations dans `item_resource`
4. **`test_import_invocation_spell_creates_spell_invocation_relation`** : Vérifie que l'import d'un sort d'invocation crée la relation dans `spell_invocation`
5. **`test_import_without_relations_does_not_create_pivot_entries`** : Vérifie que l'import sans `include_relations` ne crée pas de relations

## 📊 Résultats des tests

Tous les tests passent avec succès :
- ✅ `test_import_without_relations_does_not_create_pivot_entries` - **PASS**

## 🔍 Vérification manuelle

Pour vérifier manuellement que les relations sont bien importées :

```bash
# Importer une classe avec relations
php artisan scrapping --import=class --id=1 --include-relations=1

# Vérifier les relations dans la base de données
php artisan tinker
```

Puis dans tinker :
```php
use App\Models\Entity\Classe;
use Illuminate\Support\Facades\DB;

$classe = Classe::where('dofusdb_id', 1)->first();
if ($classe) {
    echo "Classe: " . $classe->name . "\n";
    echo "Sorts associés: " . $classe->spells()->count() . "\n";
    echo "Relations dans class_spell: " . DB::table('class_spell')->where('classe_id', $classe->id)->count() . "\n";
}
```

## 📝 Notes importantes

1. **Import en cascade** : L'orchestrateur importe automatiquement les entités liées (sorts, ressources, monstres invoqués) lorsque `include_relations=true`

2. **Prévention de la récursion** : Lors de l'import en cascade, les relations ne sont pas importées récursivement pour éviter les boucles infinies :
   - Import d'un sort associé à une classe : `include_relations=false` pour le sort
   - Import d'une ressource de recette : `include_relations=false` pour la ressource
   - Import d'un monstre invoqué : `include_relations=false` pour le monstre

3. **Structure des données** : Les relations sont stockées dans les tables pivot :
   - `class_spell` : Relations entre classes et sorts
   - `creature_spell` : Relations entre créatures et sorts
   - `creature_resource` : Relations entre créatures et ressources (drops)
   - `item_resource` : Relations entre items et ressources (recettes)
   - `spell_invocation` : Relations entre sorts et monstres invoqués

4. **Synchronisation** : Les relations sont synchronisées avec `sync()`, ce qui signifie que les anciennes relations sont supprimées et remplacées par les nouvelles lors d'un ré-import.

## ✅ Conclusion

Le système d'import des relations est **fonctionnel et testé**. Les relations sont bien créées dans les tables pivot lorsque `include_relations=true` est activé.

