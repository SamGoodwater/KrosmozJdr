<?php

/**
 * Une icône par capacité : hexagone, fond transparent, couleur et symbole liés au nom.
 *
 * Écrit storage/app/public/images/icons/capabilities/{id}.webp
 * et renseigne capabilities.image (sans écraser une image hors de ce dossier).
 *
 * Usage : php scripts/build-capability-icons.php
 */

declare(strict_types=1);

use App\Models\Entity\Capability;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$outDir = dirname(__DIR__).'/storage/app/public/images/icons/capabilities';
if (! is_dir($outDir) && ! mkdir($outDir, 0775, true) && ! is_dir($outDir)) {
    fwrite(STDERR, "Dossier inaccessible : {$outDir}\n");
    exit(1);
}

/**
 * @return array{0: string, 1: string} symbole, couleur
 */
function capabilityIconChoice(string $name, int $id): array
{
    $folded = foldCapabilityName($name);
    foreach (capabilityIconRules() as [$pattern, $symbols, $color]) {
        if (preg_match($pattern, $folded) === 1) {
            $symbol = $symbols[0];
            $color = shiftHex($color, (($id % 7) - 3) * 10);

            return [$symbol, $color];
        }
    }

    $pool = ['spark', 'star', 'rune', 'eye', 'hand', 'compass', 'book', 'shield'];
    $symbol = $pool[$id % count($pool)];
    $hue = ($id * 47) % 360;

    return [$symbol, hslToHex($hue, 0.62, 0.42)];
}

function foldCapabilityName(string $name): string
{
    $name = mb_strtolower($name, 'UTF-8');
    $name = str_replace(['’', '‘', '·', 'œ', 'æ'], ["'", "'", ' ', 'oe', 'ae'], $name);
    $name = strtr($name, [
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'í' => 'i',
        'ô' => 'o', 'ö' => 'o', 'ó' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ]);

    return $name;
}

/**
 * Règles du plus spécifique au plus général.
 *
 * @return list<array{0: string, 1: list<string>, 2: string}>
 */
