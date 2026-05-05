import { computed, onUnmounted, ref } from "vue";
import { OVERLAY_CONTENT_KIND } from "@/Composables/overlay/overlayConstants";
import { useOverlayStackStore } from "@/Composables/overlay/useOverlayStackStore";
import { useOverlayContentResolver } from "@/Composables/overlay/useOverlayContentResolver";

const stack = useOverlayStackStore();

/**
 * @param {{
 * id?: string,
 * content: any,
 * renderer?: any,
 * trigger?: string,
 * placement?: string,
 * maxWidth?: string,
 * interactive?: boolean,
 * lazy?: boolean,
 * cache?: any,
 * closeOnOutside?: boolean,
 * closeOnEscape?: boolean,
 * }} options
 */
export function useOverlayService(options) {
    const isOpen = ref(false);
    const entryId = ref(options.id || "");
    const resolver = useOverlayContentResolver({ content: options.content, cache: options.cache });

    const inferredKind = computed(() => {
        const c = options.content;
        if (typeof c === "string") return OVERLAY_CONTENT_KIND.TEXT;
        if (typeof c === "function") return OVERLAY_CONTENT_KIND.ASYNC;
        if (c && typeof c === "object") {
            if (typeof c.html === "string") return OVERLAY_CONTENT_KIND.HTML;
            if (c.component) return OVERLAY_CONTENT_KIND.COMPONENT;
            if (typeof c.loader === "function") return OVERLAY_CONTENT_KIND.ASYNC;
        }
        return OVERLAY_CONTENT_KIND.COMPONENT;
    });

    function upsert(status = "idle") {
        const id = stack.addOverlay({
            id: entryId.value || undefined,
            open: isOpen.value,
            renderInHost: Boolean(options.renderInHost),
            status,
            trigger: options.trigger || "auto",
            placement: options.placement || "top",
            maxWidth: options.maxWidth || "auto",
            interactive: Boolean(options.interactive),
            renderer: options.renderer || null,
            contentKind: resolver.resolvedKind.value || inferredKind.value,
            content: resolver.resolved.value,
            loading: resolver.loading.value,
            error: resolver.error.value,
        });
        entryId.value = id;
        return id;
    }

    async function hydrateContent() {
        const result = await resolver.resolve();
        upsert(resolver.error.value ? "error" : "ready");
        return result;
    }

    async function open() {
        if (isOpen.value) return;
        isOpen.value = true;
        upsert("opening");
        if (options.lazy !== false) {
            await hydrateContent();
        } else {
            await hydrateContent();
        }
        upsert("ready");
    }

    function close(reason = "manual") {
        if (!isOpen.value) return;
        isOpen.value = false;
        if (entryId.value) {
            stack.removeOverlay(entryId.value);
        }
        if (reason === "error") {
            resolver.clearCache();
        }
    }

    async function toggle() {
        if (isOpen.value) {
            close("toggle");
            return;
        }
        await open();
    }

    function updatePatch(patch) {
        if (!entryId.value) return;
        stack.updateOverlay(entryId.value, patch || {});
    }

    onUnmounted(() => {
        if (entryId.value) {
            stack.removeOverlay(entryId.value);
        }
        isOpen.value = false;
        resolver.dispose();
    });

    return {
        id: entryId,
        isOpen,
        inferredKind,
        loading: resolver.loading,
        error: resolver.error,
        resolved: resolver.resolved,
        resolvedKind: resolver.resolvedKind,
        open,
        close,
        toggle,
        hydrateContent,
        updatePatch,
        dispose: resolver.dispose,
    };
}
