<script setup>
/**
 * EntityViewText Molecule
 * 
 * @description
 * Vue texte d'une entité : juste le nom avec une icône/image à la même taille
 * Au hover, affiche la vue minimale en tooltip
 * Au clic, affiche la vue minimale de manière persistante
 * Utilisée dans les listes simples
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 */
import { ref, watch, computed, onUnmounted } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityViewMinimal from './EntityViewMinimal.vue';
import { getEntityConfig, normalizeEntityType } from "@/Entities/entity-registry";

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    }
});

const showHover = ref(false);
const showPersistent = ref(false);
const textRef = ref(null);

const entityTypeKey = computed(() => normalizeEntityType(props.entityType));
const entityConfig = computed(() => getEntityConfig(entityTypeKey.value));

const rawEntity = computed(() => {
    if (props.entity && typeof props.entity.toRaw === "function") return props.entity.toRaw();
    if (props.entity && typeof props.entity._data !== "undefined") return props.entity._data;
    return props.entity || {};
});

const entityName = computed(() => {
    // Si c'est une instance de modèle, utiliser getters normalisés
    if (props.entity && typeof props.entity._data !== "undefined") {
        return props.entity.name || props.entity.title || "Entité";
    }
    return props.entity?.name || props.entity?.title || "Entité";
});

// Contexte minimal: pour le champ "name", on n'a pas besoin des permissions.
const entityCtx = computed(() => ({ meta: {}, capabilities: null }));

const nameCell = computed(() => {
    const cfg = entityConfig.value;
    if (cfg?.buildCell) {
        return cfg.buildCell("name", rawEntity.value, entityCtx.value, { context: "text" });
    }
    return { type: "text", value: entityName.value || "Entité", params: {} };
});

const getEntityIcon = (type) => {
    const icons = {
        attribute: 'fa-solid fa-list',
        campaign: 'fa-solid fa-book',
        capability: 'fa-solid fa-star',
        classe: 'fa-solid fa-user',
        consumable: 'fa-solid fa-flask',
        creature: 'fa-solid fa-paw',
        item: 'fa-solid fa-box',
        monster: 'fa-solid fa-dragon',
        npc: 'fa-solid fa-user-tie',
        panoply: 'fa-solid fa-layer-group',
        resource: 'fa-solid fa-gem',
        scenario: 'fa-solid fa-scroll',
        shop: 'fa-solid fa-store',
        specialization: 'fa-solid fa-graduation-cap',
        spell: 'fa-solid fa-wand-magic-sparkles'
    };
    return icons[type] || 'fa-solid fa-circle';
};

// Gérer le clic pour afficher/masquer la vue persistante
const handleClick = (event) => {
    event.stopPropagation();
    showPersistent.value = !showPersistent.value;
};

// Fermer la vue persistante au clic extérieur ou ESC
const handleClickOutside = (event) => {
    if (textRef.value && !textRef.value.contains(event.target)) {
        showPersistent.value = false;
    }
};

const handleEscape = (event) => {
    if (event.key === 'Escape') {
        showPersistent.value = false;
    }
};

// Gérer l'ajout/suppression des event listeners selon l'état
watch(showPersistent, (isVisible) => {
    if (isVisible) {
        // Attendre que le DOM soit mis à jour
        setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
            document.addEventListener('keydown', handleEscape);
        }, 0);
    } else {
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('keydown', handleEscape);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <div 
        ref="textRef"
        class="relative inline-flex items-center gap-2 cursor-pointer group"
        @mouseenter="showHover = true"
        @mouseleave="showHover = false"
        @click="handleClick">
        
        <!-- Icône/image à la même taille que le texte -->
        <div class="flex-shrink-0" :class="entity.image ? 'w-4 h-4' : ''">
            <Image 
                v-if="entity.image" 
                :source="entity.image" 
                :alt="entity.name || 'Image'" 
                size="xs"
                class="w-4 h-4 object-cover rounded"
            />
            <Icon 
                v-else 
                :source="getEntityIcon(entityType)" 
                :alt="entity.name" 
                size="sm"
                class="text-primary-300 group-hover:text-primary-100 transition-colors"
            />
        </div>
        
        <!-- Nom -->
        <template v-if="entityConfig">
            <CellRenderer :cell="nameCell" ui-color="primary" />
        </template>
        <template v-else>
            <Tooltip :content="entityName" placement="top">
                <span class="text-primary-100 group-hover:text-primary-50 transition-colors k-truncate k-truncate-md">
                    {{ entityName }}
                </span>
            </Tooltip>
        </template>

        <!-- Tooltip avec vue minimale au hover (non persistant) -->
        <div 
            v-if="showHover && !showPersistent"
            class="absolute left-0 top-full mt-2 z-50 w-64 pointer-events-none"
            @mouseenter="showHover = true"
            @mouseleave="showHover = false">
            <div class="pointer-events-auto">
                <EntityViewMinimal 
                    :entity="entity" 
                    :entity-type="entityType"
                    :show-actions="false"
                    class="shadow-xl border-2 border-primary/20" />
            </div>
        </div>

        <!-- Vue minimale persistante au clic -->
        <div 
            v-if="showPersistent"
            class="absolute left-0 top-full mt-2 z-[100] w-64"
            @click.stop>
            <EntityViewMinimal 
                :entity="entity" 
                :entity-type="entityType"
                :show-actions="true"
                class="shadow-xl border-2 border-primary/30" />
        </div>
    </div>
</template>

