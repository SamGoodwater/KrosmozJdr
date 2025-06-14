<script setup>
/**
 * AvatarGroup Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molecule pour grouper plusieurs Avatar (atom) avec gestion du chevauchement, du nombre max affiché, et du "+N".
 * - Props : avatars (array d'objets { src, alt, ... }), size, overlap, max, ring, ringColor, ringOffset, ringOffsetColor, customUtility
 * - Slot par défaut : liste d'Avatar (ou objets users)
 * - Slot #more : contenu personnalisé pour le "+N" (optionnel)
 * - Toutes les classes DaisyUI sont explicites
 * - Utilise l'atom Avatar pour chaque avatar
 * - Responsive et accessibilité
 *
 * @example
 * <AvatarGroup :avatars="users" size="sm" overlap max="3" ring="sm" ringColor="primary" />
 * <AvatarGroup :avatars="users" overlap>
 *   <Avatar v-for="user in users" :key="user.id" :src="user.avatar" :alt="user.name" />
 * </AvatarGroup>
 *
 * @props {Array} avatars - Liste d'avatars à afficher (optionnel si slot)
 * @props {String} size - Taille des avatars (xs, sm, md, lg, xl, ...)
 * @props {Boolean} overlap - Chevauchement des avatars (par défaut false)
 * @props {Number} max - Nombre max d'avatars affichés, le reste en "+N"
 * @props {String} ring, ringColor, ringOffset, ringOffsetColor - Props DaisyUI pour Avatar
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @slot default - Liste d'Avatar (optionnel si avatars fourni)
 * @slot more - Contenu personnalisé pour le "+N" (optionnel)
 */
import { computed, h } from "vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";
import {
    getCustomUtilityProps,
    getCustomUtilityClasses,
} from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    avatars: { type: Array, default: null },
    size: {
        type: String,
        default: "",
        validator: (v) =>
            ["", "xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl"].includes(v),
    },
    overlap: { type: Boolean, default: false },
    max: { type: Number, default: 0 },
    ring: { type: String, default: "" },
    ringColor: { type: String, default: "" },
    ringOffset: { type: String, default: "" },
    ringOffsetColor: { type: String, default: "" },
    ...getCustomUtilityProps(),
});

const groupClasses = computed(() => {
    const classes = ["avatar-group"];
    if (props.overlap) classes.push("avatar-group-overlap");
    classes.push(...getCustomUtilityClasses(props));
    return classes.join(" ");
});

const avatarsToShow = computed(() => {
    if (!props.avatars) return [];
    if (props.max > 0 && props.avatars.length > props.max) {
        return props.avatars.slice(0, props.max);
    }
    return props.avatars;
});
const avatarsMore = computed(() => {
    if (!props.avatars || !props.max || props.avatars.length <= props.max)
        return 0;
    return props.avatars.length - props.max;
});
</script>

<template>
    <div :class="groupClasses">
        <!-- Si avatars fourni, on les rend -->
        <template v-if="avatars && avatars.length">
            <Avatar
                v-for="(avatar, i) in avatarsToShow"
                :key="avatar.id || avatar.alt || i"
                v-bind="{
                    ...avatar,
                    size: props.size || avatar.size,
                    ring: props.ring || avatar.ring,
                    ringColor: props.ringColor || avatar.ringColor,
                    ringOffset: props.ringOffset || avatar.ringOffset,
                    ringOffsetColor:
                        props.ringOffsetColor || avatar.ringOffsetColor,
                    shadow: props.shadow || avatar.shadow,
                    backdrop: props.backdrop || avatar.backdrop,
                    opacity: props.opacity || avatar.opacity,
                }"
            />
            <!-- +N avatar -->
            <span v-if="avatarsMore > 0" class="avatar placeholder select-none">
                <span
                    class="bg-neutral text-neutral-content"
                    :class="size ? `text-${size}` : 'text-md'"
                >
                    <slot name="more">+{{ avatarsMore }}</slot>
                </span>
            </span>
        </template>
        <!-- Sinon, slot par défaut (avatars custom) -->
        <slot v-else />
    </div>
</template>

<style scoped>
.avatar-group-overlap > *:not(:first-child) {
    margin-left: -1.25rem;
    border: 2px solid var(--color-base-100, #fff);
    z-index: 1;
}
</style>
