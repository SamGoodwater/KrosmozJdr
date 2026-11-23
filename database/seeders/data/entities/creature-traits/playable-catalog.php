<?php

declare(strict_types=1);

/**
 * Traits permanents jouables.
 *
 * - `level` : puissance. 1–3 très peu puissant, 4–6 peu puissant, 7–9 normal, 10–12 puissant, 13+ très puissant.
 *   La plupart des parties jouent autour du niveau 5 ou 6 : la majorité des traits est donc à 6 ou moins.
 *   Un personnage ne prend pas un trait au-dessus de son niveau.
 * - `available_for_characters` : vrai = un PJ ou un PNJ peut le prendre. Faux = créatures seulement.
 * - Rangs : « novice », « expérimenté », « maître ». Le rang supérieur remplace l’inférieur. On ne cumule pas.
 *
 * Plus de traits bas que de traits hauts. Les dons D&D d’armes, d’armures et de niveaux de sorts
 * ne sont pas repris : ici l’arme ne fait que donner des caractéristiques, on ne porte pas d’armure,
 * et un sort ne se paie pas en emplacements.
 *
 * @return list<array{name: string, description: string, image: string, level?: int|null, available_for_characters?: bool}>
 */
$icon = static fn (string $file): string => '/storage/images/icons/traits/'.$file;

