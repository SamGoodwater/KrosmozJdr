<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LoadingTip;
use Illuminate\Database\Seeder;

/**
 * Astuces d’écran de chargement — idempotent via {@see LoadingTip::updateOrCreate} sur le texte.
 */
class LoadingTipSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            [
                'body' => 'N’hésite pas à faire des retours : ils aident à améliorer l’outil.',
                'url' => null,
                'featured' => true,
            ],
            [
                'body' => 'Les panoplies sont un ensemble d’équipements qui se renforcent ensemble.',
                'url' => null,
                'featured' => false,
            ],
            [
                'body' => 'N’hésite pas à participer au projet sur GitHub.',
                'url' => 'https://github.com/SamGoodwater/KrosmozJdr',
                'featured' => true,
            ],
            [
                'body' => 'Rejoins-nous sur Discord pour discuter du JDR et de l’app.',
                'url' => 'https://discord.gg/XVu4VWFskj',
                'featured' => true,
            ],
            [
                'body' => 'Tu peux filtrer les bibliothèques par état : brut, brouillon, jouable…',
                'url' => null,
                'featured' => false,
            ],
            [
                'body' => 'Les fiches en état « auto » sont des propositions à relire avant publication.',
                'url' => null,
                'featured' => false,
            ],
            [
                'body' => 'Favoris : marque une entité pour la retrouver rapidement.',
                'url' => null,
                'featured' => false,
            ],
            [
                'body' => 'La recherche globale trouve pages, sections et entités de jeu.',
                'url' => null,
                'featured' => false,
            ],
        ];

        foreach ($definitions as $row) {
            LoadingTip::updateOrCreate(
                ['body' => $row['body']],
                [
                    'url' => $row['url'],
                    'featured' => $row['featured'],
                    'is_active' => true,
                    'duration_seconds' => $row['duration_seconds'] ?? LoadingTip::DEFAULT_DURATION_SECONDS,
                ]
            );
        }
    }
}
