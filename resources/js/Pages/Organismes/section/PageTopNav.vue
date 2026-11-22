<script setup>
/**
 * Bandeau de page : fil d'Ariane et menu des sections.
 *
 * @props {Boolean} showBreadcrumb - Affiche le fil d'Ariane
 * @props {String} pageTitle - Nom de la page dans le fil
 * @props {Array} sections - Entrées du plan (titre, hash, sous-titres)
 */
import { onBeforeUnmount, onMounted, ref } from "vue";
import RulesBreadcrumbSticky from "./RulesBreadcrumbSticky.vue";
import RulesPagePlan from "./RulesPagePlan.vue";

defineProps({
    showBreadcrumb: { type: Boolean, default: true },
    l1Title: { type: String, default: "" },
    l1Pages: { type: Array, default: () => [] },
    pageTitle: { type: String, default: "" },
    pageUrl: { type: String, default: "" },
    parentPage: { type: Object, default: null },
    activeSectionTitle: { type: String, default: "" },
    pages: { type: Array, default: () => [] },
    sections: { type: Array, default: () => [] },
});

const emit = defineEmits(["navigate:page", "navigate:section"]);

const isPlanDropdownOpen = ref(false);
const planDropdownRef = ref(null);

function togglePlanDropdown() {
    isPlanDropdownOpen.value = !isPlanDropdownOpen.value;
}

function closePlanDropdown() {
    isPlanDropdownOpen.value = false;
}

function handleDocumentClick(event) {
    const host = planDropdownRef.value;
    const target = event?.target;
    if (!host || !target) return;
    if (!host.contains(target)) {
        closePlanDropdown();
    }
}

function handleGlobalKeydown(event) {
    if (event?.key === "Escape") {
        closePlanDropdown();
    }
}

function handlePlanPanelClick(event) {
    const target = event?.target;
    if (target?.closest("a, button")) {
        closePlanDropdown();
    }
}

onMounted(() => {
    if (typeof document === "undefined") return;
    document.addEventListener("click", handleDocumentClick);
    window.addEventListener("keydown", handleGlobalKeydown);
});

onBeforeUnmount(() => {
    if (typeof document === "undefined") return;
    document.removeEventListener("click", handleDocumentClick);
    window.removeEventListener("keydown", handleGlobalKeydown);
});
</script>

<template>
    <div class="rules-top-nav sticky top-2 z-40 mb-5 overflow-visible">
        <div class="rules-top-nav__surface flex flex-col gap-2 md:flex-row md:items-start md:justify-between md:gap-3">
            <RulesBreadcrumbSticky
                v-if="showBreadcrumb"
                class="min-w-0 w-full self-start md:flex-1"
                :l1-title="l1Title"
                :l1-pages="l1Pages"
                :page-title="pageTitle || 'Page'"
                :page-url="pageUrl"
                :parent-page="parentPage"
                :active-section-title="activeSectionTitle"
                :pages="pages"
                :sections="sections"
                @navigate:page="emit('navigate:page', $event)"
                @navigate:section="emit('navigate:section', $event)"
            />

            <div class="rules-top-nav__actions flex w-full items-start justify-end gap-2 md:ml-auto md:w-auto md:flex-none">
                <div ref="planDropdownRef" class="rules-page-plan-popover" :class="{ 'is-open': isPlanDropdownOpen }">
                    <button
                        type="button"
                        class="rules-page-plan-popover__trigger"
                        :aria-expanded="isPlanDropdownOpen ? 'true' : 'false'"
                        aria-haspopup="dialog"
                        aria-label="Afficher le plan de la page"
                        @click.stop="togglePlanDropdown"
                    >
                        <span>Plan de la page</span>
                        <i class="fa-solid fa-chevron-down text-[10px] opacity-70" aria-hidden="true" />
                    </button>
                    <div
                        v-if="isPlanDropdownOpen"
                        class="rules-page-plan-popover__panel"
                        role="dialog"
                        aria-label="Plan de la page"
                        @click="handlePlanPanelClick"
                    >
                        <RulesPagePlan
                            :show-heading="false"
                            :l1-title="l1Title"
                            :page-title="pageTitle || 'Page'"
                            :sections="sections"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.rules-top-nav__surface {
    border-radius: var(--radius-box);
    backdrop-filter: blur(18px) saturate(1.08);
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--color-slate-900) 10%, transparent) 0%,
        color-mix(in srgb, var(--color-slate-900) 5%, transparent) 100%
    );
}

.rules-page-plan-popover {
    position: relative;
    width: fit-content;
    min-width: min(20rem, calc(100vw - 2rem));
    max-width: min(24rem, calc(100vw - 2rem));
}

.rules-page-plan-popover__trigger {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    width: 100%;
    min-width: min(20rem, calc(100vw - 2rem));
    max-width: min(24rem, calc(100vw - 2rem));
    min-height: 0;
    padding: 0.5rem 0.75rem;
    border-radius: var(--radius-box);
    border: 1px solid color-mix(in srgb, var(--color-slate-300) 18%, transparent);
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--color-slate-900) 18%, transparent) 0%,
        color-mix(in srgb, var(--color-slate-900) 10%, transparent) 100%
    );
    backdrop-filter: blur(16px) saturate(1.06);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: color-mix(in srgb, hsl(var(--bc)) 75%, transparent);
    cursor: pointer;
    user-select: none;
}

.rules-page-plan-popover__trigger > span {
    overflow-wrap: anywhere;
    white-space: normal;
}

.rules-page-plan-popover.is-open .rules-page-plan-popover__trigger i {
    transform: rotate(180deg);
}

.rules-page-plan-popover__trigger i {
    transition: transform 0.2s ease;
    flex: 0 0 auto;
}

.rules-page-plan-popover__panel {
    position: absolute;
    top: calc(100% + 0.45rem);
    right: 0;
    z-index: 60;
    width: min(24rem, calc(100vw - 2rem));
    min-width: min(20rem, calc(100vw - 2rem));
    max-height: min(70vh, 34rem);
    overflow: auto;
    padding: 0.5rem;
    border-radius: var(--radius-box);
    border: 1px solid color-mix(in srgb, var(--color-slate-300) 20%, transparent);
    background: linear-gradient(
        180deg,
        color-mix(in srgb, var(--color-slate-900) 70%, transparent) 0%,
        color-mix(in srgb, var(--color-slate-900) 85%, transparent) 100%
    );
    backdrop-filter: blur(18px) saturate(1.08);
    box-shadow: 0 12px 36px color-mix(in srgb, black 40%, transparent);
}
</style>
