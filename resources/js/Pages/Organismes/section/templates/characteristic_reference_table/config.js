/**
 * Template « Référentiel des caractéristiques » : grand tableau d'aide à la contribution.
 */
export default {
    name: 'Référentiel des caractéristiques',
    description:
        'Affiche un tableau global des caractéristiques (formules, min/max, défaut) et les bornes équipement/FM avec prix indicatifs.',
    icon: 'fa-solid fa-table-columns',
    value: 'characteristic_reference_table',
    supportsAutoSave: true,
    defaultSettings: {
        group: 'all',
        entity: '*',
        search: '',
        sort_by: 'group',
        sort_dir: 'asc',
        status_filter: 'all',
        show_prices: true,
        show_only_with_equipment: false,
    },
    defaultData: {},
    parameters: [
        {
            key: 'group',
            type: 'select',
            label: 'Groupe',
            default: 'all',
            options: [
                { value: 'all', label: 'Tous' },
                { value: 'creature', label: 'Créature' },
                { value: 'object', label: 'Objet' },
                { value: 'spell', label: 'Sort' },
            ],
        },
    ],
};

