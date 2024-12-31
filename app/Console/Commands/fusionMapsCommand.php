<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class fusionMapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:fullmap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const PATH_DEST = 'storage/app/public/maps/';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Dimensions des images (chaque petite image mesure 32x32 pixels)
        $image_width = 250;
        $image_height = 250;

        // Nombre d'images en largeur et en hauteur
        $columns = 32; // 32 images par ligne
        $rows = 40;    // 40 lignes d'images

        // Largeur et hauteur de la grande image
        $total_width = $image_width * $columns;
        $total_height = $image_height * $rows;


        // create new manager instance with desired driver
        $manager = new ImageManager(['driver' => 'imagick']);

        // Créer une nouvelle image vide de la taille totale
        $final_image = $manager->create($total_width, $total_height);

        // Parcours des images pour les ajouter à l'image finale
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $columns; $col++) {
                $index = $row * $columns + $col;  // Calculer l'indice de l'image
                $image_path = public_path('images/' . ($index + 1) . '.jpg'); // Chemin vers l'image

                if (file_exists($image_path)) {
                    $small_image = $manager->read($image_path);

                    // Calcul des coordonnées pour positionner l'image sur la grande image
                    $x = $col * $image_width;
                    $y = $row * $image_height;

                    // Ajouter l'image à la position correcte
                    $final_image->insert($small_image, 'top-left', $x, $y);
                }
            }
        }

        // Sauvegarder l'image finale
        $final_image->save(public_path(self::PATH_DEST . 'fullmap.jpg'));

        $this->info("L'image a été fusionnée et sauvegardée avec succès!");
    }
}
