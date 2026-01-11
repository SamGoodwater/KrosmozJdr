<script setup>
/**
 * CustomCellExample — Exemple de composant personnalisé pour les cellules
 * 
 * @description
 * Exemple de composant personnalisé qui peut être utilisé dans les descriptors
 * via `display.cell.component` et `display.cell.props`.
 * 
 * @example
 * // Dans resource-descriptors.js :
 * display: {
 *   cell: {
 *     component: '@/Pages/Atoms/data-display/CustomCellExample.vue',
 *     props: {
 *       format: 'currency',
 *       locale: 'fr-FR',
 *     },
 *     passEntity: false,
 *     passValue: true,
 *   }
 * }
 */

import { computed } from 'vue';

const props = defineProps({
    /**
     * Valeur brute du champ (si passValue est true)
     */
    value: {
        type: [String, Number, Boolean, Object],
        default: null,
    },
    /**
     * Entité complète (si passEntity est true)
     */
    entity: {
        type: Object,
        default: null,
    },
    /**
     * Format d'affichage
     */
    format: {
        type: String,
        default: 'text',
    },
    /**
     * Locale pour le formatage
     */
    locale: {
        type: String,
        default: 'fr-FR',
    },
    /**
     * Autres props personnalisées
     */
    // ... autres props selon vos besoins
});

const formattedValue = computed(() => {
    if (props.value === null || props.value === undefined) return '—';
    
    switch (props.format) {
        case 'currency':
            return new Intl.NumberFormat(props.locale, {
                style: 'currency',
                currency: 'EUR',
            }).format(Number(props.value));
        case 'number':
            return new Intl.NumberFormat(props.locale).format(Number(props.value));
        case 'date':
            return new Intl.DateTimeFormat(props.locale).format(new Date(props.value));
        default:
            return String(props.value);
    }
});
</script>

<template>
    <span class="text-primary-100">
        {{ formattedValue }}
    </span>
</template>
