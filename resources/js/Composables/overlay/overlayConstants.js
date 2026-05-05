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

export const DEFAULT_OVERLAY_OPTIONS = Object.freeze({
    maxOpen: 6,
    baseZIndex: 1100,
    hoverOpenDelayMs: 80,
    hoverCloseDelayMs: 140,
    clickCloseOnOutside: true,
    cacheTtlMs: 60_000,
    cacheMaxEntries: 200,
});

export const OVERLAY_Z_INDEX = Object.freeze({
    hostContainer: 1099,
    floatingPanel: 1200,
});
