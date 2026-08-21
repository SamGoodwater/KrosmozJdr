<script setup>
/**
 * EntityLineRowActions — raccourcis d'actions communs aux vues ligne.
 *
 * @description
 * Centralise le conteneur hover/focus et la whitelist des actions disponibles
 * dans les `*LineRow.vue`.
 *
 * @example
 * <EntityLineRowActions entity-type="items" :entity="item" @action="handleAction" />
 */
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";

const props = defineProps({
    entityType: { type: String, required: true },
    entity: { type: Object, required: true },
});

const emit = defineEmits(["action"]);

const LINE_ROW_ACTION_KEYS = Object.freeze([
    "state",
    "pin",
    "quick-view",
    "quick-edit",
    "view-dofusdb",
    "favorite",
    "copy-link",
]);
</script>

<template>
    <div class="entity-row-actions-hover-reveal flex min-w-8 flex-1 justify-end" @click.stop>
        <EntityActions
            :entity-type="props.entityType"
            :entity="props.entity"
            format="dropdown"
            :whitelist="LINE_ROW_ACTION_KEYS"
            @action="(actionKey, entity) => emit('action', actionKey, entity)"
        />
    </div>
</template>
