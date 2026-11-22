<?php

declare(strict_types=1);

/**
 * États jouables ajoutés au catalogue (D&D 2014 + états Dofus retenus).
 *
 * Les fiches déjà `playable` du même nom ne sont pas réécrites.
 * `aliases` sert seulement à reconnaître un jeton Brut déjà en base.
 *
 * @return list<array{name: string, description: string, image: string|null, aliases?: list<string>, flags?: list<string>}>
 */
$img = static fn (string $file): string => 'https://api.dofusdb.fr/img/states/'.$file;
$local = static fn (string $file): string => '/storage/images/icons/states/'.$file;

return [
    [
        'name' => 'À terre',
        'description' => "Tu es au sol. Tu ne peux que ramper. Te relever met fin à l’état.\nTes jets d’attaque ont un désavantage.\nUn jet d’attaque contre toi a un avantage si l’attaquant est à 1 case ou moins. Sinon, il a un désavantage.",
        'image' => $local('a-terre.png'),
    ],
    [
        'name' => 'Agrippé',
        'description' => "On te tient. Tes PM passent à 0, quels que soient tes bonus.\nL’état prend fin si la créature qui t’agrippe est incapable d’agir.\nIl prend aussi fin si un effet t’éloigne hors de sa portée.",
        'image' => $local('agrippe.png'),
    ],
    [
        'name' => 'Assourdi',
        'description' => 'Tu n’entends plus. Tu rates automatiquement tout jet de caractéristique qui a besoin de l’ouïe.',
        'image' => $local('assourdi.png'),
    ],
    [
        'name' => 'Charmé',
        'description' => "Tu ne peux pas attaquer le charmeur, ni le cibler avec un effet nuisible.\nLe charmeur a un avantage aux jets de caractéristique pour interagir avec toi.",
        'image' => $local('charme.png'),
    ],
    [
        'name' => 'Effrayé',
        'description' => "Tant que la source de ta peur est dans ta ligne de vue, tes jets de caractéristique et tes jets d’attaque ont un désavantage.\nTu ne peux pas t’en rapprocher de toi-même.",
        'image' => $local('effraye.png'),
    ],
    [
        'name' => 'Entravé',
        'description' => "Tes PM passent à 0, quels que soient tes bonus.\nLes jets d’attaque contre toi ont un avantage. Les tiens ont un désavantage.\nTes jets de sauvegarde d’Agilité ont un désavantage.",
        'image' => $local('entrave.png'),
    ],
    [
        'name' => 'Épuisement',
        'description' => "L’épuisement a six niveaux. Tu subis le niveau actuel et tous ceux d’en dessous.\n1 — désavantage aux jets de caractéristique.\n2 — PM réduits de moitié.\n3 — désavantage aux jets d’attaque et de sauvegarde.\n4 — points de vie maximum réduits de moitié.\n5 — PM à 0.\n6 — mort.\nUn repos long, si tu as mangé et bu, retire 1 niveau. Revenir à la vie retire aussi 1 niveau.",
        'image' => $local('epuisement.png'),
    ],
    [
        'name' => 'Incapable d\'agir',
        'description' => 'Tu ne fais plus ni action ni réaction. Tes PA passent à 0, quels que soient tes bonus.',
        'image' => $local('incapable-d-agir.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Inconscient',
        'description' => "Tu es incapable d’agir : PA = 0, quels que soient tes bonus. Tu ne bouges plus (PM = 0), tu ne parles plus, et tu ne perçois plus ce qui t’entoure.\nTu lâches ce que tu tenais et tu tombes à terre.\nTu rates automatiquement tes jets de sauvegarde de Force et d’Agilité.\nLes jets d’attaque contre toi ont un avantage.\nToute attaque qui te touche est un coup critique si l’attaquant est à 1 case ou moins.",
        'image' => $local('inconscient.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Invisible',
        'description' => "On ne te voit pas, sauf avec de la magie ou un sens particulier. Pour te cacher, tu comptes comme dans une zone sans visibilité. Un bruit ou une trace peut révéler ta case.\nLes jets d’attaque contre toi ont un désavantage. Les tiens ont un avantage.",
        'image' => $local('invisible.png'),
    ],
    [
        'name' => 'Paralysé',
        'description' => "Tu es incapable d’agir : PA = 0, quels que soient tes bonus. Tu ne bouges plus (PM = 0) et tu ne parles plus.\nTu rates automatiquement tes jets de sauvegarde de Force et d’Agilité.\nLes jets d’attaque contre toi ont un avantage.\nToute attaque qui te touche est un coup critique si l’attaquant est à 1 case ou moins.",
        'image' => $local('paralyse.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Pétrifié',
        'description' => "Toi et les objets non magiques que tu portes devenez une matière inerte, souvent la pierre. Ton poids est multiplié par dix et tu ne vieillis plus.\nTu es incapable d’agir (PA = 0). Tu ne bouges plus (PM = 0), tu ne parles plus, et tu ne perçois plus ce qui t’entoure.\nLes jets d’attaque contre toi ont un avantage.\nTu rates automatiquement tes jets de sauvegarde de Force et d’Agilité.\nTu as la résistance à tous les dégâts.\nLe poison et la maladie ne t’affectent plus. Un poison ou une maladie déjà là est suspendu, pas annulé.",
        'image' => $local('petrifie.png'),
        'flags' => ['prevents_spell_cast'],
    ],
    [
        'name' => 'Altruiste',
        'description' => 'Les soins destinés à tes alliés te sont redirigés. Tes alliés ne les reçoivent pas.',
        'image' => $local('altruiste.png'),
    ],
    [
        'name' => 'Enraciné',
        'description' => 'On ne peut pas te déplacer : ni t’attirer, ni te repousser, ni échanger ta place. Tes PM restent utilisables.',
        'image' => $img('stateRooted.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed', 'cant_switch_position'],
    ],
    [
        'name' => 'Indéplaçable',
        'description' => 'Aucun effet ne change ta position : ni attraction, ni poussée, ni téléportation, ni saut.',
        'image' => $img('stateUnshift.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed', 'cant_switch_position'],
    ],
    [
        'name' => 'Insoignable',
        'description' => 'Tu ne peux pas être soigné·e.',
        'image' => $img('stateInsoignable.png'),
        'flags' => ['incurable'],
    ],
    [
        'name' => 'Invulnérable',
        'description' => 'Tu ne subis aucun dégât.',
        'image' => $img('stateInvulnerable.png'),
        'flags' => ['invulnerable'],
    ],
    [
        'name' => 'Invulnérable à distance',
        'description' => 'Tu ne subis aucun dégât des attaques et des sorts à distance.',
        'image' => $img('stateInvulnerableDistance.png'),
        'flags' => ['invulnerable_range'],
    ],
    [
        'name' => 'Invulnérable au càc',
        'description' => 'Tu ne subis aucun dégât des attaques et des sorts au corps à corps.',
        'image' => $img('stateInvulnerableMelee.png'),
        'aliases' => ['Invulnérable en Mêlée'],
        'flags' => ['invulnerable_melee'],
    ],
    [
        'name' => 'Invulnérable à l\'élément Terre',
        'description' => 'Tu ne subis aucun dégât de Terre.',
        'image' => $img('stateEarth.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Feu',
        'description' => 'Tu ne subis aucun dégât de Feu.',
        'image' => $img('stateFire.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Air',
        'description' => 'Tu ne subis aucun dégât d’Air.',
        'image' => $img('stateAir.png'),
    ],
    [
        'name' => 'Invulnérable à l\'élément Eau',
        'description' => 'Tu ne subis aucun dégât d’Eau.',
        'image' => $img('stateWater.png'),
    ],
    [
        'name' => 'Lourd',
        'description' => 'On ne peut pas te déplacer : ni t’attirer, ni te repousser.',
        'image' => $img('stateImmovable.png'),
        'flags' => ['cant_be_moved', 'cant_be_pushed'],
    ],
    [
        'name' => 'Pacifiste',
        'description' => 'Tu ne peux pas infliger de dégâts.',
        'image' => $img('statePacifist.png'),
        'flags' => ['cant_deal_damage'],
    ],
    [
        'name' => 'Pacifiste à distance',
        'description' => 'Tu ne peux pas infliger de dégâts à distance.',
        'image' => $local('pacifiste-distance.png'),
    ],
    [
        'name' => 'Pacifiste au càc',
        'description' => 'Tu ne peux pas infliger de dégâts au corps à corps.',
        'image' => $local('pacifiste-cac.png'),
    ],
    [
        'name' => 'Saoul',
        'description' => 'Tes jets de sauvegarde, tes jets de compétence et tes jets d’attaque à distance ont un désavantage.',
        'image' => $local('saoul.png'),
    ],
    [
        'name' => 'Intaclable',
        'description' => 'On ne peut pas te tacler. Tu réussis automatiquement tes jets de fuite.',
        'image' => $img('stateIntaclable.png'),
        'aliases' => ['Intacable'],
        'flags' => ['cant_be_tackled'],
    ],
    [
        'name' => 'Intacleur',
        'description' => 'Tu ne peux pas tacler.',
        'image' => $img('stateIntacleur.png'),
        'flags' => ['cant_tackle'],
    ],
    [
        'name' => 'Terrorisé',
        'description' => "Tant que la source de ta frayeur est dans ta ligne de vue, tes jets de caractéristique et tes jets d’attaque ont un désavantage.\nTu ne peux pas t’en rapprocher de ton plein gré.",
        'image' => $local('terrorise.png'),
    ],
    [
        'name' => 'Résistant',
        'description' => 'Les dégâts que tu subis sont divisés par 2.',
        'image' => $local('resistant.png'),
    ],
    [
        'name' => 'Résistant à distance',
        'description' => 'Les dégâts à distance que tu subis sont divisés par 2.',
        'image' => $local('resistant-distance.png'),
    ],
    [
        'name' => 'Résistant au càc',
        'description' => 'Les dégâts au corps à corps que tu subis sont divisés par 2.',
        'image' => $local('resistant-cac.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Terre',
        'description' => 'Les dégâts de Terre que tu subis sont divisés par 2.',
        'image' => $local('resistant-terre.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Feu',
        'description' => 'Les dégâts de Feu que tu subis sont divisés par 2.',
        'image' => $local('resistant-feu.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Air',
        'description' => 'Les dégâts d’Air que tu subis sont divisés par 2.',
        'image' => $local('resistant-air.png'),
    ],
    [
        'name' => 'Résistant à l\'élément Eau',
        'description' => 'Les dégâts d’Eau que tu subis sont divisés par 2.',
        'image' => $local('resistant-eau.png'),
    ],
];