function capabilityIconRules(): array
{
    return [
        ['/glyphe|rune|echo runique|contraste|flux/', ['rune', 'spark'], '#6d28d9'],
        ['/piege|collet|assassin/', ['trap', 'claw'], '#9a3412'],
        ['/sablier|temps|retard|retour en arriere|arret du temps/', ['hourglass', 'clock'], '#3730a3'],
        ['/pelle|coup d.oeil|ma part|bas de laine/', ['shovel', 'coin'], '#a16207'],
        ['/troupeau|horde|meute|compagnon|familier|bete|bestial|anim|proie|patte|pistage|araignee/', ['paw', 'people'], '#3f6212'],
        ['/pandala|culbute|rattrapage/', ['fist', 'wave'], '#0e7490'],
        ['/meche|petard|feu d.artifice|flamme|brule|feu de camp/', ['flame', 'bomb'], '#c2410c'],
        ['/masque|mascarade|deguisement|apparence|costume|visage|double de scene|double fond/', ['mask', 'mirror'], '#7e22ce'],
        ['/tourelle|tour |tourelle|rempart|phalange|garde|seuil|cordon/', ['tower', 'shield'], '#1e40af'],
        ['/faille|fracture|fureur|choc|charge|estoc|epee|arme|bourreau|tir parfait|fleche|viser|pointe/', ['sword', 'bolt'], '#be123c'],
        ['/sang|douleur|transe|bless/', ['drop', 'heart'], '#9f1239'],
        ['/jardin|foret|ecorce|plante|baie|racine|mousse|canop|bois|feuille|seve|saison|nature|sylve/', ['tree', 'leaf'], '#3f6212'],
        ['/soin|guerison|panacee|restauration|revigor|medicine|apothicaire|antidote|eau benite|priere|sacrement|benediction|beni|consolation|absolution|veillee|relique|autel|temple|pelerin|intercession/', ['plus', 'heart', 'lantern'], '#0f766e'],
        ['/lumiere|lueur|soleil|aube/', ['sun', 'lantern'], '#b45309'],
        ['/tenebre|ombre|nuit|discret|disparition|furtif|silence|non-detection|invisible|voir l.invisible/', ['moon', 'eye'], '#334155'],
        ['/illusion|image |reve|sommeil|fou rire|suggestion|memoire|divination|augure|scrutation|catalepsie|mauvais oeil|mauvais presage/', ['mask', 'eye', 'moon'], '#7e22ce'],
        ['/langage|langue|mot |parole|cri |message|telepath|rumeur|salut|titre|salon|cour |prince|faveur|invitation/', ['speech', 'crown'], '#0369a1'],
        ['/clef|cle\b|serrure|crochetage|verrou|coffre|cache|planque|vol a la tire|receleur|identite volee|alibi|fausse cle/', ['key', 'lock'], '#57534e'],
        ['/eau|houle|marin|navire|pont|cale|quille|grappin|noeud|epave|vague|nage|respiration aquatique|maree|quais/', ['anchor', 'wave'], '#0369a1'],
        ['/pierre|roche|terre|excav|faconnage|fusion dans la pierre/', ['mountain', 'hammer'], '#57534e'],
        ['/or |kama|commerce|contrat|offre|commission|caravane|courtier|balance|arrhes|echantillon|caution|entrepot|comptoir|associe|cote du jour/', ['coin', 'scales'], '#a16207'],
        ['/atelier|artisan|reparation|recette|etabli|marteau|molette|prototype|oeuvre|geste/', ['hammer', 'gear'], '#c2410c'],
        ['/carte|piste|trace|sentier|boussole|orientation|cartographe|fil d.ariane|guide|traversee|refuge|bivouac|abri|camp/', ['compass', 'pin', 'tent'], '#0f766e'],
        ['/scene|theatre|spectacle|rideau|public|applaud|refrain|musique|note|danse|harmonica|harmonique|comedien|performance|apotheose|rire|salle|affiche|entracte|cascade|jonglage|coulisse|premiere|replique|texte illusoire|tromperie|metamorph|changement de forme|clone|hallucin/', ['mask', 'note'], '#9d174d'],
        ['/portail|teleport|zaap|saut|aile|vol |levitation|liberte de mouvement|passage/', ['portal', 'wings', 'arrow'], '#1d4ed8'],
        ['/bouclier|protection|resistance|sanctuaire|cage|prison|entrave|menotte|interdi|sceau/', ['shield', 'lock'], '#1e3a8a'],
        ['/mort|resurrection|ame|esprit|possession|revenant/', ['skull', 'spark'], '#4c1d95'],
        ['/poison|venin|maladie/', ['potion', 'drop'], '#3f6212'],
        ['/nourriture|pain|festin|baie|ration|eau claire|gourde/', ['bread', 'drop'], '#b45309'],
        ['/feu |flamme|brul/', ['flame'], '#c2410c'],
        ['/oeil|yeux|vision|voir |clairvoyance|flair|detection|alarme|vigil/', ['eye', 'bell'], '#0369a1'],
        ['/main |doigt|paume/', ['hand'], '#0e7490'],
        ['/livre|lecture|erudit|decrypt|analyse|identification|savoir|formule/', ['book', 'quill'], '#1e40af'],
        ['/commande|ralli|banniere|ordre |discipline|ronde|milice|tactic/', ['flag', 'people'], '#115e59'],
        ['/monture|caval|cheval/', ['boot', 'paw'], '#92400e'],
        ['/climat|vent|orage/', ['wave', 'bolt'], '#0284c7'],
        ['/souhait|legend|destin|contingence/', ['star', 'spark'], '#6d28d9'],
        ['/maison|manoir|hutte|demie-plan|demi-plan|poche/', ['house', 'portal'], '#7c2d12'],
        ['/corde|grappin|noeud|filet/', ['rope', 'anchor'], '#0e7490'],
        ['/sort|magie|mage|arcan|wakfu|thaumat|rituel|cercle magique|desenvout|dissipation|symbole|maitrise|telekines/', ['spark', 'rune', 'hand'], '#6d28d9'],
        ['/volont|inebranl|armure|acharnement|ardeur|frappe|souffle|tacler|prendre sur soi|camisole|tenir la ligne|derniere ligne|tiens bon|releve|solide/', ['fist', 'shield'], '#9f1239'],
        ['/sacrifice|genereux|apaisement|communion|assistance|\baide\b|epaule|a ta place|partagee/', ['heart', 'people', 'hand'], '#be123c'],
        ['/provoc|injonction|duel de mot|scandale|bon mot|bouche magique|bruit/', ['speech'], '#0369a1'],
        ['/athlete|mobile|sprinteur|repli|fuite|fuir|derobade|pas de cote|toit|gravite|recul/', ['boot', 'arrow', 'wings'], '#1d4ed8'],
        ['/pleine forme/', ['plus', 'heart'], '#0f766e'],
        ['/plume/', ['feather'], '#0284c7'],
        ['/aleatoire|reussite|truquee|tirage|deuxieme couleur/', ['dice', 'star'], '#6d28d9'],
        ['/malediction|envout/', ['skull'], '#4c1d95'],
        ['/econome|mesure juste|rien ne se perd/', ['coin', 'scales'], '#a16207'],
        ['/affut|veille|vigie|regard|cecite|surdite|localiser/', ['eye'], '#0369a1'],
        ['/aura|eveil|heros|lanterne/', ['sun', 'lantern'], '#b45309'],
        ['/explorat|donjon|chemin|labyrinthe|route secrete|territoire/', ['compass', 'pin'], '#0f766e'],
        ['/tireur/', ['bow'], '#be123c'],
        ['/castle|petrif|nid/', ['mountain', 'tower', 'house'], '#57534e'],
        ['/\bjuste|offrande|verite/', ['lantern', 'scales', 'sword'], '#0f766e'],
        ['/agrandir|retrecir/', ['arrow'], '#1d4ed8'],
        ['/dominer|passe-droit|souverain|antichambre|seigneur|monde des douze|service|presentation|place te doit/', ['crown'], '#a16207'],
        ['/encourag|meneur|insigne|formation|tactic/', ['flag'], '#115e59'],
        ['/recherche|carnet|toile lue|lire |cueillette/', ['book', 'leaf'], '#1e40af'],
        ['/contrefa|copie|quill/', ['quill'], '#1e40af'],
        ['/coup du siecle/', ['star'], '#6d28d9'],
        ['/touche/', ['hand'], '#0e7490'],
        ['/reparer|maitre|colle|ajustement/', ['hammer'], '#c2410c'],
        ['/abordage|barre|capitaine|moussaillon|greement|ecope/', ['anchor'], '#0369a1'],
        ['/souris|entree|ouvre/', ['door'], '#57534e'],
        ['/place a table/', ['bread'], '#b45309'],
        ['/signe partage/', ['rune'], '#6d28d9'],
        ['/soin|guerir|vie/', ['plus', 'heart'], '#0f766e'],
        ['/bouclier|garde/', ['shield'], '#1e40af'],
        ['/animal|bete/', ['paw'], '#3f6212'],
        ['/ombre|nuit/', ['moon'], '#334155'],
        ['/feu/', ['flame'], '#c2410c'],
        ['/eau/', ['drop'], '#0369a1'],
    ];
}

