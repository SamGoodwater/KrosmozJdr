<script setup>
/**
 * SectionGallery Template
 * 
 * @description
 * Template de section pour afficher une galerie d'images.
 * - Affiche plusieurs images en grille
 * - Gère le nombre de colonnes (2, 3, 4)
 * - Gère l'espacement (sm, md, lg)
 * 
 * @props {Object} params - Paramètres de la section
 * @props {Array} params.images - Tableau d'images (requis)
 * @props {Number} params.columns - Nombre de colonnes (2|3|4, optionnel, défaut: 3)
 * @props {String} params.gap - Espacement entre les images (sm|md|lg, optionnel, défaut: md)
 * @props {Object} section - Données complètes de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionGallery 
 *   :params="{ 
 *     images: [{ src: '/img1.jpg', alt: 'Image 1', caption: 'Légende 1' }],
 *     columns: 3,
 *     gap: 'md'
 *   }"
 *   :section="section"
 * />
 */
import { computed } from 'vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';

const props = defineProps({
    params: {
        type: Object,
        required: true,
        default: () => ({})
    },
    section: {
        type: Object,
        required: true
    },
    user: {
        type: Object,
        default: null
    }
});

/**
 * Images de la galerie
 */
const images = computed(() => {
    return props.params.images || [];
});

/**
 * Classes de colonnes
 */
const columnsClasses = computed(() => {
    const columns = props.params.columns || 3;
    const columnsMap = {
        2: 'grid-cols-1 md:grid-cols-2',
        3: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
    };
    return columnsMap[columns] || 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
});

/**
 * Classes d'espacement
 */
const gapClasses = computed(() => {
    const gap = props.params.gap || 'md';
    const gapMap = {
        'sm': 'gap-2',
        'md': 'gap-4',
        'lg': 'gap-6'
    };
    return gapMap[gap] || 'gap-4';
});
</script>

<template>
    <div class="section-gallery">
        <div v-if="images.length > 0" class="grid" :class="[columnsClasses, gapClasses]">
            <figure
                v-for="(image, index) in images"
                :key="index"
                class="overflow-hidden rounded-lg shadow-lg"
            >
                <Image
                    :src="image.src"
                    :alt="image.alt || `Image ${index + 1}`"
                    class="w-full h-auto object-cover"
                />
                <figcaption 
                    v-if="image.caption"
                    class="p-2 text-sm text-base-content/70 text-center bg-base-200"
                >
                    {{ image.caption }}
                </figcaption>
            </figure>
        </div>
        
        <div v-else class="text-center py-8 text-base-content/50">
            <p>Aucune image dans la galerie.</p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.section-gallery {
    figure {
        margin: 0;
        transition: transform 0.2s ease;
        
        &:hover {
            transform: scale(1.02);
        }
    }
}
</style>

