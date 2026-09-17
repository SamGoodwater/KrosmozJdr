<?php

declare(strict_types=1);

namespace Database\Seeders\Entity;

use App\Models\Entity\Language;
use Illuminate\Database\Seeder;

/**
 * Référentiel des langues « de base » pour le JDR (ton Monde des Douze / Dofus).
 *
 * Idempotent : {@see Language::updateOrCreate} par nom.
 */
class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            [
                'name' => 'Commun',
                'color' => '#64748b',
                'description' => <<<'TXT'
L’idiome des routes, des marchés et des zaaps : on l’entend de la place d’Astrub jusqu’aux contrées les plus paumées. Suffisant pour marchander, recruter un groupe, jurister trois secondes ou traiter quelqu’un de « sac de bouse » sans passer pour un Iop fini. La base quoi — ni classe, ni panache, mais tout le monde la comprend.
TXT,
            ],
            [
                'name' => 'Brakmarien',
                'color' => '#991b1b',
                'description' => <<<'TXT'
Teinte rouge brique et étron séché : le parler des faubourgs de Brâkmar et des routes du Sidimote, où l’on compare volontiers ton visage à une crotte de Dragodinde. Juron à la Rushu, métaphores brûlantes, compliments qui sonnent comme des menaces — parfait pour négocier une dette, une alliance ou une bagarre sans passer par le tribunal de Bonta.
TXT,
            ],
            [
                'name' => 'Bontarien',
                'color' => '#2563eb',
                'description' => <<<'TXT'
Accent policé des artères de Bonta et des plaines de Cania côté « civilisation ». Phrases longues, registre noble même pour commander une baguette, sous-texte de devoir et d’ordre — les autres villes jurent que ça pue le parchemin et le réglement de gardien.
TXT,
            ],
            [
                'name' => 'Argot des brigandins',
                'color' => '#581c87',
                'description' => <<<'TXT'
Le parler des bandes de route et des tavernes louches : celui des brigandins et des « entrepreneurs » qui réalisent des transferts de kamas sans mandat de la milice. Mots courts, sens dédoublés, tout ce qu’il faut pour ne pas nommer le butin, repérer un pigeon ou conclure une embuscade entre camarades de extraction sociale douteuse.
TXT,
            ],
            [
                'name' => 'Eliotropien',
                'color' => '#6366f1',
                'description' => <<<'TXT'
Idiome des disciples du Wakfu et du temple : on y parle cieux, dieux et entailles dans la réalité comme si on ouvrait une fenêtre sur l’au-delà. Formules hiérarchisées, métaphores de lumière et d’écho, gros sous-entendus quand on prie tout bas dans une nef — ou qu’on signe un portail en raclant les dalles du sanctuaire. À réserver aux lieux sacrés et aux entêtés qui prétendent tenir le fil du divin entre les mains.
TXT,
            ],
            [
                'name' => 'Frigostien',
                'color' => '#0284c7',
                'description' => <<<'TXT'
Prononciation lente et bouche mi-closed du continent gelé : Frigost et ses sujets qui ont oublié qu’on pouvait sourire sans risquer un éclat de givre. Métaphores de blizzard, compliments qui sonnent comme une alerte d'hypothermie — idéal pour glisser une menace polie ou commander une chopine sans lever le ton.
TXT,
            ],
            [
                'name' => 'Pandala',
                'color' => '#15803d',
                'description' => <<<'TXT'
L’idiome des îles en bambou : l’île de Pandala et l’île de Grobe, entre brasseries flottantes et senteurs de fleurs qui masquent mal l’alcool. Proverbes à boire, humour zen qui dérape, et onomatopées de Pandawa tombée du tabouret — une langue aussi équilibrée qu’un sac plein de kamas sur une corde raide.
TXT,
            ],
            [
                'name' => 'Amaknien',
                'color' => '#c2410c',
                'description' => <<<'TXT'
L’accent du cœur du royaume : Amakna, Astrub et la côte jusqu’à Sufokia. Mélange de marchands en goguette, pêcheurs qui jurent comme des dockers et paysans convaincus que leur troupeau vaut mieux que ton équipement — une langue de passage, terreuse et conviviale, qui sent la paille et la marée.
TXT,
            ],
            [
                'name' => 'Sylvestre',
                'color' => '#166534',
                'description' => <<<'TXT'
Chuchotements et formules tournées autour de l’île de Moon : forêts mystiques, esprits chipés et rituel du « on ne réveille pas ce qui grince dans les bois ». Syllabes longues, images de lune et de mousse, parfait pour parler aux feuilles sans passer pour un Enutrof dans les orties.
TXT,
            ],
            [
                'name' => 'Bworkien',
                'color' => '#9a3412',
                'description' => <<<'TXT'
La langue officielle (non écrite) des Bworks : consonnes qui cognent, phrases taillées à la hache, et une tolérance zéro pour les subjonctifs. On peut tout dire en trois syllabes ; les nuances se règlent au coup de massue. Les autres races prétendent que ça ne compte pas comme du dialogue — tant pis pour elles.
TXT,
            ],
            [
                'name' => 'Wabbit',
                'color' => '#db2777',
                'description' => <<<'TXT'
Le dialecte carrotique de l’île de la Cawotte : suffixes en « -wap », insultes mignonnes et menaces qui sonnent comme des invitations au goûter. Entre lapins armés et humour décapant, c’est la langue idéale pour marchander une récolte ou fuir un chef-lieu sans perdre la face.
TXT,
            ],
            [
                'name' => 'Valonien',
                'color' => '#ca8a04',
                'description' => <<<'TXT'
Parler du sable et des mirages : Valonia et le désert de Saharach, où chaque phrase ramasse un grain dans la gorge. Métaphores d’oasis, marchands qui promettent la lune contre trois kamas comptés, et silence calculé pour dire « tu marches sur mes dunes » sans lever les yeux du chèche.
TXT,
            ],
        ];

        foreach ($definitions as $row) {
            Language::updateOrCreate(
                ['name' => $row['name']],
                [
                    'description' => trim($row['description']),
                    'color' => $row['color'],
                ]
            );
        }
    }
}
