<script setup>
/**
 * Monte SectionRenderer uniquement lorsque la section entre (ou approche) le viewport.
 *
 * @description
 * Placeholder léger avec ancres `#section-{id}` / `#ssec-{slug}` avant montage.
 * Le rendu riche (TipTap readonly, tableaux, etc.) reste dans SectionRenderer.
 */
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import SectionRenderer from "@/Pages/Organismes/section/SectionRenderer.vue";
import SectionContentSkeleton from "@/Pages/Molecules/section/SectionContentSkeleton.vue";
import { isFullWidthSectionTemplate, sectionWidthClass } from "@/Utils/section/sectionLayout";
import { publicSectionTitle } from "@/Utils/section/sectionTitle";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
    autoEdit: {
        type: Boolean,
        default: false,
    },
    /** Monte immédiatement (édition auto, ancre URL, premières sections visibles). */
    eager: {
        type: Boolean,
        default: false,
    },
    /** Même largeur que la section la plus large de la page. */
    forceFullWidth: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["mounted"]);

const rootRef = ref(null);
const isMounted = ref(props.eager);

const sectionId = computed(() => props.section?.id ?? null);
const sectionSlug = computed(() => String(props.section?.slug || "").trim());
const sectionTitle = computed(() => publicSectionTitle(props.section?.title));

/** Même gabarit que SectionRenderer : la page partage la largeur de sa section la plus large. */
const sectionTemplateValue = computed(() => {
    const raw = props.section?.template ?? props.section?.type ?? "text";
    if (raw && typeof raw === "object" && "value" in raw) {
        return String(raw.value || "text");
    }
    return String(raw || "text");
});
const placeholderLayoutClass = computed(() =>
    sectionWidthClass(props.forceFullWidth || isFullWidthSectionTemplate(sectionTemplateValue.value)),
);

const rootId = computed(() => (sectionId.value ? `section-${sectionId.value}` : undefined));

let observer = null;

function mountNow() {
    if (!isMounted.value) {
        isMounted.value = true;
        emit("mounted", sectionId.value);
    }
    if (observer) {
        observer.disconnect();
        observer = null;
    }
}

function setupObserver() {
    if (typeof window === "undefined" || typeof IntersectionObserver === "undefined") {
        mountNow();
        return;
    }
    if (isMounted.value || !rootRef.value) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries.some((e) => e.isIntersecting)) {
                mountNow();
            }
        },
        {
            root: null,
            rootMargin: "320px 0px 480px 0px",
            threshold: 0,
        },
    );
    observer.observe(rootRef.value);
}

watch(
    () => props.eager,
    (eager) => {
        if (eager) {
            mountNow();
        }
    },
    { immediate: true },
);

watch(
    () => props.autoEdit,
    (edit) => {
        if (edit) {
            mountNow();
        }
    },
);

onMounted(() => {
    if (!isMounted.value) {
        setupObserver();
    }
});

onBeforeUnmount(() => {
    if (observer) {
        observer.disconnect();
        observer = null;
    }
});
</script>

<template>
    <SectionRenderer
        v-if="isMounted"
        :section="section"
        :user="user"
        :auto-edit="autoEdit"
        :force-full-width="forceFullWidth"
    />

    <div
        v-else
        ref="rootRef"
        :id="rootId"
        class="section-lazy-gate section-renderer-surface relative mb-8 rounded-2xl border border-base-300/40 bg-base-100/40 px-3 pb-4 pt-2 shadow-sm backdrop-blur-[1px] md:px-5 md:pb-6 md:pt-3"
        :class="placeholderLayoutClass"
        :data-section-id="sectionId ?? undefined"
        :data-section-slug="sectionSlug || undefined"
        data-section-lazy="pending"
    >
        <span
            v-if="sectionSlug"
            :id="`ssec-${sectionSlug}`"
            class="section-scroll-anchor pointer-events-none absolute left-0 top-0 block h-px w-px -translate-y-20 opacity-0"
            aria-hidden="true"
        />
        <div class="section-lazy-gate__placeholder py-1" aria-hidden="true">
            <SectionContentSkeleton
                :template="sectionTemplateValue"
                :title="sectionTitle"
                :show-header="Boolean(sectionTitle)"
            />
        </div>
        <span class="sr-only">{{ sectionTitle ? `Chargement de ${sectionTitle}` : "Chargement" }}</span>
    </div>
</template>
