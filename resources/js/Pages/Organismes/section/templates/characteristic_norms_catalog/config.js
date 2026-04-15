/**
 * Template « Catalogue de chartes » : liste les caractéristiques ayant une grille de normes
 * (un accordéon par caractéristique, chargement paresseux du détail).
 *
 * @see SectionCharacteristicNormsCatalogRead.vue
 */
export default {
    name: 'Catalogue de chartes (normes)',
    description:
        'Affiche toutes les chartes d’un groupe (créature, objet, sort) dans un accordéon. Idéal pour une page « aide à la création » ; combinez avec des sections texte pour intro et conseils.',
    icon: 'fa-solid fa-table-list',
    value: 'characteristic_norms_catalog',
    supportsAutoSave: true,
    defaultSettings: {
        group: 'spell',
        entity: '*',
        characteristic_keys: [],
    },
    defaultData: {},
    parameters: [
        {
            key: 'group',
            type: 'select',
            label: 'Groupe',
            description: 'Famille de caractéristiques',
            default: 'spell',
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
            description: '* pour tout le groupe, ou monster, item, consumable, resource, spell, etc.',
            default: '*',
            placeholder: '*',
        },
        {
            key: 'characteristic_keys',
            type: 'text',
            label: 'Filtrer par clés (optionnel)',
            description: 'Une clé par ligne ou séparées par des virgules. Vide = toutes les chartes du groupe.',
            default: '',
            placeholder: 'dommages_spell\nsoin_spell',
        },
    ],
};
