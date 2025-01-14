<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Modules\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AttributeRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexRoute()
    {
        $response = $this->get(route('attribute.index'));
        $response->assertStatus(200);
    }

    public function testShowRoute()
    {
        $attribute = Attribute::factory()->create();
        $response = $this->get(route('attribute.show', $attribute));
        $response->assertStatus(200);
    }

    public function testCreateRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $response = $this->get(route('attribute.create'));
        $response->assertStatus(200);
    }

    public function testStoreRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $response = $this->post(route('attribute.store'), [
            'name' => 'Test Attribute',
            'uniqid' => 'test-uniqid',
            'is_visible' => true,
        ]);
        $response->assertRedirect(route('attribute.show', ['attribute' => 1]));
    }

    public function testEditRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $attribute = Attribute::factory()->create();
        $response = $this->get(route('attribute.edit', $attribute));
        $response->assertStatus(200);
    }

    public function testUpdateRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $attribute = Attribute::factory()->create();
        $response = $this->patch(route('attribute.update', $attribute), [
            'name' => 'Updated Attribute',
            'uniqid' => 'updated-uniqid',
            'is_visible' => false,
        ]);
        $response->assertRedirect(route('attribute.show', $attribute));
    }

    public function testDeleteRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $attribute = Attribute::factory()->create();
        $response = $this->delete(route('attribute.delete', $attribute));
        $response->assertRedirect(route('attribute.index'));
    }

    public function testRestoreRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $attribute = Attribute::factory()->create();
        $attribute->delete();
        $response = $this->post(route('attribute.restore', $attribute));
        $response->assertRedirect(route('attribute.index'));
    }

    public function testForceDeleteRoute()
    {
        Auth::loginUsingId(1); // Assurez-vous qu'un utilisateur avec l'ID 1 existe
        $attribute = Attribute::factory()->create();
        $response = $this->delete(route('attribute.forcedDelete', $attribute));
        $response->assertRedirect(route('attribute.index'));
    }
}
