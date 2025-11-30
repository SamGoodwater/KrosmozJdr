<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Spell;
use App\Models\Entity\Classe;
use App\Models\Type\SpellType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour SpellController
 * 
 * Vérifie que :
 * - Un utilisateur peut modifier un sort qu'il a créé
 * - Un admin peut modifier n'importe quel sort
 * - La méthode updateClasses synchronise correctement les classes
 * - La méthode updateSpellTypes synchronise correctement les types de sort
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class SpellControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Désactiver le middleware role pour les tests (on teste les policies directement)
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        // Désactiver explicitement le CSRF pour ces tests
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Test : Un utilisateur peut ajouter des classes à son sort
     */
    public function test_user_can_add_classes_to_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateClasses', $spell), [
                '_method' => 'PATCH',
                'classes' => [$classe1->id, $classe2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->classes);
        $this->assertTrue($spell->fresh()->classes->contains($classe1));
        $this->assertTrue($spell->fresh()->classes->contains($classe2));
    }

    /**
     * Test : Un utilisateur peut retirer des classes de son sort
     */
    public function test_user_can_remove_classes_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();
        $classe3 = Classe::factory()->create();
        
        // Ajouter initialement 3 classes
        $spell->classes()->attach([$classe1->id, $classe2->id, $classe3->id]);

        // Retirer classe2 et classe3, garder seulement classe1
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [$classe1->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $spell->fresh()->classes);
        $this->assertTrue($spell->fresh()->classes->contains($classe1));
        $this->assertFalse($spell->fresh()->classes->contains($classe2));
        $this->assertFalse($spell->fresh()->classes->contains($classe3));
    }

    /**
     * Test : Un utilisateur peut remplacer toutes les classes de son sort
     */
    public function test_user_can_replace_all_classes_in_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $oldClasse = Classe::factory()->create();
        $newClasse1 = Classe::factory()->create();
        $newClasse2 = Classe::factory()->create();
        
        // Ajouter une classe initialement
        $spell->classes()->attach($oldClasse->id);

        // Remplacer par de nouvelles classes
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [$newClasse1->id, $newClasse2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->classes);
        $this->assertFalse($spell->fresh()->classes->contains($oldClasse));
        $this->assertTrue($spell->fresh()->classes->contains($newClasse1));
        $this->assertTrue($spell->fresh()->classes->contains($newClasse2));
    }

    /**
     * Test : Un utilisateur peut vider toutes les classes d'un sort
     */
    public function test_user_can_clear_all_classes_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();
        
        // Ajouter des classes initialement
        $spell->classes()->attach([$classe1->id, $classe2->id]);

        // Vider toutes les classes
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $spell->fresh()->classes);
    }

    /**
     * Test : Un admin peut modifier les classes de n'importe quel sort
     */
    public function test_admin_can_update_classes_of_any_spell(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $classe1 = Classe::factory()->create();
        $classe2 = Classe::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [$classe1->id, $classe2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->classes);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier les classes d'un sort qu'il n'a pas créé
     */
    public function test_user_cannot_update_classes_of_other_user_spell(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $classe1 = Classe::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [$classe1->id],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $spell->fresh()->classes);
    }

    /**
     * Test : La validation échoue si classes n'est pas un array
     */
    public function test_update_classes_fails_if_classes_is_not_array(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('classes');
    }

    /**
     * Test : La validation échoue si une classe n'existe pas
     */
    public function test_update_classes_fails_if_classe_does_not_exist(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classe = Classe::factory()->create();
        
        // Supprimer définitivement la classe pour qu'elle n'existe plus
        $classe->forceDelete();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => [$classe->id],
            ]);

        $response->assertSessionHasErrors('classes.0');
    }

    /**
     * Test : La validation échoue si classes est manquant
     */
    public function test_update_classes_fails_if_classes_is_missing(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateClasses', $spell), [
                '_method' => 'PATCH',
            ]);

        $response->assertSessionHasErrors('classes');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les classes
     */
    public function test_guest_cannot_update_classes(): void
    {
        $spell = Spell::factory()->create();
        $classe = Classe::factory()->create();

        $response = $this->post(route('entities.spells.updateClasses', $spell), [
            '_method' => 'PATCH',
            'classes' => [$classe->id],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : Un utilisateur peut ajouter des types de sort à son sort
     */
    public function test_user_can_add_spell_types_to_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id, $spellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType1));
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType2));
    }

    /**
     * Test : Un utilisateur peut retirer des types de sort de son sort
     */
    public function test_user_can_remove_spell_types_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();
        $spellType3 = SpellType::factory()->create();
        
        // Ajouter initialement 3 types de sort
        $spell->spellTypes()->attach([$spellType1->id, $spellType2->id, $spellType3->id]);

        // Retirer spellType2 et spellType3, garder seulement spellType1
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $spell->fresh()->spellTypes);
        $this->assertTrue($spell->fresh()->spellTypes->contains($spellType1));
        $this->assertFalse($spell->fresh()->spellTypes->contains($spellType2));
        $this->assertFalse($spell->fresh()->spellTypes->contains($spellType3));
    }

    /**
     * Test : Un utilisateur peut remplacer tous les types de sort de son sort
     */
    public function test_user_can_replace_all_spell_types_in_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $oldSpellType = SpellType::factory()->create();
        $newSpellType1 = SpellType::factory()->create();
        $newSpellType2 = SpellType::factory()->create();
        
        // Ajouter un type de sort initialement
        $spell->spellTypes()->attach($oldSpellType->id);

        // Remplacer par de nouveaux types de sort
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$newSpellType1->id, $newSpellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
        $this->assertFalse($spell->fresh()->spellTypes->contains($oldSpellType));
        $this->assertTrue($spell->fresh()->spellTypes->contains($newSpellType1));
        $this->assertTrue($spell->fresh()->spellTypes->contains($newSpellType2));
    }

    /**
     * Test : Un utilisateur peut vider tous les types de sort d'un sort
     */
    public function test_user_can_clear_all_spell_types_from_own_spell(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();
        
        // Ajouter des types de sort initialement
        $spell->spellTypes()->attach([$spellType1->id, $spellType2->id]);

        // Vider tous les types de sort
        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $spell->fresh()->spellTypes);
    }

    /**
     * Test : Un admin peut modifier les types de sort de n'importe quel sort
     */
    public function test_admin_can_update_spell_types_of_any_spell(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $spellType1 = SpellType::factory()->create();
        $spellType2 = SpellType::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id, $spellType2->id],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $spell->fresh()->spellTypes);
    }

    /**
     * Test : Un utilisateur ne peut pas modifier les types de sort d'un sort qu'il n'a pas créé
     */
    public function test_user_cannot_update_spell_types_of_other_user_spell(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $spellType1 = SpellType::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType1->id],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $spell->fresh()->spellTypes);
    }

    /**
     * Test : La validation échoue si spellTypes n'est pas un array
     */
    public function test_update_spell_types_fails_if_spell_types_is_not_array(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('spellTypes');
    }

    /**
     * Test : La validation échoue si un type de sort n'existe pas
     */
    public function test_update_spell_types_fails_if_spell_type_does_not_exist(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellType = SpellType::factory()->create();
        
        // Supprimer définitivement le type de sort pour qu'il n'existe plus
        $spellType->forceDelete();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => [$spellType->id],
            ]);

        $response->assertSessionHasErrors('spellTypes.0');
    }

    /**
     * Test : La validation échoue si spellTypes est manquant
     */
    public function test_update_spell_types_fails_if_spell_types_is_missing(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
            ]);

        $response->assertSessionHasErrors('spellTypes');
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les types de sort
     */
    public function test_guest_cannot_update_spell_types(): void
    {
        $spell = Spell::factory()->create();
        $spellType = SpellType::factory()->create();

        $response = $this->post(route('entities.spells.updateSpellTypes', $spell), [
            '_method' => 'PATCH',
            'spellTypes' => [$spellType->id],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les classes et types de sort disponibles
     */
    public function test_edit_page_loads_available_classes_and_spell_types(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classe1 = Classe::factory()->create(['name' => 'Classe 1']);
        $classe2 = Classe::factory()->create(['name' => 'Classe 2']);
        $spellType1 = SpellType::factory()->create(['name' => 'Type 1']);
        $spell->classes()->attach($classe1->id);
        $spell->spellTypes()->attach($spellType1->id);

        $response = $this->actingAs($user)
            ->get(route('entities.spells.edit', $spell));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/spell/Edit')
            ->has('spell')
            ->has('availableClasses')
            ->has('availableSpellTypes')
            ->where('spell.data.classes.0.id', $classe1->id)
            ->where('spell.data.spellTypes.0.id', $spellType1->id)
        );
    }

    /**
     * Test : La synchronisation des classes fonctionne avec plusieurs classes
     */
    public function test_sync_classes_works_with_multiple_classes(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $classes = Classe::factory()->count(5)->create();
        $classeIds = $classes->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->patch(route('entities.spells.updateClasses', $spell), [
                'classes' => $classeIds,
            ]);

        $response->assertRedirect();
        $this->assertCount(5, $spell->fresh()->classes);
        foreach ($classes as $classe) {
            $this->assertTrue($spell->fresh()->classes->contains($classe));
        }
    }

    /**
     * Test : La synchronisation des types de sort fonctionne avec plusieurs types
     */
    public function test_sync_spell_types_works_with_multiple_types(): void
    {
        $user = User::factory()->create();
        $spell = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spellTypes = SpellType::factory()->count(5)->create();
        $spellTypeIds = $spellTypes->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->from(route('entities.spells.edit', $spell))
            ->post(route('entities.spells.updateSpellTypes', $spell), [
                '_method' => 'PATCH',
                'spellTypes' => $spellTypeIds,
            ]);

        $response->assertRedirect();
        $this->assertCount(5, $spell->fresh()->spellTypes);
        foreach ($spellTypes as $spellType) {
            $this->assertTrue($spell->fresh()->spellTypes->contains($spellType));
        }
    }
}

