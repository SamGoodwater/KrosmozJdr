<!--
/**
 * @component Image
 * @description Composant pour afficher des images avec des options avancées de mise en forme.
 *             Gère le redimensionnement, les filtres, les bordures et les tooltips.
 *
 * @example Usage basique
 * <Image src="/path/to/image.jpg" alt="Mon image" />
 *
 * @example Usage avec taille prédéfinie
 * <Image
 *   src="/path/to/image.jpg"
 *   size="2xl"
 *   theme="rounded-lg border"
 * />
 *
 * @example Usage avec dimensions personnalisées et filtre
 * <Image
 *   src="/path/to/image.jpg"
 *   width="64"
 *   height="48"
 *   filter="grayscale"
 *   fit="cover"
 *   position="center"
 * />
 *
 * @props {String} theme - Classes Tailwind pour le style (peut inclure : rounded, border, bgColor, etc.)
 * @props {String} src - URL de l'image (required)
 * @props {String} alt - Texte alternatif (si vide, utilise le nom du fichier)
 * @props {String} size - Taille prédéfinie ('xs' à '6xl')
 *                       xs: 16x16    sm: 24x24    md: 32x32
 *                       lg: 48x48    xl: 64x64    2xl: 96x96
 *                       3xl: 128x128 4xl: 192x192 5xl: 256x256
 *                       6xl: 512x512
 * @props {String} width - Largeur personnalisée (ignoré si size est défini)
 * @props {String} height - Hauteur personnalisée (ignoré si size est défini)
 * @props {String} fit - Mode d'ajustement de l'image
 *                      - cover: Remplit l'espace (défaut)
 *                      - contain: Affiche l'image entière
 *                      - fill: Étire l'image
 *                      - none: Taille originale
 *                      - scale-down: Comme contain mais ne dépasse pas la taille originale
 * @props {String} position - Position de l'image dans son conteneur
 *                           center (défaut), top, right, bottom, left,
 *                           top-left, top-right, bottom-left, bottom-right
 * @props {String} filter - Filtre CSS à appliquer
 *                         grayscale, sepia, blur, brightness,
 *                         contrast, hue-rotate, invert, saturate
 * @props {String} tooltip - Texte à afficher au survol
 * @props {String} tooltipPlacement - Position du tooltip (top, right, bottom, left)
 *
 * @slots default - Aucun slot disponible
 *
 * @emits Aucun événement émis
 *
 * @style Le composant utilise Tailwind CSS pour le style
 *        Les filtres sont définis dans le CSS scoped
 *        Les dimensions sont gérées via les classes Tailwind
 *
 * @dependencies
 * - @/Utils/extractTheme
 * - @/Pages/Atoms/feedback/tooltips.vue
 */
-->

<script setup>
import { ref, computed } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: "",
    },
    width: {
        type: String,
        default: "",
    },
    height: {
        type: String,
        default: "",
    },
    filter: {
        type: String,
        default: "",
        validator: (value) => [
            '',
            'grayscale',
            'sepia',
            'blur',
            'brightness',
            'contrast',
            'hue-rotate',
            'invert',
            'saturate'
        ].includes(value)
    },
    fit: {
        type: String,
        default: "cover",
        validator: (value) => ['cover', 'contain', 'fill', 'none', 'scale-down'].includes(value)
    },
    position: {
        type: String,
        default: "center",
        validator: (value) => [
            'center', 'top', 'right', 'bottom', 'left',
            'top-left', 'top-right', 'bottom-left', 'bottom-right'
        ].includes(value)
    }
});

const sizeMap = {
    'xs': { width: '16', height: '16' },
    'sm': { width: '24', height: '24' },
    'md': { width: '32', height: '32' },
    'lg': { width: '48', height: '48' },
    'xl': { width: '64', height: '64' },
    '2xl': { width: '96', height: '96' },
    '3xl': { width: '128', height: '128' },
    '4xl': { width: '192', height: '192' },
    '5xl': { width: '256', height: '256' },
    '6xl': { width: '512', height: '512' },
};

const filterMap = {
    'grayscale': 'grayscale(100%)',
    'sepia': 'sepia(100%)',
    'blur': 'blur(4px)',
    'brightness': 'brightness(150%)',
    'contrast': 'contrast(150%)',
    'hue-rotate': 'hue-rotate(90deg)',
    'invert': 'invert(100%)',
    'saturate': 'saturate(200%)'
};

const buildImageClasses = (props) => {
    const classes = ['object-cover'];

    // Size (priorité sur width/height)
    const size = props.size;
    if (size && sizeMap[size]) {
        classes.push(`w-${sizeMap[size].width}`);
        classes.push(`h-${sizeMap[size].height}`);
    } else {
        // Width
        const width = props.width;
        if (width) {
            classes.push(`w-${width}`);
        }

        // Height
        const height = props.height;
        if (height) {
            classes.push(`h-${height}`);
        }
    }

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Filtre
    const filter = props.filter;
    if (filter) {
        classes.push(`filter`);
        classes.push(`filter-${filter}`);
    }

    // Object Fit
    classes.push(`object-${props.fit}`);

    // Object Position
    if (props.position !== 'center') {
        classes.push(`object-${props.position}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const imageClasses = computed(() => buildImageClasses(combinedProps.value));

// Calculer le alt text basé sur le nom de fichier si non fourni
const altText = computed(() => {
    if (props.alt) return props.alt;

    const fileName = props.src.split('/').pop();
    return fileName.split('.')[0].replace(/[-_]/g, ' ');
});

const containerClasses = computed(() => {
    const classes = ['relative', 'inline-flex', 'justify-center', 'items-center'];

    // Size (priorité sur width/height)
    const size = props.size;
    if (size && sizeMap[size]) {
        classes.push(`w-${sizeMap[size].width}`);
        classes.push(`h-${sizeMap[size].height}`);
    } else {
        // Width
        const width = props.width;
        if (width) {
            classes.push(`w-${width}`);
        }

        // Height
        const height = props.height;
        if (height) {
            classes.push(`h-${height}`);
        }
    }

    return classes.join(" ");
});
</script>

<template>
    <div :class="containerClasses">
        <BaseTooltip
            :tooltip="tooltip"
            :tooltip-position="tooltipPosition"
        >
            <img
                :src="src"
                :alt="altText"
                :class="imageClasses"
            />
            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>
    </div>
</template>

<style scoped>
img {
    max-width: 100%;
    max-height: 100%;
}

/* Définition des filtres */
.filter-grayscale { filter: grayscale(100%); }
.filter-sepia { filter: sepia(100%); }
.filter-blur { filter: blur(4px); }
.filter-brightness { filter: brightness(150%); }
.filter-contrast { filter: contrast(150%); }
.filter-hue-rotate { filter: hue-rotate(90deg); }
.filter-invert { filter: invert(100%); }
.filter-saturate { filter: saturate(200%); }

/* Object Position personnalisées */
.object-top-left { object-position: top left; }
.object-top-right { object-position: top right; }
.object-bottom-left { object-position: bottom left; }
.object-bottom-right { object-position: bottom right; }
</style>
