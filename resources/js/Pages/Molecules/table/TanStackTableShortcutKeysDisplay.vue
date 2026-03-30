<script setup>
/**
 * Affichage d’une chaîne de raccourcis avec l’atome DaisyUI Kbd.
 *
 * @see https://daisyui.com/components/kbd/
 */
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";
import { parseShortcutKeysForDisplay } from "@/Composables/table/useTanStackTableKeyboard.js";

defineProps({
    /** Ex. « Alt+N », « Ctrl+Shift+A », « Alt+Entrée ou Alt+E » */
    keys: { type: String, default: "" },
});
</script>

<template>
    <span class="inline-flex flex-wrap items-center gap-x-1 gap-y-1">
        <template v-for="(group, gi) in parseShortcutKeysForDisplay(keys)" :key="gi">
            <span v-if="gi > 0" class="text-xs text-base-content/50 px-0.5">ou</span>
            <span class="inline-flex flex-wrap items-center gap-0.5">
                <template v-for="(part, pi) in group.parts" :key="pi">
                    <span
                        v-if="pi > 0"
                        class="text-base-content/40 text-xs px-0.5 select-none"
                        aria-hidden="true"
                    >
                        {{ group.joiner }}
                    </span>
                    <Kbd size="xs">{{ part }}</Kbd>
                </template>
            </span>
        </template>
    </span>
</template>