$traits = [
    [
        'name' => 'Malade',
        'description' => 'Tu as un désavantage aux tests de Force et d’Agilité, selon les règles.',
        'image' => $icon('malade.png'),
    ],
    [
        'name' => 'Lourd',
        'description' => 'On te déplace difficilement. Tu ne peux pas avancer sur un sol fragile.',
        'image' => $icon('lourd.png'),
    ],
    [
        'name' => 'Petite taille',
        'description' => 'Tu as un avantage en discrétion. Ton déplacement est réduit.',
        'image' => $icon('petite-taille.png'),
    ],
    [
        'name' => 'Grande taille',
        'description' => 'Tu as un avantage en intimidation et un désavantage en discrétion.',
        'image' => $icon('grande-taille.png'),
    ],
    [
        'name' => 'Gigantesque',
        'description' => 'Tu es immense. Tu as un avantage en Force et en perception.',
        'image' => $icon('gigantesque.png'),
    ],
    [
        'name' => 'Insensible aux poisons',
        'description' => 'Tu ne peux pas être empoisonné·e.',
        'image' => $icon('insensible-aux-poisons.png'),
    ],
    [
        'name' => 'Métaboliseur rapide',
        'description' => 'Un poison avalé ne t’empoisonne pas.',
        'image' => $icon('metaboliseur-rapide.png'),
    ],
    [
        'name' => 'Vif / Vive',
        'description' => 'Tu commences le combat en premier, sans jet d’initiative.',
        'image' => $icon('vif.png'),
    ],
    [
        'name' => 'Agile',
        'description' => 'Tu réussis automatiquement tes jets de fuite. On ne peut pas te tacler.',
        'image' => $icon('agile.png'),
    ],
    [
        'name' => 'Abrupt',
        'description' => 'Tu as un avantage aux jets d’Intimidation. Tu as un désavantage aux jets de Supercherie et de Persuasion.',
        'image' => $icon('abrupt.png'),
    ],
    [
        'name' => 'Absorbé',
        'description' => 'Tu as un avantage aux jets d’Arcanes, d’Histoire, de Nature et de Religion. Tu as un désavantage aux jets de Perception.',
        'image' => $icon('absorbe.png'),
    ],
    [
        'name' => 'Agressif',
        'description' => 'Tu as un avantage au jet d’initiative. Ta CA diminue de 1.',
        'image' => $icon('agressif.png'),
    ],
    [
        'name' => 'Amorphe',
        'description' => 'Tu as un avantage aux jets de sauvegarde de Sagesse contre le charme. Tu as un désavantage au jet d’initiative.',
        'image' => $icon('amorphe.png'),
    ],
    [
        'name' => 'Appliqué',
        'description' => 'Tu as un avantage aux jets pour garder ta concentration. Tu as un désavantage aux jets de Perception.',
        'image' => $icon('applique.png'),
    ],
    [
        'name' => 'Bagarreur',
        'description' => 'Tu as un avantage aux attaques à mains nues et pour agripper. Tu as un désavantage aux autres jets d’attaque. Les armes naturelles n’en profitent pas.',
        'image' => $icon('bagarreur.png'),
    ],
    [
        'name' => 'Brutal',
        'description' => 'Tu as un désavantage aux jets d’attaque au corps à corps. Tu as un avantage aux jets de dégâts de ces attaques.',
        'image' => $icon('brutal.png'),
    ],
    [
        'name' => 'Cavalier-né',
        'description' => 'Tu as un avantage aux jets de Dressage pour monter ou diriger une monture. Tu as un désavantage aux autres jets de Dressage.',
        'image' => $icon('cavalier-ne.png'),
    ],
    [
        'name' => 'Corpulent',
        'description' => 'Tu as un avantage aux jets de Force pour ne pas être poussé ou renversé. Tu as un désavantage aux jets d’Acrobaties pour te dégager.',
        'image' => $icon('corpulent.png'),
    ],
    [
        'name' => 'Courageux',
        'description' => 'Tu as un avantage aux jets de sauvegarde de Sagesse. Tu as un désavantage aux jets de sauvegarde de Vitalité.',
        'image' => $icon('courageux.png'),
    ],
    [
        'name' => 'Désinvolte',
        'description' => 'Tu as un avantage aux jets de sauvegarde de Sagesse. Tu as un désavantage aux jets de sauvegarde d’Agilité.',
        'image' => $icon('desinvolte.png'),
    ],
    [
        'name' => 'Dur d\'oreille',
        'description' => 'Tu as un avantage aux jets de Perception qui reposent sur la vue. Tu as un désavantage à ceux qui reposent sur l’ouïe.',
        'image' => $icon('dur-d-oreille.png'),
    ],
    [
        'name' => 'Engageant',
        'description' => 'Tu as un avantage aux jets de Persuasion pour obtenir des renseignements. Tu as un désavantage aux jets d’Intimidation et de Perspicacité.',
        'image' => $icon('engageant.png'),
    ],
    [
        'name' => 'Honnête',
        'description' => 'Tu as un avantage aux jets de Persuasion. Tu as un désavantage aux jets de Supercherie et de Perspicacité.',
        'image' => $icon('honnete.png'),
    ],
    [
        'name' => 'Hypermétrope',
        'description' => 'Tu as un avantage aux jets de Perception à distance. Tu as un désavantage aux jets d’Investigation de près.',
        'image' => $icon('hypermetrope.png'),
    ],
    [
        'name' => 'Infatigable',
        'description' => 'Tu as un avantage aux jets de Vitalité pour prolonger un effort. Ce qui ne devrait que te fatiguer t’impose l’Épuisement.',
        'image' => $icon('infatigable.png'),
    ],
    [
        'name' => 'Insaisissable',
        'description' => 'Tu as un avantage pour échapper à une prise. Tu as un désavantage aux autres jets de lutte.',
        'image' => $icon('insaisissable.png'),
    ],
    [
        'name' => 'Lent',
        'description' => 'Tu gagnes 1 PV de plus à chaque niveau. Tes PM de base sont divisés par 2.',
        'image' => $icon('lent.png'),
    ],
    [
        'name' => 'Maigre',
        'description' => 'Tu as un avantage aux jets d’Acrobaties pour te faufiler. Tu as un désavantage aux jets de Force contre une poussée ou un renversement.',
        'image' => $icon('maigre.png'),
    ],
    [
        'name' => 'Malhonnête',
        'description' => 'Tu as un avantage aux jets de Supercherie. Tu as un désavantage aux jets de Persuasion.',
        'image' => $icon('malhonnete.png'),
    ],
    [
        'name' => 'Méfiant',
        'description' => 'Tu as un avantage aux jets de Perspicacité. Tu as un désavantage aux jets de Persuasion et d’Intimidation.',
        'image' => $icon('mefiant.png'),
    ],
    [
        'name' => 'Musculeux',
        'description' => 'Tu as un avantage aux jets de Force et aux compétences de Force. Tu as un désavantage aux jets d’Agilité et aux compétences d’Agilité.',
        'image' => $icon('musculeux.png'),
    ],
    [
        'name' => 'Myope',
        'description' => 'Tu as un avantage aux jets d’Investigation de près. Tu as un désavantage aux jets de Perception à distance.',
        'image' => $icon('myope.png'),
    ],
    [
        'name' => 'Nyctalope',
        'description' => 'Tu vois 2 cases plus loin dans le noir. Tu as un désavantage aux jets de Perception en pleine lumière. Il faut déjà voir dans le noir.',
        'image' => $icon('nyctalope.png'),
    ],
    [
        'name' => 'Passionné',
        'description' => 'Tu as un avantage aux jets de sauvegarde de Vitalité. Tu as un désavantage aux jets de sauvegarde de Sagesse.',
        'image' => $icon('passionne.png'),
    ],
    [
        'name' => 'Poli',
        'description' => 'Tu as un avantage aux jets de Persuasion. Tu as un désavantage aux jets d’Intimidation.',
        'image' => $icon('poli.png'),
    ],
    [
        'name' => 'Prudent',
        'description' => 'Sans effet si tu es immunisé·e à la terreur. Sinon, +1 CA quand tu n’attaques pas dans ton tour, et un désavantage aux sauvegardes contre la terreur.',
        'image' => $icon('prudent.png'),
    ],
    [
        'name' => 'Sauvage',
        'description' => 'Tu as un avantage aux jets de Dressage. Tu as un désavantage aux jets de Supercherie et de Persuasion.',
        'image' => $icon('sauvage.png'),
    ],
    [
        'name' => 'Spécialisé',
        'description' => 'Tu choisis Arcanes, Histoire, Nature ou Religion : avantage à celle-ci, désavantage aux trois autres.',
        'image' => $icon('specialise.png'),
    ],
    [
        'name' => 'Rapide - novice',
        'description' => 'Tes PM de base augmentent de 1.',
        'image' => $icon('rapide.png'),
    ],
    [
        'name' => 'Rapide - expérimenté',
        'description' => 'Tes PM de base augmentent de 2. Tu enlèves 1 PV gagné à chaque niveau, y compris au niveau 1 (0 possible).',
        'image' => $icon('rapide.png'),
    ],
    [
        'name' => 'Rapide - maître',
        'description' => 'Tes PM de base augmentent de 3. Tu enlèves 1 PV gagné à chaque niveau, y compris au niveau 1 (0 possible).',
        'image' => $icon('rapide.png'),
    ],
    [
        'name' => 'Vigoureux',
        'description' => 'Tu as un avantage aux jets de sauvegarde de Vitalité. Tu as un désavantage aux jets de sauvegarde d’Agilité.',
        'image' => $icon('vigoureux.png'),
    ],
    [
        'name' => 'Pisteur',
        'description' => 'Tu as un avantage aux jets de Perception pour suivre une piste. Tu ne te perds pas en pleine nature.',
        'image' => $icon('pisteur.png'),
    ],
    [
        'name' => 'Polyglotte',
        'description' => 'Tu connais deux langues de plus.',
        'image' => $icon('polyglotte.png'),
    ],
    [
        'name' => 'Cuisinier - novice',
        'description' => 'Pendant un repos court ou long, tu prépares un repas. Une créature récupère 1d4 PV. 1 fois entre deux repos longs.',
        'image' => $icon('cuisinier.png'),
    ],
    [
        'name' => 'Cuisinier - expérimenté',
        'description' => 'Pendant un repos court ou long, tu prépares un repas. Chaque allié·e présent·e récupère 1d4 PV. 1 fois entre deux repos longs.',
        'image' => $icon('cuisinier.png'),
    ],
    [
        'name' => 'Guérisseur - novice',
        'description' => 'Hors combat, en 10 minutes : tu soignes 1d4 PV ou tu stabilises. 1 fois par créature, entre deux repos courts ou longs.',
        'image' => $icon('guerisseur.png'),
    ],
    [
        'name' => 'Guérisseur - expérimenté',
        'description' => 'Hors combat, en 10 minutes : tu soignes 1d6 PV ou tu stabilises. 1 fois par créature, entre deux repos courts ou longs.',
        'image' => $icon('guerisseur.png'),
    ],
    [
        'name' => 'Guérisseur - maître',
        'description' => 'Hors combat, en 10 minutes : tu soignes 1d8 PV ou tu stabilises. 1 fois par créature, entre deux repos courts ou longs.',
        'image' => $icon('guerisseur.png'),
    ],
    [
        'name' => 'Furtif',
        'description' => 'Tu as un avantage en Discrétion dans la pénombre. Tu as un désavantage en pleine lumière.',
        'image' => $icon('furtif.png'),
    ],
    [
        'name' => 'Athlète',
        'description' => 'Tu as un avantage aux jets d’Athlétisme pour grimper ou sauter. Te relever ne te coûte pas de PM.',
        'image' => $icon('athlete.png'),
    ],
    [
        'name' => 'Initié',
        'description' => '1 fois entre deux repos courts ou longs, hors combat : une petite lumière, un message court, ou tu ouvres une serrure simple.',
        'image' => $icon('initie.png'),
    ],
    [
        'name' => 'Souple',
        'description' => 'Tu as un avantage pour échapper à une prise et à tes jets de fuite. On peut encore te tacler.',
        'image' => $icon('souple.png'),
    ],
    [
        'name' => 'Résistance au poison - novice',
        'description' => 'Tu as un avantage aux sauvegardes contre le poison.',
        'image' => $icon('resistance-au-poison.png'),
    ],
    [
        'name' => 'Résistance au poison - expérimenté',
        'description' => 'Tu as un avantage aux sauvegardes contre le poison. Les dégâts de poison que tu subis sont divisés par 2.',
        'image' => $icon('resistance-au-poison.png'),
    ],
    [
        'name' => 'Sentinelle - novice',
        'description' => 'Quand tu tacles, la cible perd aussi 1 PM.',
        'image' => $icon('sentinelle.png'),
    ],
    [
        'name' => 'Sentinelle - expérimenté',
        'description' => 'Quand tu tacles, la cible perd aussi 2 PM.',
        'image' => $icon('sentinelle.png'),
    ],
    [
        'name' => 'Second souffle - novice',
        'description' => '1 fois entre deux repos courts ou longs, pour 2 PA : tu récupères 1d6 PV.',
        'image' => $icon('second-souffle.png'),
    ],
    [
        'name' => 'Second souffle - expérimenté',
        'description' => '1 fois entre deux repos courts ou longs, pour 2 PA : tu récupères 2d6 PV.',
        'image' => $icon('second-souffle.png'),
    ],
    [
        'name' => 'Second souffle - maître',
        'description' => '1 fois entre deux repos courts ou longs, pour 2 PA : tu récupères 3d6 PV.',
        'image' => $icon('second-souffle.png'),
    ],
    [
        'name' => 'Charge - novice',
        'description' => 'Si tu avances d’au moins 3 cases en ligne droite avant une attaque au contact, tu ajoutes 1d4 aux dégâts.',
        'image' => $icon('charge.png'),
    ],
    [
        'name' => 'Charge - expérimenté',
        'description' => 'Si tu avances d’au moins 3 cases en ligne droite avant une attaque au contact, tu ajoutes 1d6 aux dégâts.',
        'image' => $icon('charge.png'),
    ],
    [
        'name' => 'Charge - maître',
        'description' => 'Si tu avances d’au moins 3 cases en ligne droite avant une attaque au contact, tu ajoutes 1d8 aux dégâts.',
        'image' => $icon('charge.png'),
    ],
    [
        'name' => 'Meneur - novice',
        'description' => '1 fois entre deux repos courts ou longs, un discours : tes alliés à 2 cases ou moins gagnent 1d4 PV temporaires.',
        'image' => $icon('meneur.png'),
    ],
    [
        'name' => 'Meneur - expérimenté',
        'description' => '1 fois entre deux repos courts ou longs, un discours : tes alliés à 2 cases ou moins gagnent 1d6 PV temporaires.',
        'image' => $icon('meneur.png'),
    ],
    [
        'name' => 'Meneur - maître',
        'description' => '1 fois entre deux repos courts ou longs, un discours : tes alliés à 2 cases ou moins gagnent 1d8 PV temporaires.',
        'image' => $icon('meneur.png'),
    ],
    [
        'name' => 'Endurci',
        'description' => 'Tu choisis Force, Agilité ou Sagesse. Tu as un avantage aux sauvegardes de cette caractéristique.',
        'image' => $icon('endurci.png'),
    ],
    [
        'name' => 'Brise-sort - novice',
        'description' => 'Si une créature lance un sort à 1 case de toi, tu peux réagir : son jet d’attaque de ce sort a un désavantage. 1 fois entre deux repos courts ou longs.',
        'image' => $icon('brise-sort.png'),
    ],
    [
        'name' => 'Brise-sort - expérimenté',
        'description' => 'Si une créature lance un sort à 1 case de toi, tu peux réagir : son jet d’attaque de ce sort a un désavantage. 1 fois par tour.',
        'image' => $icon('brise-sort.png'),
    ],
    [
        'name' => 'Dur à cuire - novice',
        'description' => 'Tu gagnes 1 PV de plus à chaque niveau, y compris au niveau 1.',
        'image' => $icon('dur-a-cuire.png'),
    ],
    [
        'name' => 'Dur à cuire - expérimenté',
        'description' => 'Tu gagnes 2 PV de plus à chaque niveau, y compris au niveau 1.',
        'image' => $icon('dur-a-cuire.png'),
    ],
    [
        'name' => 'Dur à cuire - maître',
        'description' => 'Tu gagnes 3 PV de plus à chaque niveau, y compris au niveau 1.',
        'image' => $icon('dur-a-cuire.png'),
    ],
    [
        'name' => 'Adepte élémentaire - novice',
        'description' => 'Tu choisis Feu, Eau, Air ou Terre. Tu ajoutes 1 aux dégâts de cet élément.',
        'image' => $icon('adepte-elementaire.png'),
    ],
    [
        'name' => 'Adepte élémentaire - expérimenté',
        'description' => 'Tu choisis Feu, Eau, Air ou Terre. Tu ajoutes 1 aux dégâts de cet élément, et ces dégâts ignorent l’état Résistant. Invulnérable s’applique toujours.',
        'image' => $icon('adepte-elementaire.png'),
    ],
    [
        'name' => 'Inébranlable - novice',
        'description' => 'Tu as un avantage aux sauvegardes contre le charme et la terreur.',
        'image' => $icon('inebranlable.png'),
    ],
    [
        'name' => 'Inébranlable - expérimenté',
        'description' => 'Tu as un avantage aux sauvegardes contre le charme et la terreur. Une créature de niveau inférieur au tien ne peut ni te charmer ni te terroriser.',
        'image' => $icon('inebranlable.png'),
    ],
];

