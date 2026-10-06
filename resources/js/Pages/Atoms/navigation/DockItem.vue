<script setup>
defineOptions({ inheritAttrs: false });

/**
 * DockItem Atom (DaisyUI + Custom Utility + Route)
 *
 * @description
 * Atomique item de dock stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Par défaut : `<li class="dock-item">` + trigger (enfant direct du `<ul>` Dock)
 * - `bare` : trigger seul (le parent fournit le `<li>` et peut envelopper tooltip/dropdown)
 *
 * @see https://daisyui.com/components/dock/
 *
 * @example
 * <DockItem icon="fa-home" pack="solid" label="Accueil" active route="home" />
 * <li class="dock-item"><DockItem bare icon="fa-user" label="Compte" /></li>
 */
import { computed, useAttrs } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import RouteAtom from "@/Pages/Atoms/action/Route.vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import { sizeXlList } from "@/Pages/Atoms/atomMap";

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    active: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: "" },
    pack: {
        type: String,
        default: "",
        validator: (v) => ["solid", "regular", "brands", "duotone"].includes(v),
    },
    label: { type: String, default: "" },
    route: { type: String, default: "" },
    href: { type: String, default: "" },
    color: { type: String, default: "" },
    size: {
        type: String,
        default: "",
        validator: (v) => sizeXlList.includes(v),
    },
    target: {
        type: String,
        default: "",
        validator: (v) =>
            ["", "_blank", "_self", "_parent", "_top"].includes(v),
    },
    /**
     * Sans `<li>` : le parent fournit le list item (tooltip/dropdown autour du trigger).
     */
    bare: { type: Boolean, default: false },
});

const fallthroughAttrs = useAttrs();

const itemClasses = computed(() =>
    mergeClasses(
        [
            "dock-item",
            props.active && "dock-active",
            props.size === "xs" && "dock-xs",
            props.size === "sm" && "dock-sm",
            props.size === "md" && "dock-md",
            props.size === "lg" && "dock-lg",
            props.size === "xl" && "dock-xl",
            props.class,
        ],
        getCustomUtilityClasses(props),
    ),
);

const triggerClasses = computed(() =>
    mergeClasses("dock-item__trigger", props.bare ? props.class : ""),
);

const commonAttrs = computed(() => getCommonAttrs(props));
const triggerAriaLabel = computed(
    () => props.ariaLabel || props.label || undefined,
);

/** Attrs HTML hors props déclarées (ex. data-*, aria-expanded du Dropdown). */
const triggerFallthrough = computed(() => {
    const out = {};
    for (const [key, value] of Object.entries(fallthroughAttrs)) {
        if (key === "class" || key === "style") continue;
        if (key.startsWith("on") && typeof value === "function") continue;
        out[key] = value;
    }
    return out;
});

const emit = defineEmits(["click"]);

function onTriggerClick(event) {
    if (props.disabled) {
        event.preventDefault();
        return;
    }
    emit("click", event);
}
</script>

<template>
    <li
        v-if="!bare"
        :class="itemClasses"
        v-bind="commonAttrs"
    >
        <RouteAtom
            v-if="route || href"
            :route="route"
            :href="href"
            :disabled="props.disabled"
            :aria-label="triggerAriaLabel"
            :tabindex="props.tabindex"
            :role="props.role"
            :id="props.id"
            :target="props.target"
            class="dock-item__trigger"
            v-bind="triggerFallthrough"
            @click="onTriggerClick"
        >
            <span
                v-if="$slots.icon || icon"
                class="dock-item__icon"
            >
                <slot name="icon">
                    <Icon
                        v-if="icon"
                        :source="icon"
                        :pack="pack"
                        :alt="'icon'"
                        :size="size || 'md'"
                        :disabled="props.disabled"
                    />
                </slot>
            </span>
            <span v-if="$slots.label || label" class="dock-label">
                <slot name="label">{{ label }}</slot>
            </span>
            <slot />
        </RouteAtom>
        <button
            v-else
            type="button"
            :disabled="props.disabled"
            :tabindex="props.tabindex"
            :aria-label="triggerAriaLabel"
            :id="props.id"
            class="dock-item__trigger"
            v-bind="triggerFallthrough"
            @click="onTriggerClick"
        >
            <span
                v-if="$slots.icon || icon"
                class="dock-item__icon"
            >
                <slot name="icon">
                    <Icon
                        v-if="icon"
                        :source="icon"
                        :pack="pack"
                        :alt="'icon'"
                        :size="size || 'md'"
                        :disabled="props.disabled"
                    />
                </slot>
            </span>
            <span v-if="$slots.label || label" class="dock-label">
                <slot name="label">{{ label }}</slot>
            </span>
            <slot />
        </button>
    </li>

    <RouteAtom
        v-else-if="route || href"
        :route="route"
        :href="href"
        :disabled="props.disabled"
        :aria-label="triggerAriaLabel"
        :tabindex="props.tabindex"
        :role="props.role"
        :id="props.id"
        :target="props.target"
        :class="triggerClasses"
        v-bind="{ ...commonAttrs, ...triggerFallthrough }"
        @click="onTriggerClick"
    >
        <span
            v-if="$slots.icon || icon"
            class="dock-item__icon"
        >
            <slot name="icon">
                <Icon
                    v-if="icon"
                    :source="icon"
                    :pack="pack"
                    :alt="'icon'"
                    :size="size || 'md'"
                    :disabled="props.disabled"
                />
            </slot>
        </span>
        <span v-if="$slots.label || label" class="dock-label">
            <slot name="label">{{ label }}</slot>
        </span>
        <slot />
    </RouteAtom>

    <button
        v-else
        type="button"
        :disabled="props.disabled"
        :tabindex="props.tabindex"
        :aria-label="triggerAriaLabel"
        :id="props.id"
        :class="triggerClasses"
        v-bind="{ ...commonAttrs, ...triggerFallthrough }"
        @click="onTriggerClick"
    >
        <span
            v-if="$slots.icon || icon"
            class="dock-item__icon"
        >
            <slot name="icon">
                <Icon
                    v-if="icon"
                    :source="icon"
                    :pack="pack"
                    :alt="'icon'"
                    :size="size || 'md'"
                    :disabled="props.disabled"
                />
            </slot>
        </span>
        <span v-if="$slots.label || label" class="dock-label">
            <slot name="label">{{ label }}</slot>
        </span>
        <slot />
    </button>
</template>

<style scoped lang="scss"></style>
