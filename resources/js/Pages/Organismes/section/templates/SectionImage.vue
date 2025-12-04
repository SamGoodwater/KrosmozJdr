<script setup>
/**
 * SectionImage Template
 * 
 * @description
 * Template de section pour afficher une image.
 * - Affiche une image avec optionnellement une légende
 * - Gère l'alignement (left, center, right)
 * - Gère la taille (sm, md, lg, xl, full)
 * 
 * @props {Object} params - Paramètres de la section
 * @props {String} params.src - URL de l'image (requis)
 * @props {String} params.alt - Texte alternatif (requis)
 * @props {String} params.caption - Légende de l'image (optionnel)
 * @props {String} params.align - Alignement (left|center|right, optionnel)
 * @props {String} params.size - Taille (sm|md|lg|xl|full, optionnel)
 * @props {Object} section - Données complètes de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionImage 
 *   :params="{ src: '/image.jpg', alt: 'Description', caption: 'Légende', align: 'center', size: 'lg' }"
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
 * Classes d'alignement
 */
const alignClasses = computed(() => {
    const align = props.params.align || 'center';
    const alignMap = {
        'left': 'justify-start',
        'center': 'justify-center',
        'right': 'justify-end'
    };
    return alignMap[align] || 'justify-center';
});

/**
 * Classes de taille
 */
const sizeClasses = computed(() => {
    const size = props.params.size || 'md';
    const sizeMap = {
        'sm': 'max-w-sm',
        'md': 'max-w-md',
        'lg': 'max-w-lg',
        'xl': 'max-w-xl',
        'full': 'max-w-full'
    };
    return sizeMap[size] || 'max-w-md';
});
</script>

<template>
    <div class="section-image">
        <div class="flex" :class="alignClasses">
            <figure class="w-full" :class="sizeClasses">
                <Image
                    :src="params.src"
                    :alt="params.alt || 'Image'"
                    class="w-full h-auto rounded-lg shadow-lg"
                />
                <figcaption 
                    v-if="params.caption"
                    class="mt-2 text-sm text-base-content/70 text-center italic"
                >
                    {{ params.caption }}
                </figcaption>
            </figure>
        </div>
    </div>
</template>

<style scoped lang="scss">
.section-image {
    figure {
        margin: 0;
    }
}
</style>

