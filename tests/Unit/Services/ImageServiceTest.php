<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImageServiceTest extends TestCase
{
    private ImageService $imageService;
    private string $testImagePath = 'test-images/test.jpg';
    private string $testImageContent;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une image de test
        $this->testImageContent = file_get_contents(base_path('tests/Unit/Services/fixtures/test.jpg'));
        Storage::disk('public')->put($this->testImagePath, $this->testImageContent);

        $this->imageService = new ImageService();
    }

    protected function tearDown(): void
    {
        // Nettoyer les fichiers de test
        Storage::disk('public')->delete($this->testImagePath);
        Storage::disk('public')->deleteDirectory('thumbnails');

        parent::tearDown();
    }

    public function test_exists_returns_true_for_existing_image()
    {
        $this->assertTrue($this->imageService->exists($this->testImagePath));
    }

    public function test_exists_returns_false_for_non_existing_image()
    {
        $this->assertFalse($this->imageService->exists('non-existing.jpg'));
    }

    public function test_getFullPath_returns_correct_path()
    {
        $expectedPath = Storage::disk('public')->path($this->testImagePath);
        $this->assertEquals($expectedPath, $this->imageService->getFullPath($this->testImagePath));
    }

    public function test_isFontAwesome_returns_true_for_fa_icons()
    {
        $this->assertTrue($this->imageService->isFontAwesome('fa-user'));
        $this->assertTrue($this->imageService->isFontAwesome('storage/images/fa-user'));
    }

    public function test_isFontAwesome_returns_false_for_regular_images()
    {
        $this->assertFalse($this->imageService->isFontAwesome('images/photo.jpg'));
    }

    public function test_generateThumbnail_creates_thumbnail_with_correct_dimensions()
    {
        $options = [
            'width' => 100,
            'height' => 100,
            'fit' => 'cover'
        ];

        $thumbnailPath = $this->imageService->generateThumbnail($this->testImagePath, $options);

        $this->assertNotNull($thumbnailPath);
        $this->assertTrue(Storage::disk('public')->exists($thumbnailPath));

        // Vérifier les dimensions
        $image = (new ImageManager(new Driver()))->read(Storage::disk('public')->path($thumbnailPath));
        $this->assertEquals(100, $image->width());
        $this->assertEquals(100, $image->height());
    }

    public function test_generateThumbnail_returns_null_for_non_existing_image()
    {
        $thumbnailPath = $this->imageService->generateThumbnail('non-existing.jpg');
        $this->assertNull($thumbnailPath);
    }

    public function test_convertToWebp_converts_image_to_webp()
    {
        $webpPath = $this->imageService->convertToWebp($this->testImagePath);

        $this->assertTrue(Storage::disk('public')->exists($webpPath));
        $this->assertEquals('webp', pathinfo($webpPath, PATHINFO_EXTENSION));

        // Vérifier que l'image originale a été supprimée
        $this->assertFalse(Storage::disk('public')->exists($this->testImagePath));
    }

    public function test_cleanThumbnails_removes_old_thumbnails()
    {
        // Créer quelques thumbnails
        $this->imageService->generateThumbnail($this->testImagePath, ['width' => 100, 'height' => 100]);

        // Attendre 1 seconde pour s'assurer que les fichiers sont plus vieux que le délai
        sleep(1);

        // Nettoyer les thumbnails plus vieux que 1 seconde
        $this->imageService->cleanThumbnails(1);

        // Vérifier que les thumbnails ont été supprimés
        $this->assertEmpty(Storage::disk('public')->files('thumbnails'));
    }
}
