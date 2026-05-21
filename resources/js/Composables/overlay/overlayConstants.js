export const OVERLAY_MAX_WIDTH_CLASS = Object.freeze({
    xs: "max-w-xs",
    sm: "max-w-sm",
    md: "max-w-md",
    lg: "max-w-lg",
    xl: "max-w-xl",
    auto: "",
});

export const OVERLAY_TRIGGER = Object.freeze({
    HOVER: "hover",
    CLICK: "click",
    AUTO: "auto",
    MANUAL: "manual",
});

export const OVERLAY_CONTENT_KIND = Object.freeze({
    TEXT: "text",
    HTML: "html",
    COMPONENT: "component",
    ASYNC: "async",
});

/**
 * Ordre des placements pour `placement="auto"` (middleware {@link https://floating-ui.com/docs/autoPlacement | autoPlacement}).
 * Les côtés centrés (`top`, `bottom`, `left`, `right`) sont évalués en premier ;
 * les variantes `-start` / `-end` servent de repli quand le bord manque de place (coins).
 */
export const OVERLAY_AUTO_ALLOWED_PLACEMENTS = Object.freeze([
    "top",
    "bottom",
    "left",
    "right",
    "top-start",
    "top-end",
    "bottom-start",
    "bottom-end",
    "left-start",
    "left-end",
    "right-start",
    "right-end",
]);

export const OVERLAY_Z_INDEX = Object.freeze({
    hostContainer: 1099,
    stackBase: 1100,
    floatingPanel: 1200,
});

/** Recherche globale header : `dialog.showModal()` (top layer) au-dessus des overlays z-index. */
export const GLOBAL_SEARCH_Z_INDEX = Object.freeze({
    panel: 1260,
});

export const DEFAULT_OVERLAY_OPTIONS = Object.freeze({
    maxOpen: 6,
    baseZIndex: OVERLAY_Z_INDEX.stackBase,
    hoverOpenDelayMs: 80,
    hoverCloseDelayMs: 140,
    clickCloseOnOutside: true,
    cacheTtlMs: 60_000,
    cacheMaxEntries: 200,
});
