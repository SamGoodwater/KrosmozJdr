<script setup>
/**
 * Édition du tableau des bonus d’équipement : projection live, pas de paramètres.
 *
 * @example
 * <SectionEquipmentBonusTableEdit :section="section" />
 */
defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});
</script>

<template>
    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info" />
        <span>
            Ce tableau se construit tout seul à partir des caractéristiques objet
            (formule par bande, types d’équipement, prix et forgemagie). Aucun réglage à saisir ici.
        </span>
    </div>
</template>