/**
 * Puissance et accès PJ / PNJ. Plus de fiches en bas de l’échelle.
 *
 * @var array<string, array{0: int, 1: bool}>
 */
$power = [
    'Malade' => [1, false],
    'Poli' => [1, true],
    'Honnête' => [1, true],
    'Malhonnête' => [1, true],
    'Dur d\'oreille' => [1, true],
    'Myope' => [1, true],
    'Hypermétrope' => [1, true],
    'Amorphe' => [1, true],
    'Spécialisé' => [1, true],
    'Absorbé' => [1, true],
    'Sauvage' => [1, true],
    'Cavalier-né' => [1, true],
    'Appliqué' => [1, true],
    'Méfiant' => [1, true],
    'Engageant' => [1, true],
    'Abrupt' => [1, true],
    'Polyglotte' => [1, true],
    'Cuisinier - novice' => [1, true],
    'Pisteur' => [2, true],
    'Initié' => [2, true],
    'Dur à cuire - novice' => [2, true],
    'Furtif' => [3, true],
    'Petite taille' => [3, false],
    'Courageux' => [4, true],
    'Passionné' => [4, true],
    'Vigoureux' => [4, true],
    'Désinvolte' => [4, true],
    'Corpulent' => [4, true],
    'Maigre' => [4, true],
    'Insaisissable' => [4, true],
    'Infatigable' => [4, true],
    'Nyctalope' => [4, true],
    'Athlète' => [4, true],
    'Guérisseur - novice' => [4, true],
    'Souple' => [5, true],
    'Grande taille' => [5, false],
    'Agressif' => [5, true],
    'Musculeux' => [5, true],
    'Résistance au poison - novice' => [5, true],
    'Second souffle - novice' => [5, true],
    'Charge - novice' => [5, true],
    'Brutal' => [6, true],
    'Bagarreur' => [6, true],
    'Prudent' => [6, true],
    'Lent' => [6, true],
    'Sentinelle - novice' => [6, true],
    'Meneur - novice' => [6, true],
    'Rapide - novice' => [6, true],
    'Cuisinier - expérimenté' => [6, true],
    'Métaboliseur rapide' => [6, false],
    'Brise-sort - novice' => [6, true],
    'Inébranlable - novice' => [6, true],
    'Adepte élémentaire - novice' => [6, true],
    'Lourd' => [7, false],
    'Guérisseur - expérimenté' => [7, true],
    'Dur à cuire - expérimenté' => [7, true],
    'Endurci' => [8, true],
    'Résistance au poison - expérimenté' => [8, true],
    'Second souffle - expérimenté' => [8, true],
    'Charge - expérimenté' => [8, true],
    'Sentinelle - expérimenté' => [9, true],
    'Meneur - expérimenté' => [9, true],
    'Insensible aux poisons' => [10, false],
    'Rapide - expérimenté' => [10, true],
    'Agile' => [11, false],
    'Adepte élémentaire - expérimenté' => [11, true],
    'Guérisseur - maître' => [11, true],
    'Brise-sort - expérimenté' => [11, true],
    'Charge - maître' => [12, true],
    'Gigantesque' => [13, false],
    'Inébranlable - expérimenté' => [13, true],
    'Second souffle - maître' => [13, true],
    'Vif / Vive' => [14, false],
    'Dur à cuire - maître' => [14, true],
    'Meneur - maître' => [14, true],
    'Rapide - maître' => [15, true],
];

return array_map(static function (array $trait) use ($power): array {
    $row = $power[$trait['name']] ?? null;
    if ($row === null) {
        throw new RuntimeException('Puissance manquante pour le trait '.$trait['name']);
    }
    $trait['level'] = $row[0];
    $trait['available_for_characters'] = $row[1];

    return $trait;
}, $traits);
