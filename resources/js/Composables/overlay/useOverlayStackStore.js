import { computed, reactive } from "vue";
import { DEFAULT_OVERLAY_OPTIONS } from "@/Composables/overlay/overlayConstants";

const state = reactive({
    overlays: [],
    lastId: 0,
    config: {
        maxOpen: DEFAULT_OVERLAY_OPTIONS.maxOpen,
        baseZIndex: DEFAULT_OVERLAY_OPTIONS.baseZIndex,
    },
});

function nextId() {
    state.lastId += 1;
    return `ovl-${state.lastId}`;
}

function touchOverlay(id) {
    const entry = state.overlays.find((x) => x.id === id);
    if (!entry) return;
    entry.lastActiveAt = Date.now();
}

function ensureCapacity() {
    if (state.overlays.length <= state.config.maxOpen) return;
    const sorted = [...state.overlays].sort((a, b) => a.lastActiveAt - b.lastActiveAt);
    const victim = sorted[0];
    if (!victim) return;
    removeOverlay(victim.id);
}

function assignZIndexes() {
    state.overlays.forEach((entry, idx) => {
        entry.zIndex = state.config.baseZIndex + idx + 1;
    });
}

function addOverlay(payload) {
    const id = payload?.id || nextId();
    const now = Date.now();
    const existing = state.overlays.find((x) => x.id === id);
    if (existing) {
        Object.assign(existing, payload, { id, lastActiveAt: now });
        touchOverlay(id);
        assignZIndexes();
        return id;
    }
    state.overlays.push({
        id,
        createdAt: now,
        lastActiveAt: now,
        status: "idle",
        zIndex: state.config.baseZIndex + state.overlays.length + 1,
        ...payload,
    });
    ensureCapacity();
    assignZIndexes();
    return id;
}

function updateOverlay(id, patch) {
    const entry = state.overlays.find((x) => x.id === id);
    if (!entry) return;
    Object.assign(entry, patch || {});
    entry.lastActiveAt = Date.now();
    assignZIndexes();
}

function removeOverlay(id) {
    const idx = state.overlays.findIndex((x) => x.id === id);
    if (idx === -1) return;
    state.overlays.splice(idx, 1);
    assignZIndexes();
}

function closeAll() {
    state.overlays.splice(0, state.overlays.length);
}

export function useOverlayStackStore() {
    const overlays = computed(() => state.overlays);
    const topOverlay = computed(() => {
        if (!state.overlays.length) return null;
        return state.overlays[state.overlays.length - 1];
    });
    return {
        overlays,
        topOverlay,
        config: state.config,
        addOverlay,
        updateOverlay,
        removeOverlay,
        closeAll,
        touchOverlay,
    };
}
