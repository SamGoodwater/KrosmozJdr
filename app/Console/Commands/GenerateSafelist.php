<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateSafelist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:safelist
        {--output=resources/css/safelist.txt : Chemin de sortie du fichier safelist}
        {--base-url=http://localhost:8000 : URL de base pour les requêtes}
        {--auth : Utiliser un utilisateur authentifié}
        {--save-html=false : Sauvegarder les réponses HTML dans storage/app/debug/html}
        {--debug : Afficher plus d\'informations de débogage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scanne les routes de l\'application pour créer une safelist des classes Tailwind utilisées';

    private array $excludedPatterns = [
        '/^_debugbar/',
        '/^sanctum/',
        '/^livewire/',
        '/^api/',
        '/^horizon/',
        '/^\/?$/', // Page d'accueil (sera traitée séparément)
    ];

    private string $debugPath;
    private string $scrapperPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Vérifier que Node.js est installé
        if (!$this->checkNodeInstalled()) {
            $this->error('Node.js est requis pour exécuter cette commande.');
            return 1;
        }

        // Créer le script scrapper
        $this->createScrapperScript();

        $outputPath = $this->option('output');
        $baseUrl = $this->option('base-url');
        $useAuth = $this->option('auth');
        $saveHtml = $this->option('save-html');
        $debug = $this->option('debug');
        $classes = [];

        // Préparer le dossier de debug
        if ($saveHtml) {
            $this->debugPath = storage_path('app/debug/html');
            if (!File::exists($this->debugPath)) {
                File::makeDirectory($this->debugPath, 0755, true);
            } else {
                File::cleanDirectory($this->debugPath);
            }
        }

        // Préparer l'authentification si nécessaire
        if ($useAuth) {
            $user = User::where('role', 'admin')->first();
            if (!$user) {
                $this->error('Aucun utilisateur admin trouvé !');
                return 1;
            }
            Auth::login($user);
        }

        // Récupérer toutes les routes GET
        $routes = Route::getRoutes()->getRoutesByMethod()['GET'] ?? [];
        $urls = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            if (!$this->shouldExcludeRoute($uri)) {
                $urls[] = $baseUrl . '/' . ltrim($uri, '/');
            }
        }

        // Sauvegarder les URLs dans un fichier temporaire
        $urlsFile = storage_path('app/urls.json');
        File::put($urlsFile, json_encode($urls));

        // Exécuter le script Node.js
        $process = new Process([
            'node',
            $this->scrapperPath,
            $urlsFile,
            $this->debugPath,
            $debug ? '1' : '0'
        ]);

        $this->info('Analyse des pages avec Puppeteer...');

        try {
            $process->mustRun();
            $output = json_decode($process->getOutput(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Erreur lors du décodage JSON: " . json_last_error_msg());
            }

            $classes = $output['classes'] ?? [];
        } catch (ProcessFailedException $e) {
            $this->error('Erreur lors de l\'exécution du script: ' . $e->getMessage());
            return 1;
        } finally {
            // Nettoyage
            File::delete($urlsFile);
            File::delete($this->scrapperPath);
        }

        // Générer le fichier safelist
        $this->generateSafelistFile($classes, $outputPath);

        $this->newLine();
        $this->info(count($classes) . ' classes uniques trouvées et sauvegardées dans ' . $outputPath);

        if ($useAuth) {
            Auth::logout();
        }

        return 0;
    }

    private function checkNodeInstalled(): bool
    {
        $process = new Process(['node', '--version']);
        try {
            $process->run();
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function createScrapperScript(): void
    {
        $this->scrapperPath = storage_path('app/scrapper.js');

        $script = <<<'JS'
const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    const urlsFile = process.argv[2];
    const debugPath = process.argv[3];
    const debug = process.argv[4] === '1';

    const urls = JSON.parse(fs.readFileSync(urlsFile, 'utf8'));
    const browser = await puppeteer.launch();
    const classes = new Set();

    try {
        for (const url of urls) {
            if (debug) {
                console.error(`Analyzing ${url}...`);
            }

            const page = await browser.newPage();

            try {
                await page.goto(url, { waitUntil: 'networkidle0', timeout: 30000 });

                // Attendre que Vue.js ait fini de charger
                await page.waitForFunction(() => {
                    return !document.querySelector('[data-v-app]') ||
                           document.querySelector('[data-v-app]').__vue_app__;
                }, { timeout: 5000 });

                // Extraire les classes
                const pageClasses = await page.evaluate(() => {
                    const elements = document.querySelectorAll('*');
                    const classes = new Set();

                    elements.forEach(el => {
                        if (el.className && typeof el.className === 'string') {
                            el.className.split(' ').forEach(c => classes.add(c));
                        }
                    });

                    return Array.from(classes);
                });

                pageClasses.forEach(c => classes.add(c));

                if (debugPath) {
                    const html = await page.content();
                    const filename = url.replace(/[^a-z0-9]/gi, '_') + '.html';
                    fs.writeFileSync(`${debugPath}/${filename}`, html);
                }

            } catch (error) {
                console.error(`Error processing ${url}: ${error.message}`);
            } finally {
                await page.close();
            }
        }
    } finally {
        await browser.close();
    }

    // Filtrer et nettoyer les classes
    const cleanClasses = Array.from(classes)
        .filter(c => c && !c.includes('${') && !c.includes('{') && !c.includes('}'))
        .sort();

    console.log(JSON.stringify({ classes: cleanClasses }));
})();
JS;

        File::put($this->scrapperPath, $script);
    }

    private function generateSafelistFile(array $classes, string $outputPath): void
    {
        $content = "/* Fichier généré automatiquement - Ne pas modifier directement */\n\n";
        $content .= "@layer utilities {\n";
        $content .= "    .safelist {\n        @apply\n";

        foreach ($classes as $class) {
            $content .= "            $class\n";
        }

        $content .= "        ;\n    }\n}\n";

        File::put($outputPath, $content);
    }

    private function shouldExcludeRoute(string $uri): bool
    {
        foreach ($this->excludedPatterns as $pattern) {
            if (preg_match($pattern, $uri)) {
                return true;
            }
        }
        return false;
    }
}
