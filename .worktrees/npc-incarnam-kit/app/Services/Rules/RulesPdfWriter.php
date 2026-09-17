<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Écrit le livre de règles en PDF sur le disque public.
 *
 * Chromium (impression headless) est préféré : DomPDF sature sur le livre complet.
 * DomPDF reste le repli pour les extraits courts et les environnements sans navigateur.
 *
 * @example
 * $path = (new RulesPdfWriter())->write('<h1>Bonjour</h1>', 'downloads/generated/livre.pdf');
 */
class RulesPdfWriter
{
    private const DOMPDF_MAX_HTML_BYTES = 120_000;

    /**
     * @return string Chemin relatif sur le disque public
     */
    public function write(string $html, string $relativePath): string
    {
        $rendered = view('pdf.rules-book', [
            'title' => 'Krosmoz JDR — Livre de règles',
            'html' => $html,
        ])->render();

        $chromium = $this->chromiumBinary();
        if ($chromium !== null) {
            try {
                $this->writeWithChromium($chromium, $rendered, $relativePath);

                return $relativePath;
            } catch (\Throwable $exception) {
                if (strlen($rendered) > self::DOMPDF_MAX_HTML_BYTES) {
                    throw $exception;
                }
            }
        }

        if (strlen($rendered) > self::DOMPDF_MAX_HTML_BYTES) {
            throw new RuntimeException(
                'Le livre est trop long pour DomPDF. Installe Chromium (paquet « chromium ») pour compiler le PDF.'
            );
        }

        $pdf = Pdf::loadHTML($rendered)->setPaper('a4', 'portrait');
        $this->disk()->put($relativePath, $pdf->output());

        return $relativePath;
    }

    private function writeWithChromium(string $binary, string $htmlDocument, string $relativePath): void
    {
        $absolutePdf = $this->disk()->path($relativePath);
        $directory = dirname($absolutePdf);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $htmlFile = $directory.'/.rules-book-'.uniqid('', true).'.html';
        file_put_contents($htmlFile, $htmlDocument);

        try {
            $process = $this->chromiumPrintProcess($binary, $absolutePdf, $htmlFile, true);
            $process->setTimeout(300);
            $process->run();

            if (! $process->isSuccessful() || ! is_file($absolutePdf) || filesize($absolutePdf) < 100) {
                $fallback = $this->chromiumPrintProcess($binary, $absolutePdf, $htmlFile, false);
                $fallback->setTimeout(300);
                $fallback->run();
                $process = $fallback;
            }

            if (! $process->isSuccessful() || ! is_file($absolutePdf) || filesize($absolutePdf) < 100) {
                throw new RuntimeException(
                    'Chromium n’a pas produit le PDF : '.trim($process->getErrorOutput().' '.$process->getOutput())
                );
            }
        } finally {
            if (is_file($htmlFile)) {
                unlink($htmlFile);
            }
        }
    }

    private function chromiumPrintProcess(string $binary, string $absolutePdf, string $htmlFile, bool $newHeadless): Process
    {
        $args = [
            $binary,
            $newHeadless ? '--headless=new' : '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--no-pdf-header-footer',
            '--print-to-pdf='.$absolutePdf,
            'file://'.$htmlFile,
        ];

        return new Process($args);
    }

    private function chromiumBinary(): ?string
    {
        foreach (['chromium', 'chromium-browser', 'google-chrome', 'google-chrome-stable'] as $name) {
            $process = new Process(['bash', '-lc', 'command -v '.escapeshellarg($name)]);
            $process->run();
            $path = trim($process->getOutput());
            if ($process->isSuccessful() && $path !== '' && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    private function disk(): Filesystem
    {
        return Storage::disk((string) config('game_downloads.disk', 'public'));
    }
}
