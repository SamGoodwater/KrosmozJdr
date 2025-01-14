<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Modules\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    public function testAttributeCreation()
    {
        $attribute = Attribute::factory()->create([
            'name' => 'Test Attribute',
            'uniqid' => 'test-uniqid',
            'is_visible' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => 'Test Attribute',
            'uniqid' => 'test-uniqid',
            'is_visible' => true,
        ]);
    }

    public function testAttributeRelations()
    {
        $attribute = Attribute::factory()->create();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $attribute->classes);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $attribute->creatures);
    }

    public function testImagePathFunction()
    {
        $attribute = Attribute::factory()->create(['image' => 'test-image.jpg']);
        $this->assertStringContainsString('test-image.jpg', $attribute->imagePath());
    }
}
