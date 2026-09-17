<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Project\DevReportsService;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Sécurité : chemins de rapports Markdown confinés à storage/app/dev-reports.
 */
class DevReportsServiceSecurityTest extends TestCase
{
    private DevReportsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DevReportsService;
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/dev-report-security-other'));
        parent::tearDown();
    }

    public function test_is_allowed_new_report_path_accepts_reports_directory_only(): void
    {
        $dir = $this->service->storageDirectory();
        File::ensureDirectoryExists($dir);
        $uuid = uniqid('t', false);
        $path = $dir.DIRECTORY_SEPARATOR.'review-web-unit-'.$uuid.'.md';

        $this->assertTrue($this->service->isAllowedNewReportPath($path));
    }

    public function test_is_allowed_new_report_path_rejects_different_directory(): void
    {
        $outside = storage_path('app/dev-report-security-other');
        File::ensureDirectoryExists($outside);
        $path = $outside.DIRECTORY_SEPARATOR.'notevil.md';

        $this->assertFalse($this->service->isAllowedNewReportPath($path));
    }

    #[DataProvider('invalidBasenames')]
    public function test_is_allowed_new_report_path_rejects_dangerous_basename(string $filename): void
    {
        $dir = $this->service->storageDirectory();
        File::ensureDirectoryExists($dir);

        $this->assertFalse($this->service->isAllowedNewReportPath($dir.DIRECTORY_SEPARATOR.$filename));
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function invalidBasenames(): iterable
    {
        yield 'null_byte' => ["evil-\0trail.md"];

        yield 'hidden_leading_dot' => ['.env.md'];

        yield 'subdir_in_concat_path' => ['a'.DIRECTORY_SEPARATOR.'b.md'];

        yield 'wrong_ext' => ['x.txt'];

        yield 'spaces' => ['my report.md'];

        yield 'unicode' => ["rapport\u{00e9}.md"];

        yield 'parent_dots_in_name' => ['..md'];

        yield 'shell_chars' => ['a&b.md'];
    }

    public function test_resolve_safe_download_rejects_when_realpath_escapes_via_prefix_collision(): void
    {
        $dir = $this->service->storageDirectory();
        File::ensureDirectoryExists($dir);
        $evilParent = dirname($dir).DIRECTORY_SEPARATOR.basename($dir).'-evil-boundary-test';
        File::ensureDirectoryExists($evilParent);
        $evilFile = $evilParent.DIRECTORY_SEPARATOR.'escaped.md';
        File::put($evilFile, 'secret');

        $symlinkBasename = 'symlink-evil-boundary-test.md';
        $symlinkPath = $dir.DIRECTORY_SEPARATOR.$symlinkBasename;
        @unlink($symlinkPath);
        if (! @symlink($evilFile, $symlinkPath)) {
            $this->markTestSkipped('symlink() non disponible ou interdit sur cette plateforme.');

            return;
        }

        $resolved = $this->service->resolveSafeDownloadPath($symlinkBasename);
        try {
            $this->assertNull($resolved, 'Le téléchargement ne doit pas suivre un symlink hors du dossier rapport.');
        } finally {
            @unlink($symlinkPath);
            File::deleteDirectory($evilParent);
        }
    }
}
