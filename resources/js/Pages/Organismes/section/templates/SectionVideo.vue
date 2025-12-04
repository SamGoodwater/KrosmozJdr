<script setup>
/**
 * SectionVideo Template
 * 
 * @description
 * Template de section pour afficher une vidéo.
 * - Supporte YouTube, Vimeo et vidéos directes
 * - Gère l'autoplay et les contrôles
 * 
 * @props {Object} params - Paramètres de la section
 * @props {String} params.src - URL de la vidéo (requis)
 * @props {String} params.type - Type de vidéo (youtube|vimeo|direct, requis)
 * @props {Boolean} params.autoplay - Lecture automatique (optionnel)
 * @props {Boolean} params.controls - Afficher les contrôles (optionnel, défaut: true)
 * @props {Object} section - Données complètes de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionVideo 
 *   :params="{ 
 *     src: 'https://www.youtube.com/watch?v=...',
 *     type: 'youtube',
 *     autoplay: false,
 *     controls: true
 *   }"
 *   :section="section"
 * />
 */
import { computed } from 'vue';

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
 * URL d'embed pour YouTube
 */
const youtubeEmbedUrl = computed(() => {
    if (props.params.type !== 'youtube' || !props.params.src) {
        return null;
    }
    
    // Extraire l'ID de la vidéo YouTube
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const match = props.params.src.match(youtubeRegex);
    
    if (!match || !match[1]) {
        return null;
    }
    
    const videoId = match[1];
    const autoplay = props.params.autoplay ? '&autoplay=1' : '';
    const controls = props.params.controls !== false ? '&controls=1' : '&controls=0';
    
    return `https://www.youtube.com/embed/${videoId}?rel=0${autoplay}${controls}`;
});

/**
 * URL d'embed pour Vimeo
 */
const vimeoEmbedUrl = computed(() => {
    if (props.params.type !== 'vimeo' || !props.params.src) {
        return null;
    }
    
    // Extraire l'ID de la vidéo Vimeo
    const vimeoRegex = /(?:vimeo\.com\/)(?:.*\/)?(\d+)/;
    const match = props.params.src.match(vimeoRegex);
    
    if (!match || !match[1]) {
        return null;
    }
    
    const videoId = match[1];
    const autoplay = props.params.autoplay ? '&autoplay=1' : '';
    const controls = props.params.controls !== false ? '&controls=1' : '&controls=0';
    
    return `https://player.vimeo.com/video/${videoId}?${autoplay}${controls}`;
});

/**
 * URL de la vidéo directe
 */
const directVideoUrl = computed(() => {
    if (props.params.type !== 'direct' || !props.params.src) {
        return null;
    }
    
    return props.params.src;
});

/**
 * Vérifie si la vidéo est valide
 */
const isValid = computed(() => {
    if (!props.params.src || !props.params.type) {
        return false;
    }
    
    if (props.params.type === 'youtube') {
        return youtubeEmbedUrl.value !== null;
    }
    
    if (props.params.type === 'vimeo') {
        return vimeoEmbedUrl.value !== null;
    }
    
    if (props.params.type === 'direct') {
        return directVideoUrl.value !== null;
    }
    
    return false;
});
</script>

<template>
    <div class="section-video">
        <div v-if="isValid" class="video-container">
            <!-- YouTube -->
            <iframe
                v-if="params.type === 'youtube' && youtubeEmbedUrl"
                :src="youtubeEmbedUrl"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                class="w-full aspect-video rounded-lg"
            />
            
            <!-- Vimeo -->
            <iframe
                v-else-if="params.type === 'vimeo' && vimeoEmbedUrl"
                :src="vimeoEmbedUrl"
                frameborder="0"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen
                class="w-full aspect-video rounded-lg"
            />
            
            <!-- Vidéo directe -->
            <video
                v-else-if="params.type === 'direct' && directVideoUrl"
                :src="directVideoUrl"
                :autoplay="params.autoplay || false"
                :controls="params.controls !== false"
                class="w-full rounded-lg"
            >
                Votre navigateur ne supporte pas la lecture de vidéos.
            </video>
        </div>
        
        <div v-else class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <h3 class="font-bold">Vidéo invalide</h3>
                <p class="text-sm">
                    L'URL de la vidéo ou le type spécifié n'est pas valide.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.section-video {
    .video-container {
        position: relative;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }
    
    iframe,
    video {
        display: block;
        width: 100%;
        height: auto;
    }
}
</style>

