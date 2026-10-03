<?php

declare(strict_types=1);

/**
 * Mode d’application des seeders.
 *
 * Par défaut (`overwrite` = false), les seeders ne créent que les lignes absentes :
 * la base est la source de vérité. Les fichiers sous database/seeders/data/ sont
 * une copie versionnée, réécrite hors production lors de project:backup.
 *
 * SEED_OVERWRITE=true ou SeedMode::forceOverwrite(true) (ex. project:seed --overwrite)
 * réapplique le comportement historique updateOrCreate / purge d’orphelins.
 */
return [

    'overwrite' => (bool) env('SEED_OVERWRITE', false),

];
