<script setup>
/**
 * ResourceViewText — Vue Text pour Resource
 * 
 * @description
 * Nom + icône/image. Au survol, affiche ResourceViewMinimal.
 * Utilisée dans les listes simples.
 * 
 * @props {Resource} resource - Instance du modèle Resource
 */
import { ref, computed, watch, onUnmounted } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import ResourceViewMinimal from './ResourceViewMinimal.vue';

const props = defineProps({
    resource: {
        type: Object,
        required: true
    }
});

const showHover = ref(false);
const showPersistent = ref(false);
const textRef = ref(null);

const nameCell = computed(() => {
    return props.resource.toCell('name', {
        size: 'sm',
        context: 'text',
    });
});

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
        <div class="flex-shrink-0" :class="resource.image ? 'w-4 h-4' : ''">
            <Image 
                v-if="resource.image" 
                :source="resource.image" 
                :alt="resource.name || 'Image'" 
                size="xs"
                class="w-4 h-4 object-cover rounded"
            />
            <Icon 
                v-else 
                source="fa-solid fa-gem" 
                :alt="resource.name" 
                size="sm"
                class="text-primary-300 group-hover:text-primary-100 transition-colors"
            />
        </div>
        
        <!-- Nom -->
        <CellRenderer :cell="nameCell" ui-color="primary" />

        <!-- Tooltip avec vue minimale au hover (non persistant) -->
        <div 
            v-if="showHover && !showPersistent"
            class="absolute left-0 top-full mt-2 z-50 w-64 pointer-events-none"
            @mouseenter="showHover = true"
            @mouseleave="showHover = false">
            <div class="pointer-events-auto">
                <ResourceViewMinimal 
                    :resource="resource" 
                    :show-actions="false"
                    class="shadow-xl border-2 border-primary/20" />
            </div>
        </div>

        <!-- Vue minimale persistante au clic -->
        <div 
            v-if="showPersistent"
            class="absolute left-0 top-full mt-2 z-[100] w-64"
            @click.stop>
            <ResourceViewMinimal 
                :resource="resource" 
                :show-actions="true"
                class="shadow-xl border-2 border-primary/30" />
        </div>
    </div>
</template>
