<script setup>
/**
 * TanStackTablePagination Molecule
 *
 * @description
 * Pagination côté client (TanStack Table state).
 */

import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { useDevice } from "@/Composables/layout/useDevice";

const props = defineProps({
    pageIndex: { type: Number, required: true },
    pageCount: { type: Number, required: true },
    pageSize: { type: Number, required: true },
    totalRows: { type: Number, required: true },
    perPageOptions: { type: Array, default: () => [10, 25, 50, 100] },
    canPrev: { type: Boolean, default: false },
    canNext: { type: Boolean, default: false },
    /**
     * Taille UI (DaisyUI) appliquée aux contrôles de pagination.
     * Valeurs attendues: xs|sm|md|lg (fallback md).
     */
    uiSize: { type: String, default: "md" },
    /**
     * Couleur UI (Design System) appliquée au select.
     */
    uiColor: { type: String, default: "primary" },
});

const emit = defineEmits(["prev", "next", "first", "last", "go", "set-page-size"]);

const { isMobile, isTablet } = useDevice();

const btnSize = computed(() => {
    if (props.uiSize === "xs") return "xs";
    if (props.uiSize === "lg") return "lg";
    if (props.uiSize === "md") return "md";
    return "sm";
});

const maxPageButtons = computed(() => {
    // Responsive: on réduit si l'espace est faible, sans dépasser 7.
    // - mobile: 3 (1 avant / current / 1 après)
    // - tablette: 5 (2 avant / 2 après)
    // - desktop: 7 (3 avant / 3 après) comme demandé
    if (isMobile.value) return 3;
    if (isTablet.value) return 5;
    return 7;
});

const canFirst = computed(() => props.pageIndex > 0);
const canLast = computed(() => props.pageIndex < Math.max(0, props.pageCount - 1));

const pageNumbers = computed(() => {
    const count = Math.max(0, Number(props.pageCount || 0));
    if (count <= 0) return [];

    const current = Math.min(Math.max(0, Number(props.pageIndex || 0)), count - 1);
    const max = Math.min(maxPageButtons.value, count);
    const half = Math.floor(max / 2); // max=7 => 3 avant / 3 après

    const start = Math.min(Math.max(0, current - half), Math.max(0, count - max));
    const end = start + max - 1;

    const pages = [];
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});
</script>

<template>
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-base-content/70">
            {{ totalRows }} lignes
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end sm:gap-3">
            <label class="flex items-center gap-2 text-sm text-base-content/70">
                <span class="hidden sm:inline">Lignes</span>
                <!-- Menu personnalisé (au lieu du select natif) pour un rendu lisible en thème sombre sur tous les navigateurs -->
                <Dropdown
                    placement="bottom-start"
                    :close-on-content-click="true"
                >
                    <template #trigger>
                        <button
                            type="button"
                            class="flex items-center justify-between gap-2 rounded-lg border border-base-300 bg-base-200/90 px-3 py-2 text-left text-base-content backdrop-blur-sm min-w-[4.5rem] hover:bg-base-300/90 focus:outline focus:ring-2 focus:ring-primary/50"
                            :class="uiSize === 'xs' ? 'text-xs py-1.5' : uiSize === 'lg' ? 'text-base py-2.5' : 'text-sm'"
                            title="Lignes par page"
                            aria-haspopup="listbox"
                            :aria-label="`Lignes par page : ${pageSize} actuellement`"
                        >
                            <span>{{ pageSize }}</span>
                            <Icon source="fa-solid fa-chevron-down" alt="" size="xs" class="opacity-70" />
                        </button>
                    </template>
                    <template #content>
                        <div
                            class="bg-base-100 text-base-content rounded-lg border border-base-300 shadow-lg py-1 min-w-[4.5rem]"
                            role="listbox"
                        >
                            <button
                                v-for="opt in perPageOptions"
                                :key="String(opt)"
                                type="button"
                                role="option"
                                :aria-selected="opt === pageSize"
                                class="w-full px-3 py-2 text-left text-sm transition-colors rounded-none first:rounded-t-lg last:rounded-b-lg"
                                :class="opt === pageSize ? 'bg-primary/20 text-primary-content' : 'hover:bg-base-200'"
                                @click="emit('set-page-size', opt)"
                            >
                                {{ opt }}
                            </button>
                        </div>
                    </template>
                </Dropdown>
            </label>

            <div class="flex items-center gap-1 justify-end">
                <!-- Début -->
                <Btn
                    :size="btnSize"
                    variant="outline"
                    :color="uiColor"
                    square
                    opacity="sm"
                    :disabled="!canFirst"
                    title="Aller à la première page"
                    @click="emit('first')"
                >
                    <Icon source="fa-solid fa-angles-left" alt="Première page" size="sm" />
                </Btn>

                <!-- Précédent -->
                <Btn
                    :size="btnSize"
                    variant="outline"
                    :color="uiColor"
                    square
                    opacity="sm"
                    :disabled="!canPrev"
                    title="Page précédente"
                    @click="emit('prev')"
                >
                    <Icon source="fa-solid fa-chevron-left" alt="Précédent" size="sm" />
                </Btn>

                <!-- Pages -->
                <Btn
                    v-for="p in pageNumbers"
                    :key="p"
                    :size="btnSize"
                    :variant="p === pageIndex ? 'soft' : 'ghost'"
                    :color="uiColor"
                    class="min-w-9"
                    :opacity="p === pageIndex ? undefined : 'sm'"
                    :title="`Aller à la page ${p + 1}`"
                    @click="emit('go', p)"
                >
                    {{ p + 1 }}
                </Btn>

                <!-- Suivant -->
                <Btn
                    :size="btnSize"
                    variant="outline"
                    :color="uiColor"
                    square
                    opacity="sm"
                    :disabled="!canNext"
                    title="Page suivante"
                    @click="emit('next')"
                >
                    <Icon source="fa-solid fa-chevron-right" alt="Suivant" size="sm" />
                </Btn>

                <!-- Fin -->
                <Btn
                    :size="btnSize"
                    variant="outline"
                    :color="uiColor"
                    square
                    opacity="sm"
                    :disabled="!canLast"
                    title="Aller à la dernière page"
                    @click="emit('last')"
                >
                    <Icon source="fa-solid fa-angles-right" alt="Dernière page" size="sm" />
                </Btn>
            </div>

            <div class="text-sm text-base-content/60 text-right">
                Page {{ pageIndex + 1 }} / {{ pageCount || 1 }}
            </div>
        </div>
    </div>
</template>


