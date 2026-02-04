<?php

namespace App\Console\Commands;

use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;

class IconsJsonGenerator extends Command
{
    use GuardsProductionEnvironment;

    const PATH_DEST = 'storage/app/public/icons/icons.json';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:IconsGenerator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère un fichier JSON contenant les liens de toutes les images dans des dossiers spécifiques';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->guardDevelopmentOnly()) {
            return self::FAILURE;
        }

        $directories = [
            'storage/app/public/images/',  // Dossier racine des images
            'storage/app/public/images/icons/',
            'storage/app/public/images/icons/modules/',
            'storage/app/public/images/icons/modules/classes',
            'storage/app/public/images/icons/modules/dices',
            'storage/app/public/images/icons/modules/spell_zone',
            'storage/app/public/images/icons/modules/classe_orientations/',
        ];

        $imageLinks = [];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                $this->error('Le dossier ' . $directory . ' n\'existe pas.');
                continue;
            }
            $this->info('Recherche d\'images dans le dossier : ' . $directory);
            $files = $this->getAllFiles($directory);
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME);
                    $relativePath = str_replace('storage/app/public/', 'storage/', $file);
                    $this->info('Image trouvée : ' . $file);

                    // Extraire le chemin relatif à partir de storage/app/public/images/
                    $pathParts = explode('images/', $file);
                    if (count($pathParts) > 1) {
                        $directoryPath = dirname($pathParts[1]);
                        $directoryKey = $directoryPath === '.' ? 'images' : 'images/' . $directoryPath;

                        if (!isset($imageLinks[$directoryKey])) {
                            $imageLinks[$directoryKey] = [];
                        }
                        $imageLinks[$directoryKey][$fileName] = $relativePath;
                        $this->info('Lien de l\'image : [' . $directoryKey . "] [" . $fileName . '] => ' . $relativePath);
                    }
                } else {
                    $this->error('Image non trouvée : ' . $file);
                    continue;
                }
            }
        }

        // Vérifiez et créez le répertoire si nécessaire
        $directoryPath = dirname(self::PATH_DEST);
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $json = json_encode($imageLinks, JSON_PRETTY_PRINT); // ou JSON_UNESCAPED_UNICODE
        file_put_contents(self::PATH_DEST, $json);

        $this->info('Fichier JSON généré avec succès.');

        return 0;
    }

    /**
     * Get all files from a directory recursively.
     *
     * @param string $directory
     * @return array
     */
    private function getAllFiles($directory)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
