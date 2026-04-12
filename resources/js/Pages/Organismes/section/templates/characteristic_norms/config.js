/**
 * Configuration du template CharacteristicNorms
 *
 * Affiche la charte (normes) d'une caractéristique : grille 5×20, conditions interactives, graphique.
 * La caractéristique, le groupe et l'entité sont configurés dans les settings de la section.
 */
export default {
    name: 'Charte caractéristique',
    description: 'Affiche la charte (normes) d\'une caractéristique avec tableau interactif, graphique et conditions de lecture.',
    icon: 'fa-solid fa-chart-bar',
    value: 'characteristic_norms',
    supportsAutoSave: true,
    defaultSettings: {
        characteristic_key: '',
        group: 'creature',
        entity: '*',
    },
    defaultData: {},
    parameters: [
        {
            key: 'characteristic_key',
            type: 'text',
            label: 'Clé de la caractéristique',
            description: 'Clé unique de la caractéristique (ex: strength_creature, damage_min_spell)',
            default: '',
            placeholder: 'strength_creature',
        },
        {
            key: 'group',
            type: 'select',
            label: 'Groupe',
            description: 'Groupe de la caractéristique',
            default: 'creature',
            options: [
                { value: 'creature', label: 'Créature' },
                { value: 'object', label: 'Objet' },
                { value: 'spell', label: 'Sort' },
            ],
        },
        {
            key: 'entity',
            type: 'text',
            label: 'Entité',
            description: 'Entité spécifique ou * pour le groupe entier',
            default: '*',
            placeholder: '*',
        },
    ],
};
