<script setup>
/**
 * AptitudeViewMinimal — Vue minimal (Aptitude)
 *
 * @description
 * EntityMinimalCard : image + nom ; description discrète ; effet mis en avant.
 */
import { computed } from "vue";
import AptitudeIcon from "@/Pages/Molecules/entity/aptitude/AptitudeIcon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import { Aptitude } from "@/Models/Entity/Aptitude";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";

const props = defineProps({
    aptitude: { type: Object, default: null },
    /** Fallback si le parent passe `entity` (ex. map modal incomplète). */
    entity: { type: Object, default: null },
    showActions: { type: Boolean, default: true },
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "delete",
    "action",
]);

const model = computed(() => {
    const raw = props.aptitude ?? props.entity;
    if (!raw) return null;
    if (raw instanceof Aptitude) return raw;
    if (typeof raw.toCell === "function") return raw;
    return new Aptitude(raw._data ?? raw);
});

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "aptitudes",
    showRoute: "entities.aptitudes.show",
    editRoute: "entities.aptitudes.edit",
    routeParam: "aptitude",
    emit,
    getEntity: () => model.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const imageUrl = computed(() => {
    const u = model.value?.image ?? model.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const displayName = computed(() => model.value?.name || "Aptitude");

const getCell = (fieldKey) => {
    if (!model.value || typeof model.value.toCell !== "function") return null;
    return model.value.toCell(fieldKey, { size: "sm", context: "minimal" });
};

const descriptionCell = computed(() => getCell("description"));
const hasDescription = computed(() => {
    const v = descriptionCell.value?.value;
    if (v == null) return false;
    return String(v).replace(/<[^>]*>/g, "").trim().length > 0;
});

const effectRaw = computed(() => {
    const raw = model.value?.effect ?? model.value?._data?.effect;
    return raw != null && String(raw).trim() !== "" ? String(raw) : "";
});

const effectPlainText = computed(() =>
    effectRaw.value
        .replace(/<[^>]+>/g, " ")
        .replace(/\s+/g, " ")
        .trim(),
);

const hasEffect = computed(() => Boolean(effectPlainText.value));

const effectHtmlSafe = computed(() => {
    if (!effectRaw.value || !/<[a-z][\s\S]*>/i.test(effectRaw.value)) return "";
    return sanitizeHtml(effectRaw.value);
});

const usageBits = computed(() => {
    const bits = [];
    for (const key of ["reuse", "duration", "range"]) {
        const v = model.value?.[key] ?? model.value?._data?.[key];
        if (v != null && String(v).trim() !== "") bits.push(String(v).trim());
    }
    return bits;
});
</script>

<template>
    <EntityMinimalCard
        v-if="model"
        :display-mode="displayMode"
        pinned-entity-type="aptitudes"
        :pinned-entity-id="model?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div data-cy="entity-minimal-card-compact" class="flex flex-col gap-1.5 p-2">
                <div class="flex gap-2">
                    <AptitudeIcon
                        size="xs"
                        :src="imageUrl || ''"
                        :label="displayName"
                        :aptitude-id="model?.id"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex min-w-0 items-center gap-1.5">
                            <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                        </div>
                        <p
                            v-if="usageBits.length"
                            class="mt-0.5 truncate text-[10px] text-base-content/55"
                            :title="usageBits.join(' · ')"
                        >
                            {{ usageBits.join(" · ") }}
                        </p>
                        <div
                            v-if="hasDescription"
                            class="prose prose-sm prose-invert mt-1 max-w-none text-[11px] leading-snug italic text-primary-300/80 line-clamp-2"
                        >
                            <CellRenderer :cell="descriptionCell" ui-color="primary" />
                        </div>
                    </div>
                </div>
                <div
                    v-if="hasEffect"
                    class="group/effect w-full rounded-md border-glass-t-sm bg-primary/5 px-1.5 py-1"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/95">
                        Effet
                    </p>
                    <!-- eslint-disable vue/no-v-html -- HTML sanitizé -->
                    <article
                        v-if="effectHtmlSafe"
                        class="prose prose-sm prose-invert max-w-none text-[11px] leading-snug text-base-content line-clamp-3 group-hover/effect:line-clamp-none"
                        v-html="effectHtmlSafe"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                    <p
                        v-else
                        class="wrap-break-word text-[11px] leading-snug text-base-content line-clamp-3 group-hover/effect:line-clamp-none"
                    >
                        {{ effectPlainText }}
                    </p>
                </div>
            </div>
        </template>
        <template #expanded>
            <div data-cy="entity-minimal-card-expanded" class="flex flex-col gap-1.5 p-2">
                <div class="flex items-start gap-2">
                    <AptitudeIcon
                        size="xs"
                        :src="imageUrl || ''"
                        :label="displayName"
                        :aptitude-id="model?.id"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex w-full min-w-0 items-start gap-1.5">
                            <div class="min-w-0">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    <EntityMinimalTitle :label="displayName" @open="openQuickView" />
                                </div>
                                <p
                                    v-if="usageBits.length"
                                    class="mt-0.5 text-[10px] text-base-content/55"
                                >
                                    {{ usageBits.join(" · ") }}
                                </p>
                            </div>
                            <div v-if="showActions" data-entity-actions class="ml-auto flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="aptitudes"
                                    :entity="model"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div
                            v-if="hasDescription"
                            class="prose prose-sm prose-invert mt-1 max-w-none text-xs leading-snug italic text-primary-300/80"
                        >
                            <CellRenderer :cell="descriptionCell" ui-color="primary" />
                        </div>
                    </div>
                </div>
                <div
                    v-if="hasEffect"
                    class="w-full rounded-md border-glass-t-sm bg-primary/5 px-1.5 py-1"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/95">
                        Effet
                    </p>
                    <!-- eslint-disable vue/no-v-html -- HTML sanitizé -->
                    <article
                        v-if="effectHtmlSafe"
                        class="prose prose-sm prose-invert max-w-none text-xs leading-snug text-base-content"
                        v-html="effectHtmlSafe"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                    <p v-else class="wrap-break-word text-xs leading-snug text-base-content">
                        {{ effectPlainText }}
                    </p>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
