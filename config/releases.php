<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Version semver affichée par le changelog public (Markdown)
    |--------------------------------------------------------------------------
    |
    | Sert aux chemins `/changelog/feed/X.Y.Z` et au fichier
    | `storage/app/public/changelog/{semver}.md`. À aligner lors de la sortie prod.
    |
    */

    'public_changelog_semver' => env('PUBLIC_CHANGELOG_SEMVER', '1.3.2'),
];
