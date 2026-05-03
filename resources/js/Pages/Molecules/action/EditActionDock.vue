<script setup>
/**
 * EditActionDock — Actions d’édition (Enregistrer + Annuler / Reset / custom).
 *
 * @description
 * Mode **flottant** (`fixedOnDesktop` true) : groupe compact en bas à droite du viewport,
 * sans barre pleine largeur ni fond de conteneur ; secondaires au survol / focus.
 * Mode **inline** (`fixedOnDesktop` false) : flux dans une carte / bloc (ex. prix item).
 *
 * **Ne pas utiliser dans une modale** pour le mode flottant : préférer {@link EntityEditForm}
 * avec `embedded-in-modal` ou des {@link Btn} compacts dans le `Modal`.
 *
 * @props {Boolean} fixedOnDesktop - true : coin bas-droit fixe ; false : aligné à droite dans le flux.
 */
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

defineProps({
    primaryLabel: {
        type: String,
        required: true,
    },
    processingLabel: {
        type: String,
        default: 'Enregistrement...',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    showSecondary: {
        type: Boolean,
        default: true,
    },
    secondaryActions: {
        type: Array,
        default: () => [],
    },
    fixedOnDesktop: {
        type: Boolean,
        default: true,
    },
    rootClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['primary', 'action']);

/**
 * @param {{ variant?: string }} act
 * @returns {string}
 */
function secondaryVariant(act) {
    return act?.variant && String(act.variant).trim() !== '' ? act.variant : 'outline';
}

/**
 * @param {{ color?: string }} act
 * @returns {string}
 */
function secondaryColor(act) {
    return act?.color && String(act.color).trim() !== '' ? act.color : '';
}
</script>

<template>
    <!-- Mode flottant : une seule ligne / groupe en bas à droite, pas de fond de conteneur -->
    <div
        v-if="fixedOnDesktop"
        :class="[
            'edit-action-dock',
            'group',
            'pointer-events-auto',
            'fixed',
            'bottom-4',
            'right-4',
            'z-50',
            'flex',
            'flex-row-reverse',
            'flex-wrap',
            'items-center',
            'justify-end',
            'gap-2',
            'max-w-[calc(100vw-2rem)]',
            rootClass,
        ]"
    >
        <Btn
            type="button"
            color="primary"
            class="shadow-lg"
            :disabled="disabled || processing"
            @click="emit('primary')"
        >
            <i class="fa-solid fa-save mr-2"></i>
            {{ processing ? processingLabel : primaryLabel }}
        </Btn>

        <div
            v-if="showSecondary && secondaryActions.length"
            class="flex max-w-0 flex-row items-center gap-2 overflow-hidden opacity-0 transition-all duration-200 ease-out pointer-events-none group-hover:max-w-md group-hover:opacity-100 group-hover:pointer-events-auto group-focus-within:max-w-md group-focus-within:opacity-100 group-focus-within:pointer-events-auto"
        >
            <template v-for="act in secondaryActions" :key="act.key">
                <Tooltip
                    v-if="act.tooltip"
                    :content="act.tooltip"
                    placement="top"
                >
                    <Btn
                        type="button"
                        :variant="secondaryVariant(act)"
                        :color="secondaryColor(act)"
                        :disabled="act.disabled || processing"
                        class="shadow-lg"
                        @click="emit('action', act.key)"
                    >
                        <i
                            v-if="act.iconClass"
                            :class="act.iconClass"
                        ></i>
                        {{ act.label }}
                    </Btn>
                </Tooltip>
                <Btn
                    v-else
                    type="button"
                    :variant="secondaryVariant(act)"
                    :color="secondaryColor(act)"
                    :disabled="act.disabled || processing"
                    class="shadow-lg"
                    @click="emit('action', act.key)"
                >
                    <i
                        v-if="act.iconClass"
                        :class="act.iconClass"
                    ></i>
                    {{ act.label }}
                </Btn>
            </template>
        </div>

        <slot name="secondary-mobile" />
    </div>

    <!-- Mode inline : carte / section (pas de fixed) -->
    <div
        v-else
        :class="['edit-action-dock', 'flex', 'w-full', 'flex-wrap', 'items-center', 'justify-end', 'gap-2', rootClass]"
    >
        <template v-for="act in secondaryActions" :key="act.key">
            <Tooltip
                v-if="act.tooltip"
                :content="act.tooltip"
                placement="top"
            >
                <Btn
                    type="button"
                    :variant="secondaryVariant(act)"
                    :color="secondaryColor(act)"
                    :disabled="act.disabled || processing"
                    class="shadow-lg"
                    @click="emit('action', act.key)"
                >
                    <i
                        v-if="act.iconClass"
                        :class="act.iconClass"
                    ></i>
                    {{ act.label }}
                </Btn>
            </Tooltip>
            <Btn
                v-else
                type="button"
                :variant="secondaryVariant(act)"
                :color="secondaryColor(act)"
                :disabled="act.disabled || processing"
                class="shadow-lg"
                @click="emit('action', act.key)"
            >
                <i
                    v-if="act.iconClass"
                    :class="act.iconClass"
                ></i>
                {{ act.label }}
            </Btn>
        </template>
        <slot name="secondary-mobile" />
        <Btn
            type="button"
            color="primary"
            class="shadow-lg"
            :disabled="disabled || processing"
            @click="emit('primary')"
        >
            <i class="fa-solid fa-save mr-2"></i>
            {{ processing ? processingLabel : primaryLabel }}
        </Btn>
    </div>
</template>
