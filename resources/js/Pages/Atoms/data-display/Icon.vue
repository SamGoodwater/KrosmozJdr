<script setup>
/**
 * Icon Atom (Atomic Design, DaisyUI)
 *
 * @description
 * Composant atomique pour afficher une icône/image simple.
 * - Support des icônes FontAwesome (fa-solid, fa-brands, fa-regular, fa-duotone)
 * - Support des images via l'atom Image
 * - Props : source (nom logique ou chemin), alt (texte alternatif), size (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl), disabled (hérité de commonProps)
 * - La taille contrôle la hauteur (height), la largeur est auto
 * - Si disabled=true, l'icône est affichée en noir et blanc (grayscale)
 *
 * @note Cet atom n'utilise PAS DaisyUI (aucune classe DaisyUI), il est purement utilitaire.
 *
 * @example
 * <Icon source="icons/modules/pa" alt="PA" size="md" />
 * <Icon source="logos/logo" alt="Logo" size="lg" :disabled="true" />
 * <Icon source="fa-house" alt="Accueil" size="md" pack="solid" />
 *
 * @props {String} source - Nom logique ou chemin de l'icône (obligatoire)
 * @props {String} alt - Texte alternatif (obligatoire)
 * @props {String} size - Taille prédéfinie (xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl), défaut md
 * @props {Boolean} disabled - Affiche l'icône en noir et blanc si true
 * @props {String} pack - Pack FontAwesome (solid, regular, brands, duotone)
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 */
import { computed, ref, watch, onMounted } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import { getCommonProps, getCommonAttrs, mergeClasses } from "@/Utils/atomic-design/uiHelper";
import { size6XlList } from "@/Pages/Atoms/atomMap";
import { sizeHeightMap, faSizeMap } from "./data-displayMap";
import { ImageService } from "@/Utils/file/ImageService";

const props = defineProps({
    ...getCommonProps(),
    source: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: "md",
        validator: (v) => size6XlList.includes(v),
    },
    class: { type: String, default: "" },
    pack: {
        type: String,
        default: "solid",
        validator: (value) =>
            ["solid", "regular", "brands", "duotone"].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const height = computed(() => sizeHeightMap[props.size] || sizeHeightMap.md);
const grayscale = computed(() =>
    props.disabled ? { filter: "grayscale(100%)" } : {},
);

const atomClasses = computed(() => {
    return mergeClasses(["icon"], props.class);
});

// État
const iconUrl = ref("");
const iconPack = ref(props.pack);
const isLoading = ref(false);
const hasError = ref(false);

// Résolution de l'URL de l'icône
async function resolveIcon() {
    if (!props.source) {
        iconUrl.value = "";
        return;
    }

    isLoading.value = true;
    hasError.value = false;

    try {
        if (isFontAwesome.value) {
            iconUrl.value = props.source;
            iconPack.value = extractPack.value;
        } else {
            iconUrl.value = await ImageService.getImageUrl(props.source);
        }
    } catch (error) {
        console.error("Icon - Erreur de chargement:", error);
        hasError.value = true;
        iconUrl.value = "";
    } finally {
        isLoading.value = false;
    }
}

// Classes de l'icône
const iconClasses = computed(() => [
    "icon",
    `icon-${props.size}`,
    props.disabled && "opacity-50 cursor-not-allowed",
    iconPack.value && `fa-${iconPack.value}`,
    iconUrl.value,
]);

// Watch pour recharger l'icône si source change
watch(
    () => props.source,
    () => {
        resolveIcon();
    },
);

onMounted(() => {
    resolveIcon();
});

// Vérifie si c'est une icône FontAwesome
const isFontAwesome = computed(() => {
    return typeof props.source === "string" && props.source.startsWith("fa-");
});

// Extrait le pack à partir de la source
const extractPack = computed(() => {
    if (!isFontAwesome.value) return props.pack;

    if (typeof props.source === "string" && props.source.startsWith("fa-solid")) return "solid";
    if (typeof props.source === "string" && props.source.startsWith("fa-regular")) return "regular";
    if (typeof props.source === "string" && props.source.startsWith("fa-brands")) return "brands";
    if (typeof props.source === "string" && props.source.startsWith("fa-duotone")) return "duotone";

    return props.pack;
});

// Classes pour l'icône FontAwesome
const faClasses = computed(() => {
    // Si le pack est spécifié, on l'utilise
    const iconClass = extractPack.value
        ? `fa-${extractPack.value} ${props.source.replace(/^fa-(solid|regular|brands|duotone)-/, "fa-")}`
        : props.source;

    return mergeClasses([
        iconClass,
        faSizeMap[props.size] || faSizeMap.md,
        "w-auto",
        props.disabled ? "opacity-50" : "",
    ], props.class);
});

// Gestion des attributs d'accessibilité pour éviter les conflits
const attrs = computed(() => {
    const commonAttrs = getCommonAttrs(props);
    
    // Si l'icône est dans un contexte interactif (bouton, lien), 
    // on retire tabindex pour éviter les conflits d'accessibilité
    if (commonAttrs.tabindex !== undefined) {
        const { tabindex, ...rest } = commonAttrs;
        return rest;
    }
    
    return commonAttrs;
});
</script>

<template>
    <!-- Icône FontAwesome -->
    <i
        v-if="isFontAwesome"
        :class="faClasses"
        :style="grayscale"
        v-bind="attrs"
        v-on="$attrs"
    />

    <!-- Image -->
    <Image
        v-else
        :source="props.source"
        :alt="props.alt"
        :height="height"
        :size="''"
        v-bind="attrs"
        v-on="$attrs"
        :style="grayscale"
        :class="atomClasses"
        :disabled="props.disabled"
    />
</template>

<style scoped lang="scss">
.icon {
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: inline-block;
    width: auto;
}
</style>
