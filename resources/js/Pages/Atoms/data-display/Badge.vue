<script setup>
/**
 * Badge Atom (DaisyUI + Tailwind)
 *
 * @description
 * Composant atomique Badge conforme DaisyUI (v5.x) et Atomic Design.
 * - Slot par défaut : contenu du badge (texte, nombre, icône, etc.)
 * - Prop content : texte simple à afficher (prioritaire sur slot par défaut)
 * - Slot #content : contenu HTML complexe (prioritaire sur prop content)
 * - Props DaisyUI : color, size, variant (outline, dash, soft, ghost)
 * - Support des couleurs Tailwind : format 'color-shade' (ex: 'blue-700', 'orange-500')
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/badge/
 * @version DaisyUI v5.x
 *
 * @example
 * <Badge color="primary" content="Nouveau" />
 * <Badge color="error" size="lg" variant="outline">Erreur</Badge>
 * <Badge color="blue-700" content="Custom Tailwind" />
 * <Badge color="orange-500" size="sm">Custom</Badge>
 * <Badge color="auto" auto-label="Alice" content="A" />
 * <Badge color="auto" auto-label="12" auto-scheme="level" content="N12" />
 * <Badge color="auto" auto-label="6" auto-scheme="rarity" content="Unique" />
 *
 * @props {String} content - Texte simple à afficher dans le badge (optionnel, prioritaire sur slot)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error') ou Tailwind (format 'color-shade', ex: 'blue-700') ou auto ('auto')
 * @props {String} autoLabel - Source de l'auto-color (obligatoire si le badge utilise un slot, optionnel si `content` est fourni)
 * @props {String} autoScheme - Nuancié: 'mixed' | 'rainbow' | 'level' | 'rarity' (défaut: 'mixed')
 * @props {String} autoTone - 'mid' | 'light' | 'dark' (défaut: 'mid')
 * @props {Boolean} glassy - Active un léger gradient "glassmorphism" (défaut: false)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl'), défaut ''
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft', 'ghost'), défaut ''
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - Contenu du badge (fallback)
 * @slot content - Contenu HTML complexe prioritaire
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from "vue"
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorList, sizeXlList, variantList } from '@/Pages/Atoms/atomMap';
import { isValidColor, getReadableTextColor, getTailwindTokenFromLabel } from '@/Utils/color/Color';

// Fonction pour détecter si une couleur est Tailwind (format 'color-shade')
// Utilisée dans le computed, pas dans le validator
function isTailwindColor(color) {
    if (!color || colorList.includes(color)) return false;
    // Format Tailwind : 'color-shade' (ex: 'blue-700', 'orange-500')
    return /^[a-z]+-\d+$/.test(color);
}

function isAutoColor(color) {
    return color === 'auto';
}

function isCssColor(color) {
    // On accepte hex/rgb/hsl/oklch... tout format reconnu par colord.
    // On exclut explicitement les couleurs DaisyUI et Tailwind.
    if (!color) return false;
    if (colorList.includes(color)) return false;
    if (isTailwindColor(color)) return false;
    return isValidColor(color);
}

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    content: { type: String, default: '' },
    color: {
        type: String,
        default: '',
        // Validator : DaisyUI, Tailwind 'color-shade', ou couleur CSS valide (hex/rgb/hsl/oklch)
        // NOTE: `defineProps()` est hoisté en dehors de setup() : ne pas référencer de fonctions locales ici.
        // On peut toutefois référencer les imports (ex: isValidColor).
        validator: v =>
            !v ||
            v === 'auto' ||
            colorList.includes(v) ||
            /^[a-z]+-\d+$/.test(v) ||
            isValidColor(v),
    },
    autoLabel: { type: String, default: '' },
    autoScheme: {
        type: String,
        default: 'mixed',
        validator: v => ['mixed', 'rainbow', 'level', 'rarity'].includes(v),
    },
    autoTone: {
        type: String,
        default: 'mid',
        validator: v => ['mid', 'light', 'dark'].includes(v),
    },
    glassy: { type: Boolean, default: false },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    variant: {
        type: String,
        default: '',
        validator: v => variantList.includes(v),
    },
    outline: {
        type: Boolean,
        default: false
    }
});

const autoColorToken = computed(() => {
    if (!isAutoColor(props.color)) return '';

    const label = (props.autoLabel || props.content || '').trim();
    if (!label) return 'blue-500';

    return getTailwindTokenFromLabel(label, {
        scheme: props.autoScheme,
        tone: props.autoTone,
    });
});

const effectiveColor = computed(() => (isAutoColor(props.color) ? autoColorToken.value : props.color));

// Détecter si la couleur est Tailwind ou DaisyUI
const isTailwind = computed(() => isTailwindColor(effectiveColor.value));
const isCustomCssColor = computed(() => isCssColor(effectiveColor.value));

const inlineStyle = computed(() => {
    const base = (props.style && typeof props.style === 'object') ? props.style : {};

    // 1) Couleur CSS custom => on force background + texte lisible
    if (isCustomCssColor.value) {
        const bg = String(effectiveColor.value);
        const fg = getReadableTextColor(bg);
        return {
            ...base,
            backgroundColor: bg,
            color: fg,
        };
    }

    // 2) Token Tailwind `color-shade` => éviter les classes dynamiques `bg-${color}`
    // (non détectées par Tailwind JIT). On utilise les variables CSS Tailwind.
    if (isTailwind.value) {
        const raw = String(effectiveColor.value);
        const [rawColorName, shadeStr] = raw.split("-");
        // Tolerance: certains utilisent "grey" au lieu de "gray"
        const colorName = rawColorName === "grey" ? "gray" : rawColorName;
        const shade = Number(shadeStr);

        const cssVar = `--color-${colorName}-${shadeStr}`;
        const cssVarText = `--color-${colorName}-950`;

        // Variants outline/dash => pas de background, mais bordure + texte colorés
        const isOutlineLike = props.variant === 'outline' || props.variant === 'dash';
        const isDarkShade = Number.isFinite(shade) ? shade >= 600 : true;

        if (isOutlineLike) {
            return {
                ...base,
                backgroundColor: "transparent",
                borderColor: `var(${cssVar})`,
                color: `var(${cssVar})`,
            };
        }

        const baseStyle = {
            ...base,
            backgroundColor: `var(${cssVar})`,
            color: isDarkShade ? "#ffffff" : `var(${cssVarText})`,
        };

        if (!props.glassy) return baseStyle;

        // Glassy: léger gradient + bordure subtile
        return {
            ...baseStyle,
            backgroundImage: `linear-gradient(135deg, color-mix(in srgb, var(${cssVar}) 92%, transparent), color-mix(in srgb, var(${cssVar}) 70%, transparent))`,
            borderWidth: "1px",
            borderStyle: "solid",
            borderColor: `color-mix(in srgb, var(${cssVar}) 55%, transparent)`,
        };
    }

    // 3) Token DaisyUI => on essaie d'améliorer le contraste en se basant sur la variable thème
    //    (si disponible), sans toucher au background DaisyUI (géré par les classes badge-*).
    //    But: éviter certains thèmes où le text-content est trop faible.
    if (props.color && colorList.includes(props.color) && typeof window !== 'undefined') {
        try {
            const token = String(props.color);
            const cssVar = `--color-${token}-500`;
            const raw = getComputedStyle(document.documentElement).getPropertyValue(cssVar)?.trim();
            if (raw && isValidColor(raw)) {
                const fg = getReadableTextColor(raw);
                return { ...base, color: fg };
            }
        } catch {
            // ignore
        }
    }

    return props.style || "";
});

// Classes de couleur : DaisyUI ou Tailwind
const colorClasses = computed(() => {
    if (!effectiveColor.value) return [];
    
    // Couleur CSS custom => via style inline
    if (isCustomCssColor.value) {
        return [];
    }

    if (isTailwind.value) {
        // Couleur Tailwind : gérée en style inline (voir inlineStyle)
        return [];
    }
    
    // Couleur DaisyUI
    return [
        effectiveColor.value === 'neutral' && 'badge-neutral',
        effectiveColor.value === 'primary' && 'badge-primary',
        effectiveColor.value === 'secondary' && 'badge-secondary',
        effectiveColor.value === 'accent' && 'badge-accent',
        effectiveColor.value === 'info' && 'badge-info',
        effectiveColor.value === 'success' && 'badge-success',
        effectiveColor.value === 'warning' && 'badge-warning',
        effectiveColor.value === 'error' && 'badge-error',
    ].filter(Boolean);
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'badge',
            ...colorClasses.value,
            props.size === 'xs' && 'badge-xs',
            props.size === 'sm' && 'badge-sm',
            props.size === 'md' && 'badge-md',
            props.size === 'lg' && 'badge-lg',
            props.size === 'xl' && 'badge-xl',
            props.variant === 'outline' && 'badge-outline',
            props.variant === 'dash' && 'badge-dash',
            props.variant === 'soft' && 'badge-soft',
            props.variant === 'ghost' && 'badge-ghost',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <span :class="atomClasses" v-bind="attrs" v-on="$attrs" :style="inlineStyle">
        <!-- Priorité : content prop > slot content > slot default -->
        <span v-if="content && !$slots.content && !$slots.default">{{ content }}</span>
        <slot name="content" v-else-if="$slots.content" />
        <slot v-else />
    </span>
</template>

<style scoped></style>
