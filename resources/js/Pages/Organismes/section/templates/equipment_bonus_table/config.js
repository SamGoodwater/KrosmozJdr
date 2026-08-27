/**
 * Template « Tableau des bonus d’équipement » : projection live slot × carac × bandes.
 *
 * @see SectionEquipmentBonusTableRead.vue
 */
export default {
    name: 'Tableau des bonus d’équipement',
    description:
        'Affiche les plafonds de bonus par type d’équipement et par caractéristique (bandes de 2 niveaux, prix, forgemagie), d’après les formules en base.',
    icon: 'fa-solid fa-table',
    value: 'equipment_bonus_table',
    supportsAutoSave: true,
    defaultSettings: {},
    defaultData: {},
    parameters: [],
};
