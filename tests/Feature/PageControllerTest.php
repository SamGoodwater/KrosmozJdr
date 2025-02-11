<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatePage()
    {
        $user = User::factory()->admin()->create();

        $response = $this->withoutMiddleware()->actingAs($user)->post('/page/store', [
            'name' => 'Test Page',
            'slug' => 'test-page',
            'uniqid' => uniqid(),
            'is_public' => true,
            'is_visible' => true,
            'is_editable' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pages', ['name' => 'Test Page']);
    }

    public function testUpdatePage()
    {
        $user = User::factory()->admin()->create();
        $page = Page::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->patch("/page/{$page->id}", [
            'name' => 'New Name',
            'slug' => 'new-name',
            'uniqid' => $page->uniqid,
            'is_public' => true,
            'is_visible' => true,
            'is_editable' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pages', ['name' => 'New Name']);
    }

    public function testDeletePage()
    {
        $user = User::factory()->admin()->create();
        $page = Page::factory()->create(['uniqid' => uniqid()]);

        $response = $this->actingAs($user)->delete("/page/{$page->uniqid}");
        $response->assertStatus(302);
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }
}
