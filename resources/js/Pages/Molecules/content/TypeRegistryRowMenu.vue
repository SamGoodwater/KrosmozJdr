<script setup>
/**
 * Menu icône d’une ligne de registre de types (scrap, en jeu, déplacer, supprimer).
 */
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    row: { type: Object, required: true },
    disabled: { type: Boolean, default: false },
    canToggleScrap: { type: Boolean, default: false },
    canToggleCatalog: { type: Boolean, default: false },
    canMove: { type: Boolean, default: false },
    canDelete: { type: Boolean, default: false },
    scrapOn: { type: Boolean, default: false },
    inGameOn: { type: Boolean, default: false },
    /** @type {{ value: string, label: string }[]} */
    moveOptions: { type: Array, default: () => [] },
});

const emit = defineEmits(["toggle-scrap", "toggle-ingame", "move", "delete"]);

const itemClass =
    "flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm hover:bg-base-200";
</script>

<template>
    <Dropdown placement="bottom-end" :disabled="disabled" aria-label="Actions du type">
        <template #trigger>
            <Btn
                size="sm"
                variant="ghost"
                class="btn-square"
                :disabled="disabled"
                title="Actions"
                aria-label="Actions du type"
            >
                <Icon source="fa-solid fa-ellipsis-vertical" size="sm" alt="" />
            </Btn>
        </template>
        <template #content>
            <div class="menu bg-base-100 rounded-box z-1 w-56 p-2 shadow-lg border border-base-300">
                <button
                    v-if="canToggleScrap"
                    type="button"
                    :class="itemClass"
                    @click="emit('toggle-scrap')"
                >
                    <Icon
                        :source="scrapOn ? 'fa-solid fa-ban' : 'fa-solid fa-cloud-arrow-down'"
                        size="sm"
                        alt=""
                    />
                    {{ scrapOn ? "Ne plus scraper" : "Scraper" }}
                </button>
                <button
                    v-if="canToggleCatalog"
                    type="button"
                    :class="itemClass"
                    @click="emit('toggle-ingame')"
                >
                    <Icon
                        :source="inGameOn ? 'fa-solid fa-eye-slash' : 'fa-solid fa-gamepad'"
                        size="sm"
                        alt=""
                    />
                    {{ inGameOn ? "Retirer du jeu" : "Utiliser en jeu" }}
                </button>
                <template v-if="canMove && moveOptions.length">
                    <div class="divider my-1 text-xs">Déplacer vers</div>
                    <button
                        v-for="opt in moveOptions"
                        :key="opt.value"
                        type="button"
                        :class="itemClass"
                        @click="emit('move', opt.value)"
                    >
                        <Icon source="fa-solid fa-arrow-right" size="sm" alt="" />
                        {{ opt.label }}
                    </button>
                </template>
                <button
                    v-if="canDelete"
                    type="button"
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-error hover:bg-error/10"
                    @click="emit('delete')"
                >
                    <Icon source="fa-solid fa-trash" size="sm" alt="" />
                    Supprimer
                </button>
            </div>
        </template>
    </Dropdown>
</template>
