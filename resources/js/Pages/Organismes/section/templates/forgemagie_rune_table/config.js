/**
 * Template « Tableau des runes de forgemagie » : projection live du référentiel des caractéristiques.
 *
 * @see SectionForgemagieRuneTableRead.vue
 */
export default {
    name: 'Tableau des runes de forgemagie',
    description:
        'Liste les runes disponibles (bonus maximum, prix moyen, équipements autorisés) d’après les caractéristiques objet forgemageables.',
    icon: 'fa-solid fa-hammer',
    value: 'forgemagie_rune_table',
    supportsAutoSave: true,
    defaultSettings: {
        sort_by: 'name',
        sort_dir: 'asc',
        show_base_price: false,
    },
    defaultData: {},
    parameters: [
        {
            key: 'sort_by',
            type: 'select',
            label: 'Trier par',
            default: 'name',
            options: [
                { value: 'name', label: 'Nom de la rune' },
                { value: 'rune_price', label: 'Prix de la rune' },
                { value: 'max_bonus', label: 'Bonus maximum' },
            ],
        },
    ],
};
