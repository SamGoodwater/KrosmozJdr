<script setup>
import { computed } from "vue";

const props = defineProps({
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

const pagesList = computed(() =>
    (Array.isArray(props.pages) ? props.pages : []).filter((item) => item?.title && item?.url),
);

const l1PagesList = computed(() =>
    (Array.isArray(props.l1Pages) ? props.l1Pages : []).filter((item) => item?.title && item?.url),
);

const sectionsList = computed(() =>
    (Array.isArray(props.sections) ? props.sections : []).filter((item) => item?.title && item?.id),
);

const hasL1Choices = computed(() => l1PagesList.value.length > 1);
const hasPageChoices = computed(() => pagesList.value.length > 1);
const hasSectionChoices = computed(() => sectionsList.value.length > 1);

const singlePageItem = computed(() => {
    if (pagesList.value.length === 1) {
        return pagesList.value[0];
    }
    const url = String(props.pageUrl || "").trim();
    const title = String(props.pageTitle || "").trim();
    if (url && title) {
        return { title, url };
    }
    return null;
});

const singleL1Item = computed(() => {
    if (l1PagesList.value.length === 1) {
        return l1PagesList.value[0];
    }
    const parent = props.parentPage;
    if (parent?.url && parent?.title) {
        return { title: parent.title, url: parent.url };
    }
    return null;
});
const singleSectionItem = computed(() => sectionsList.value[0] || null);

const compactTail = computed(() => {
    const section = String(props.activeSectionTitle || "").trim();
    return section || String(props.pageTitle || "").trim() || "Page";
});

const goToPage = (page) => emit("navigate:page", page);
const goToSection = (section) => emit("navigate:section", section);
</script>

<template>
    <div class="w-full overflow-visible">
        <nav class="breadcrumbs text-sm text-base-content/80" aria-label="Breadcrumb">
            <ul class="flex-nowrap items-center gap-1 sm:gap-2">
                <li class="hidden lg:inline">
                    <div v-if="hasL1Choices" class="dropdown dropdown-bottom">
                        <button
                            type="button"
                            tabindex="0"
                            class="inline-flex max-w-56 items-center gap-1 rounded px-1.5 py-0.5 text-left leading-tight transition-colors hover:text-primary"
                            :title="l1Title || undefined"
                            aria-label="Choisir une page mère"
                        >
                            <span class="truncate">{{ l1Title || "Règles" }}</span>
                            <i class="fa-solid fa-chevron-down relative top-px shrink-0 self-center text-[9px] leading-none opacity-70" />
                        </button>
                        <ul
                            tabindex="0"
                            class="dropdown-content menu menu-xs z-50 mt-1 max-h-72 w-72 overflow-auto rounded-box border border-base-300 bg-base-100/95 p-1 shadow-2xl backdrop-blur-xl"
                        >
                            <li v-for="item in l1PagesList" :key="item.url">
                                <button
                                    type="button"
                                    class="truncate text-left"
                                    :title="item.title"
                                    @click="goToPage(item)"
                                >
                                    {{ item.title }}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <button
                        v-else-if="singleL1Item"
                        type="button"
                        class="inline-flex max-w-56 items-center rounded px-1.5 py-0.5 text-left transition-colors hover:text-primary"
                        :title="singleL1Item.title"
                        @click="goToPage(singleL1Item)"
                    >
                        <span class="truncate">{{ l1Title || singleL1Item.title }}</span>
                    </button>
                    <span v-else class="inline-flex max-w-56 truncate px-1.5 py-0.5" :title="l1Title || undefined">
                        {{ l1Title || "Règles" }}
                    </span>
                </li>

                <li class="hidden lg:inline pointer-events-none select-none" aria-hidden="true">
                    <i class="fa-solid fa-chevron-right text-[9px] opacity-40" />
                </li>

                <li class="hidden sm:inline">
                    <div v-if="hasPageChoices" class="dropdown dropdown-bottom">
                        <button
                            type="button"
                            tabindex="0"
                            class="inline-flex max-w-56 items-center gap-1 rounded px-1.5 py-0.5 text-left leading-tight transition-colors hover:text-primary"
                            :title="pageTitle || undefined"
                            aria-label="Choisir une autre page"
                        >
                            <span class="truncate">{{ pageTitle || "Page" }}</span>
                            <i class="fa-solid fa-chevron-down relative top-px shrink-0 self-center text-[9px] leading-none opacity-70" />
                        </button>
                        <ul
                            tabindex="0"
                            class="dropdown-content menu menu-xs z-50 mt-1 max-h-72 w-72 overflow-auto rounded-box border border-base-300 bg-base-100/95 p-1 shadow-2xl backdrop-blur-xl"
                        >
                            <li v-for="item in pagesList" :key="item.url">
                                <button
                                    type="button"
                                    class="truncate text-left"
                                    :title="item.title"
                                    @click="goToPage(item)"
                                >
                                    {{ item.title }}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <button
                        v-else-if="singlePageItem"
                        type="button"
                        class="inline-flex max-w-56 items-center rounded px-1.5 py-0.5 text-left leading-tight transition-colors hover:text-primary"
                        :title="singlePageItem.title"
                        @click="goToPage(singlePageItem)"
                    >
                        <span class="truncate">{{ pageTitle || singlePageItem.title }}</span>
                    </button>
                    <span v-else class="inline-flex max-w-56 truncate px-1.5 py-0.5" :title="pageTitle || undefined">
                        {{ pageTitle || "Page" }}
                    </span>
                </li>

                <li class="hidden sm:inline pointer-events-none select-none" aria-hidden="true">
                    <i class="fa-solid fa-chevron-right text-[9px] opacity-40" />
                </li>

                <li class="hidden sm:inline">
                    <div v-if="hasSectionChoices" class="dropdown dropdown-bottom dropdown-end">
                        <button
                            type="button"
                            tabindex="0"
                            class="inline-flex max-w-56 items-center gap-1 rounded px-1.5 py-0.5 text-left leading-tight transition-colors hover:text-primary"
                            :title="activeSectionTitle || undefined"
                            aria-label="Choisir une autre section"
                        >
                            <span class="truncate">{{ activeSectionTitle || "Section" }}</span>
                            <i class="fa-solid fa-chevron-down relative top-px shrink-0 self-center text-[9px] leading-none opacity-70" />
                        </button>
                        <ul
                            tabindex="0"
                            class="dropdown-content menu menu-xs z-50 mt-1 max-h-72 w-72 overflow-auto rounded-box border border-base-300 bg-base-100/95 p-1 shadow-2xl backdrop-blur-xl"
                        >
                            <li v-for="section in sectionsList" :key="section.id">
                                <button
                                    type="button"
                                    class="truncate text-left"
                                    :title="section.title"
                                    @click="goToSection(section)"
                                >
                                    {{ section.title }}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <button
                        v-else-if="singleSectionItem"
                        type="button"
                        class="inline-flex max-w-56 items-center rounded px-1.5 py-0.5 text-left leading-tight transition-colors hover:text-primary"
                        :title="singleSectionItem.title"
                        @click="goToSection(singleSectionItem)"
                    >
                        <span class="truncate">{{ activeSectionTitle || singleSectionItem.title }}</span>
                    </button>
                    <span v-else class="inline-flex max-w-56 truncate px-1.5 py-0.5" :title="activeSectionTitle || undefined">
                        {{ activeSectionTitle || "Section" }}
                    </span>
                </li>

                <li class="sm:hidden max-w-[62vw] truncate" :title="compactTail">
                    <span class="truncate">{{ compactTail }}</span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.breadcrumbs {
    overflow: visible !important;
}

.breadcrumbs > ul {
    overflow: visible !important;
}

.breadcrumbs > ul > li {
    min-width: 0;
    overflow: visible;
}

.breadcrumbs :deep(ul > li + *::before) {
    content: none !important;
}

.breadcrumbs > ul > li > :deep(span),
.breadcrumbs > ul > li > :deep(button) {
    align-items: center;
}
</style>