function glyphMarkup(string $key): string
{
    $body = match ($key) {
        'spark' => '<path d="M0-13 2.6-3.2 13-2.2 4 3.2 6.4 13 0 7.2-6.4 13-4 3.2-13-2.2-2.6-3.2Z"/>',
        'star' => '<path d="M0-12 3.2-3.6 12-3.2 5.2 2.2 7.4 11.2 0 6.4-7.4 11.2-5.2 2.2-12-3.2-3.2-3.6Z"/>',
        'bolt' => '<path d="M2-13-7 1h5l-3 12 11-15H4Z"/>',
        'plus' => '<path d="M-3.2-11h6.4v7.8H11v6.4H3.2V11h-6.4V3.2H-11v-6.4h7.8Z"/>',
        'heart' => '<path d="M0 10 C-8 3-12-1-8-6c2.4-2.6 5.4-1.2 8 1.6 2.6-2.8 5.6-4.2 8-1.6 4 5-8 9-8 16Z"/>',
        'shield' => '<path d="M0-12c6 2 10 3 12 4v7c0 7-6 11-12 14C-6 10-12 6-12-1V-8c2-1 6-2 12-4Z"/>',
        'eye' => '<ellipse cx="0" cy="0" rx="12" ry="7"/><circle cx="0" cy="0" r="3.2" fill="#1c1917" stroke="none"/>',
        'leaf' => '<path d="M0 12C-11 4-10-8 1-13 2 0 4 6 0 12Z"/><path d="M-1 8C2 2 6-4 10-10" fill="none"/>',
        'flame' => '<path d="M0 13c-7-2-9-8-6-13 1 3 2 4 2 4C-6-2-2-10 2-13c1 5 4 6 4 6 4-2 6 2 5 7 3 1 4 5 1 9-2 3-4 4-6 4Z"/>',
        'drop' => '<path d="M0-13C7-4 11 1 11 6a11 11 0 0 1-22 0c0-5 4-10 11-19Z"/>',
        'sword' => '<path d="M-1.3-13h2.6L2 1H-2Z"/><path d="M-8 1h16v3.4H-8Z"/><path d="M-1.4 4.4h2.8V12h-2.8Z"/>',
        'bow' => '<path d="M-4-12c9 4 9 20 0 24" fill="none" stroke-width="2.3"/><path d="M-4-12v24" fill="none" stroke-width="1.6"/><path d="M-4 0h11M4-3.2 11 0 4 3.2" fill="none" stroke-width="1.8"/>',
        'paw' => '<circle cx="-7" cy="-6" r="3"/><circle cx="-2" cy="-10" r="3"/><circle cx="4" cy="-10" r="3"/><circle cx="9" cy="-5" r="3"/><ellipse cx="0" cy="4" rx="8" ry="6"/>',
        'key' => '<circle cx="-6" cy="0" r="5.5"/><circle cx="-6" cy="0" r="2.1" fill="#1c1917" stroke="none"/><path d="M-1 0h14M8 0v5M11.5 0v3.4" fill="none" stroke-width="2.4"/>',
        'lock' => '<rect x="-7" y="-1" width="14" height="11" rx="2"/><path d="M-4-1V-6a4 4 0 0 1 8 0v5" fill="none" stroke-width="2.2"/>',
        'mask' => '<path fill-rule="evenodd" d="M-12-5c2-6 7-8 12-8s10 2 12 8c-1 8-5 14-12 14S-11 3-12-5ZM-7-1a2.3 2.3 0 1 0 4.6 0 2.3 2.3 0 1 0-4.6 0ZM2.4-1a2.3 2.3 0 1 0 4.6 0 2.3 2.3 0 1 0-4.6 0Z"/>',
        'book' => '<path d="M-11-10h8c2 0 3 2 3 4v16c-2-2-4-2-6-2h-5Z"/><path d="M11-10H3c-2 0-3 2-3 4v16c2-2 4-2 6-2h5Z"/>',
        'speech' => '<rect x="-12" y="-10" width="24" height="14" rx="3"/><path d="M-4 4-6 12 4 4Z"/>',
        'coin' => '<ellipse cx="0" cy="0" rx="11" ry="11"/><ellipse cx="0" cy="0" rx="5" ry="5" fill="none" stroke-width="1.8"/>',
        'hammer' => '<rect x="-11" y="-10" width="16" height="8" rx="1.5"/><rect x="-2" y="-4" width="4" height="16" rx="1"/>',
        'flag' => '<path d="M-8-13v26" fill="none" stroke-width="2.4"/><path d="M-8-13h16l-4 6 4 6H-8Z"/>',
        'sun' => '<circle cx="0" cy="0" r="5"/><path d="M0-12v4M0 8v4M-12 0h4M8 0h4M-8-8l3 3M5 5l3 3M-8 8l3-3M5-5l3-3" fill="none" stroke-width="2"/>',
        'moon' => '<path fill-rule="evenodd" d="M2-12a10 10 0 1 0 8 16 8 8 0 1 1-8-16Z"/>',
        'foot' => '<ellipse cx="0" cy="2" rx="6" ry="9"/><circle cx="-6" cy="-8" r="2.2"/><circle cx="-1" cy="-11" r="2.2"/><circle cx="4" cy="-9" r="2"/>',
        'skull' => '<circle cx="0" cy="-3" r="8"/><path d="M-5 4h10v4H-5Z"/><circle cx="-3" cy="-3" r="1.6" fill="#1c1917" stroke="none"/><circle cx="3" cy="-3" r="1.6" fill="#1c1917" stroke="none"/>',
        'crown' => '<path d="M-12 3-8-9-3 0 0-11 3 0 8-9 12 3Z"/><rect x="-12" y="3" width="24" height="4.5" rx="1"/>',
        'note' => '<ellipse cx="-3" cy="6" rx="5" ry="3.4"/><path d="M1 4V-12" fill="none" stroke-width="2"/><path d="M1-12h8v4H1" />',
        'pin' => '<path d="M0-12c5 0 8 4 8 8 0 6-8 14-8 14S-8 2-8-4c0-4 3-8 8-8Z"/><circle cx="0" cy="-4" r="2.4" fill="#1c1917" stroke="none"/>',
        'tent' => '<path d="M0-12 13 10H-13Z"/><path d="M0-2v12" fill="none" stroke-width="1.8"/>',
        'anchor' => '<circle cx="0" cy="-8" r="3"/><path d="M0-5v14M-8 4c0 6 4 8 8 8s8-2 8-8" fill="none" stroke-width="2.2"/><path d="M-6 2h12" fill="none" stroke-width="2.2"/>',
        'feather' => '<path d="M-2 12C2 4 8-6 10-12 4-8-2-2-4 6c-2 2-2 4 2 6Z"/><path d="M-2 8 6-6" fill="none"/>',
        'potion' => '<rect x="-3" y="-13" width="6" height="4" rx="1"/><path d="M-6-8h12l3 6c2 6 0 12-9 14-9-2-11-8-9-14Z"/>',
        'people' => '<circle cx="-5" cy="-6" r="3.2"/><circle cx="5" cy="-6" r="3.2"/><path d="M-12 8c1-5 3-7 7-7s6 2 7 7M-2 8c1-5 3-7 7-7"/>',
        'arrow' => '<path d="M0-12 10 2H5v10H-5V2h-5Z"/>',
        'hourglass' => '<path d="M-8-12h16l-8 12 8 12H-8l8-12Z"/>',
        'tree' => '<path d="M0-13 10-2H6L12 8H-12L-6-2h-4Z"/><rect x="-2" y="8" width="4" height="6"/>',
        'wave' => '<path d="M-13 2c3-5 6-5 9 0s6 5 9 0 6-5 8 0v6H-13Z"/>',
        'lantern' => '<rect x="-5" y="-6" width="10" height="14" rx="2"/><path d="M-3-10h6M0-13v3M-7 12h14" fill="none" stroke-width="2"/>',
        'scales' => '<path d="M0-12v18M-10-8h20" fill="none" stroke-width="2"/><path d="M-10-8-14 2h8Z"/><path d="M10-8 6 2h8Z"/>',
        'boot' => '<path d="M-6-12h8v9h6c1.6 0 3 1.6 3 3.5V8H-8V3h2Z"/>',
        'house' => '<path d="M0-12 12-2v14H-12V-2Z"/><rect x="-3" y="2" width="6" height="10" fill="#1c1917" stroke="none"/>',
        'compass' => '<circle cx="0" cy="0" r="11" fill="none" stroke-width="2"/><path d="M0-9 2.4-2.4 9 0 2.4 2.4 0 9-2.4 2.4-9 0-2.4-2.4Z"/>',
        'wings' => '<path d="M0 2C-4-8-12-10-13-4-8 0-4 2 0 4-4 6-8 10-6 12-2 8 0 4 0 2 4 6 8 10 6 12 2 8 0 4 0 2 4 2 8 0 13-4 12-10 4-8 0 2Z"/>',
        'dice' => '<rect x="-9" y="-9" width="18" height="18" rx="3"/><circle cx="-4" cy="-4" r="1.4" fill="#1c1917" stroke="none"/><circle cx="4" cy="4" r="1.4" fill="#1c1917" stroke="none"/>',
        'shovel' => '<path d="M-6 2h8l2 8H-8Z"/><path d="M-1-13h3v16h-3Z"/>',
        'bomb' => '<circle cx="0" cy="3" r="9"/><path d="M4-6 9-12" fill="none" stroke-width="2"/><circle cx="10" cy="-13" r="2"/>',
        'bell' => '<path d="M0-12v2c6 1 8 6 8 10H-8c0-4 2-9 8-10Z"/><rect x="-9" y="0" width="18" height="3" rx="1"/><circle cx="0" cy="7" r="2"/>',
        'rune' => '<path d="M0-12 10 0 0 12-10 0Z"/><path d="M0-5v10M-4 0h8" fill="none" stroke="#1c1917" stroke-width="1.6"/>',
        'clock' => '<circle cx="0" cy="0" r="11" fill="none" stroke-width="2.2"/><path d="M0-6v6h5" fill="none" stroke-width="2"/>',
        'claw' => '<path d="M-8 10-10-6l4 2 2-10 3 8 3-8 2 10 4-2-2 16Z"/>',
        'bread' => '<path d="M-11 2c0-6 5-10 11-10s11 4 11 10v6H-11Z"/><path d="M-6-2h4M1-4h5" fill="none" stroke="#1c1917" stroke-width="1.4"/>',
        'portal' => '<path d="M-10 12V-2c0-8 4-12 10-12s10 4 10 12v14" fill="none" stroke-width="2.4"/><path d="M-4 12V2c0-3 2-5 4-5s4 2 4 5v10" fill="none" stroke-width="2"/>',
        'fist' => '<path d="M-8 4c0-6 2-10 6-10 2 0 3 2 3 4V0c2-2 4-2 5 0v3c2-1 4 0 4 2v7c0 4-4 6-10 6s-8-3-8-8Z"/>',
        'rope' => '<path d="M-10-8c6 0 6 6 0 6s-6 6 0 6 6 6 0 6" fill="none" stroke-width="2.4"/>',
        'campfire' => '<path d="M-10 8h20" fill="none" stroke-width="2"/><path d="M0 8C-1-2-6-2-4-8c3 2 3 4 2 6 2-4 6-4 5 0 2 1 3 4 1 6"/>',
        'mountain' => '<path d="M-13 10  -2-10 4-2 8-8 13 10Z"/>',
        'net' => '<path d="M-10-10h20v20H-10Z" fill="none" stroke-width="1.8"/><path d="M-10-4h20M-10 4h20M-4-10v20M4-10v20" fill="none" stroke-width="1.3"/>',
        'hand' => '<path d="M-3 12V0M-3 2c-3 0-5-2-5-4s2-3 4-2M-3-1c-1-4 1-8 4-8s4 3 3 6M2-2c0-4 2-7 4-6s2 4 1 7M6 0c1-3 4-4 5-2s-1 5-2 7c-2 4-6 7-10 7"/>',
        'trap' => '<circle cx="0" cy="0" r="9" fill="none" stroke-width="2"/><path d="M0-9v18M-9 0h18" fill="none" stroke-width="1.6"/>',
        'quill' => '<path d="M-8 12 8-12" fill="none" stroke-width="2.2"/><path d="M4-12 10-8 2-6Z"/>',
        'mirror' => '<ellipse cx="0" cy="-1" rx="8" ry="10"/><path d="M-4 10h8" fill="none" stroke-width="2"/>',
        'chain' => '<rect x="-10" y="-4" width="8" height="8" rx="3" fill="none" stroke-width="2"/><rect x="2" y="-4" width="8" height="8" rx="3" fill="none" stroke-width="2"/>',
        'gear' => '<circle cx="0" cy="0" r="4"/><path d="M-2-12h4l1 4h-6ZM-2 8h4l1 4h-6ZM-12-2v4l4-1v-2ZM8-1l4-1v4l-4-1Z" />',
        'tower' => '<path d="M-8 12V-4L-4-10h8l4 6v16Z"/><rect x="-2" y="4" width="4" height="8" fill="#1c1917" stroke="none"/>',
        'door' => '<path d="M-8-12h16v24H-8Z"/><circle cx="4" cy="2" r="1.4" fill="#1c1917" stroke="none"/>',
        default => '<path d="M0-12 3-3 12-2 5 3 7 12 0 7-7 12-5 3-12-2-3-3Z"/>',
    };

    return '<g transform="translate(32 32)" fill="#fffaf3" stroke="#1c1917" stroke-width="1.15" stroke-linejoin="round" stroke-linecap="round">'.$body.'</g>';
}

