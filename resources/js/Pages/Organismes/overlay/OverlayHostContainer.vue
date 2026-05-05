<script setup>
import { computed, onMounted } from "vue";
import { useOverlayStackStore } from "@/Composables/overlay/useOverlayStackStore";
import { OVERLAY_MAX_WIDTH_CLASS, OVERLAY_Z_INDEX } from "@/Composables/overlay/overlayConstants";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";

const stack = useOverlayStackStore();
const overlays = computed(() => stack.overlays.value.filter((x) => x.renderInHost === true));
const hasOverlays = computed(() => overlays.value.length > 0);
const hostContainerStyle = computed(() => ({ zIndex: OVERLAY_Z_INDEX.hostContainer }));

onMounted(() => {
    // Nettoie d'eventuelles entrees residuelles (ex: HMR/navigation interrompue).
    stack.closeAll();
});

const maxWidthClassFor = (value) => OVERLAY_MAX_WIDTH_CLASS[value] || "";
const safeHtml = (value) => (typeof value === "string" ? sanitizeHtml(value) : "");
</script>

<template>
    <Teleport v-if="hasOverlays" to="body">
        <div class="pointer-events-none fixed inset-0" :style="hostContainerStyle" aria-hidden="true">
            <div
                v-for="item in overlays"
                :key="item.id"
                class="pointer-events-auto absolute"
                :style="{ zIndex: item.zIndex || 1100 }"
            >
                <div
                    class="overlay-host-surface"
                    :class="maxWidthClassFor(item.maxWidth)"
                >
                    <div
                        v-if="item.loading"
                        class="flex items-center gap-2 rounded-md border border-base-300 bg-base-100/95 p-2 text-xs text-base-content/80 shadow-md"
                    >
                        <span class="loading loading-spinner loading-xs"></span>
                        <span>Chargement...</span>
                    </div>

                    <component
                        :is="item.renderer"
                        v-else-if="item.renderer && item.contentKind === 'component'"
                        v-bind="item.content?.props || {}"
                    />

                    <component
                        :is="item.content?.component"
                        v-else-if="item.contentKind === 'component' && item.content?.component"
                        v-bind="item.content?.props || {}"
                    />

                    <!-- eslint-disable vue/no-v-html -->
                    <div
                        v-else-if="item.contentKind === 'html'"
                        class="rounded-md border border-base-300 bg-base-100/95 p-2 text-sm shadow-md"
                        v-html="safeHtml(item.content)"
                    />
                    <!-- eslint-enable vue/no-v-html -->

                    <div
                        v-else
                        class="rounded-md border border-base-300 bg-base-100/95 p-2 text-sm shadow-md"
                    >
                        {{ item.content ?? "" }}
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
