import { computed, ref, watch } from "vue";

let seq = 0;
function nextId(prefix) {
    seq += 1;
    return `${prefix}-${seq}`;
}

function focusFirstInteractive(root) {
    if (!(root instanceof HTMLElement)) return;
    const first = root.querySelector(
        'button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])'
    );
    if (first instanceof HTMLElement) first.focus({ preventScroll: true });
}

/**
 * @param {{
 * openRef: import('vue').Ref<boolean>,
 * interactive?: boolean,
 * focusTrap?: boolean
 * }} options
 */
export function useOverlayA11y(options) {
    const triggerId = ref(nextId("overlay-trigger"));
    const panelId = ref(nextId("overlay-panel"));
    const lastFocused = ref(null);
    const interactive = options.interactive ?? false;
    const focusTrap = options.focusTrap ?? false;

    const triggerAttrs = computed(() => ({
        id: triggerId.value,
        "aria-expanded": String(options.openRef.value),
        "aria-controls": panelId.value,
        "aria-describedby": panelId.value,
        "aria-haspopup": interactive ? "dialog" : "true",
    }));

    const panelAttrs = computed(() => ({
        id: panelId.value,
        role: interactive ? "dialog" : "tooltip",
        "aria-modal": interactive ? "false" : undefined,
    }));

    function onOpen(panelEl) {
        lastFocused.value = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        if (interactive && panelEl instanceof HTMLElement) {
            focusFirstInteractive(panelEl);
        }
    }

    function onClose() {
        if (lastFocused.value instanceof HTMLElement) {
            lastFocused.value.focus({ preventScroll: true });
        }
    }

    function onPanelKeydown(event, panelEl) {
        if (!focusTrap || !interactive || event.key !== "Tab" || !(panelEl instanceof HTMLElement)) return;
        const focusable = [...panelEl.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])')]
            .filter((el) => el instanceof HTMLElement && !el.hasAttribute("disabled"));
        if (!focusable.length) {
            event.preventDefault();
            return;
        }
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const current = document.activeElement;
        if (!event.shiftKey && current === last) {
            event.preventDefault();
            first.focus();
        } else if (event.shiftKey && current === first) {
            event.preventDefault();
            last.focus();
        }
    }

    watch(
        () => options.openRef.value,
        (open, prev) => {
            if (open && !prev) return;
            if (!open && prev) onClose();
        }
    );

    return {
        triggerAttrs,
        panelAttrs,
        onOpen,
        onPanelKeydown,
    };
}