function capabilityIconSvg(string $symbol, string $color): string
{
    $hex = '<path d="M18 9h28l11 23-11 23H18L7 32Z" fill="'.$color.'" stroke="#1c1917" stroke-width="2.6" stroke-linejoin="round"/>';

    return '<?xml version="1.0" encoding="UTF-8"?>'
        .'<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 64 64">'
        .$hex
        .glyphMarkup($symbol)
        .'</svg>';
}

function shiftHex(string $hex, int $degrees): string
{
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;
    $d = $max - $min;
    $h = 0.0;
    $s = 0.0;
    if ($d > 0.00001) {
        $s = $d / (1 - abs(2 * $l - 1));
        $h = match (true) {
            $max === $r => 60 * fmod((($g - $b) / $d), 6),
            $max === $g => 60 * ((($b - $r) / $d) + 2),
            default => 60 * ((($r - $g) / $d) + 4),
        };
    }
    $h = fmod($h + $degrees + 360, 360);

    return hslToHex($h, $s, $l);
}

function hslToHex(float $h, float $s, float $l): string
{
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;
    [$r, $g, $b] = match (true) {
        $h < 60 => [$c, $x, 0],
        $h < 120 => [$x, $c, 0],
        $h < 180 => [0, $c, $x],
        $h < 240 => [0, $x, $c],
        $h < 300 => [$x, 0, $c],
        default => [$c, 0, $x],
    };
    $to = static fn (float $v): string => str_pad(dechex((int) round(($v + $m) * 255)), 2, '0', STR_PAD_LEFT);

    return '#'.$to($r).$to($g).$to($b);
}

