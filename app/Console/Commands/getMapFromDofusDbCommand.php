<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class getMapFromDofusDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:map';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Récupère la map depuis la base de données de DofusDB.com';


    const PATH_DEST = 'storage/app/private/maps/';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Crée le dossier si il n'existe pas
        if (!is_dir(self::PATH_DEST)) {
            mkdir(self::PATH_DEST, 0777, true);
        }

        // URL de base
        $url_base = 'https://api.dofusdb.fr/img/worlds/1/1/';

        // Nombre total d'images
        $total_images = 1280;

        for ($i = 1; $i <= $total_images; $i++) {
            // Crée l'URL de l'image
            $image_url = $url_base . $i . '.jpg';

            // Nom de fichier pour sauvegarder l'image
            $save_path = self::PATH_DEST . $i . '.jpg';

            // Télécharge l'image
            $image_data = file_get_contents($image_url);

            // Vérifie si l'image a été téléchargée avec succès
            if ($image_data !== false) {
                // Sauvegarde l'image dans le répertoire spécifié
                file_put_contents($save_path, $image_data);
                $this->info("Image $i téléchargée avec succès.");
            } else {
                $this->info("Erreur lors du téléchargement de l'image $i.");
            }
        }
    }
}
