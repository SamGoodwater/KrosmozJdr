<script setup>
/**
 * SectionRenderer Organism
 * 
 * @description
 * Composant organisme pour rendre dynamiquement une section selon son type.
 * - Route vers le bon template de section selon le type
 * - Passe les params de la section au template
 * - Gère les erreurs si le type n'existe pas
 * 
 * @props {Object} section - Données de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionRenderer :section="section" :user="user" />
 */
import { computed, defineAsyncComponent } from 'vue';
import SectionText from './templates/SectionText.vue';
import SectionImage from './templates/SectionImage.vue';
import SectionGallery from './templates/SectionGallery.vue';
import SectionVideo from './templates/SectionVideo.vue';
import SectionEntityTable from './templates/SectionEntityTable.vue';

const props = defineProps({
    section: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value === 'object' && 'id' in value && 'type' in value;
        }
    },
    user: {
        type: Object,
        default: null
    }
});

/**
 * Composant template à charger selon le type
 */
const templateComponent = computed(() => {
    const type = props.section.type;
    
    // Mapping des types vers les composants
    const typeMap = {
        'text': SectionText,
        'image': SectionImage,
        'gallery': SectionGallery,
        'video': SectionVideo,
        'entity_table': SectionEntityTable
    };
    
    return typeMap[type] || null;
});

/**
 * Params de la section (avec valeurs par défaut)
 */
const sectionParams = computed(() => {
    return props.section.params || {};
});
</script>

<template>
    <div class="section-renderer" :data-section-id="section.id" :data-section-type="section.type">
        <!-- Rendu dynamique du template -->
        <component
            v-if="templateComponent"
            :is="templateComponent"
            :params="sectionParams"
            :section="section"
            :user="user"
        />

        <!-- Erreur si le type n'existe pas -->
        <div v-else class="section-error alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <h3 class="font-bold">Type de section inconnu</h3>
                <p class="text-sm">
                    Le type "{{ section.type }}" n'est pas reconnu.
                    Veuillez contacter un administrateur.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.section-renderer {
    position: relative;
}

.section-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    min-height: 100px;
}

.section-error {
    margin: 1rem 0;
}
</style>