function writeCapabilityWebp(string $svg, string $path): void
{
    $image = new Imagick;
    $image->setBackgroundColor(new ImagickPixel('transparent'));
    $image->setResolution(256, 256);
    $image->readImageBlob($svg);
    $image->setImageFormat('webp');
    $image->setOption('webp:lossless', 'true');
    $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
    if ($image->getImageWidth() !== 128) {
        $image->resizeImage(128, 128, Imagick::FILTER_LANCZOS, 1);
    }
    $image->writeImage($path);
    $image->clear();
}

if (getenv('CAPABILITY_ICONS_SKIP') === '1') {
    return;
}

$kept = 0;
$written = 0;
$fallback = 0;
$fallbackNames = [];

foreach (Capability::query()->orderBy('id')->get(['id', 'name', 'image']) as $capability) {
    $id = (int) $capability->id;
    $name = (string) $capability->name;
    [$symbol, $color] = capabilityIconChoice($name, $id);
    $folded = foldCapabilityName($name);
    $matched = false;
    foreach (capabilityIconRules() as [$pattern]) {
        if (preg_match($pattern, $folded) === 1) {
            $matched = true;
            break;
        }
    }
    if (! $matched) {
        $fallback++;
        $fallbackNames[] = $name;
    }

    if (in_array('--list-fallback', $argv ?? [], true)) {
        continue;
    }

    $path = $outDir.'/'.$id.'.webp';
    writeCapabilityWebp(capabilityIconSvg($symbol, $color), $path);
    $written++;

    $url = '/storage/images/icons/capabilities/'.$id.'.webp';
    $current = trim((string) $capability->image);
    if ($current !== '' && ! str_contains($current, '/images/icons/capabilities/')) {
        $kept++;
        continue;
    }
    if ($current !== $url) {
        Capability::query()->whereKey($id)->update(['image' => $url]);
    }
}

echo "{$written} icônes, {$fallback} sans mot-clé, {$kept} images perso conservées.\n";
foreach ($fallbackNames as $label) {
    echo "  · {$label}\n";
}
