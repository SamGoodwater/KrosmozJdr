<script setup>
defineOptions({ inheritAttrs: false });

/**
 * FileInput Molecule (DaisyUI + KrosmozJDR)
 *
 * @description
 * Molécule avancée pour l'upload de fichiers, basée sur DaisyUI et Atomic Design.
 * - Utilise FileInputAtom pour l'input natif stylé DaisyUI
 * - Gère drag & drop, preview (image, nom, taille), progress (slot ou prop), suppression, helper text
 * - Props : label, helper, progress, multiple, accept, maxSize, error, disabled, etc.
 * - Slots : default (preview custom), progress, helper, actions, tooltip
 * - mergeClasses pour la composition des classes
 * - getCommonAttrs pour les attributs HTML/accessibilité
 * - Utilise Btn, Progress, etc. si besoin
 * - Tooltip intégré
 * - Pas de logique d'upload (juste UI et gestion du fichier sélectionné)
 *
 * @see https://daisyui.com/components/file-input/
 *
 * @example
 * <FileInput label="Avatar" accept="image/*" :maxSize="2*1024*1024" helper="Max 2Mo" />
 *
 * @props {String} label - Label du champ (optionnel)
 * @props {String} helper - Texte d'aide (optionnel, sinon slot #helper)
 * @props {Boolean} multiple - Sélection multiple
 * @props {String} accept - Types MIME acceptés
 * @props {Number} maxSize - Taille max (en octets)
 * @props {Boolean} disabled - Désactive l'input
 * @props {String} error - Message d'erreur
 * @props {Number} progress - Progression (0-100, optionnel)
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Preview custom du fichier sélectionné
 * @slot progress - Progress bar custom
 * @slot helper - Texte d'aide custom
 * @slot actions - Actions custom (ex: bouton supprimer)
 * @slot tooltip - Tooltip custom
 */
import { ref, computed } from 'vue';
import FileInputAtom from '@/Pages/Atoms/data-input/FileInputAtom.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Progress from '@/Pages/Atoms/feedback/Progress.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { formatSizeToMB } from '@/Utils/file/File';

const props = defineProps({
    ...getCommonProps(),
    label: { type: String, default: '' },
    helper: { type: String, default: '' },
    multiple: { type: Boolean, default: false },
    accept: { type: String, default: '' },
    maxSize: { type: Number, default: 0 },
    error: { type: String, default: '' },
    progress: { type: Number, default: null },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'delete', 'error']);
const fileInputRef = ref(null);
const dragActive = ref(false);
const files = ref([]); // tableau de File

function onInput(e) {
    handleFiles(e.target.files);
}
function onDrop(e) {
    e.preventDefault();
    dragActive.value = false;
    handleFiles(e.dataTransfer.files);
}
function onDragOver(e) {
    e.preventDefault();
    dragActive.value = true;
}
function onDragLeave(e) {
    e.preventDefault();
    dragActive.value = false;
}
function handleFiles(fileList) {
    const arr = Array.from(fileList);
    // Validation taille et accept
    const valid = arr.filter(f => {
        if (props.maxSize && f.size > props.maxSize) {
            emit('error', `Le fichier ${f.name} dépasse la taille maximale autorisée.`);
            return false;
        }
        if (props.accept && !f.type.match(props.accept.replace('*', '.*'))) {
            emit('error', `Le format du fichier ${f.name} n'est pas accepté.`);
            return false;
        }
        return true;
    });
    files.value = props.multiple ? valid : valid.slice(0, 1);
    emit('update:modelValue', props.multiple ? files.value : files.value[0] || null);
}
function onDelete(idx) {
    if (props.multiple) {
        files.value.splice(idx, 1);
        emit('update:modelValue', files.value);
    } else {
        files.value = [];
        emit('update:modelValue', null);
    }
    emit('delete');
}
function triggerInput() {
    fileInputRef.value?.click();
}

const rootClasses = computed(() =>
    mergeClasses([
        'fileinput-molecule',
        dragActive.value && 'ring-2 ring-primary/60',
        props.disabled && 'opacity-60 pointer-events-none',
        props.class
    ])
);
const attrs = computed(() => getCommonAttrs(props));

</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div :class="rootClasses" v-bind="attrs" v-on="$attrs">
            <label v-if="label" class="block mb-2 font-semibold">{{ label }}</label>
            <div class="relative w-full border border-base-300 rounded-box p-4 bg-base-200 transition-all"
                @dragover="onDragOver" @dragleave="onDragLeave" @drop="onDrop"
                :class="{ 'ring-2 ring-primary/60': dragActive }" @click="triggerInput" style="cursor:pointer;">
                <FileInputAtom ref="fileInputRef" :multiple="multiple" :accept="accept" :disabled="disabled"
                    class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                    style="height:100%;width:100%;top:0;left:0;" @input="onInput" />
                <div class="flex flex-col items-center justify-center min-h-[60px]">
                    <slot v-if="files.length && $slots.default" :files="files" />
                    <template v-else-if="files.length">
                        <div v-for="(file, idx) in files" :key="file.name" class="flex items-center gap-2 mb-2">
                            <img v-if="file.type.startsWith('image/')" :src="URL.createObjectURL(file)" alt="preview"
                                class="w-12 h-12 object-cover rounded" />
                            <span class="truncate max-w-xs">{{ file.name }}</span>
                            <span class="text-xs text-base-400">({{ formatSizeToMB(file.size) }} Mo)</span>
                            <Btn size="xs" variant="ghost" color="error" circle @click.stop="onDelete(idx)"
                                :aria-label="'Supprimer'">
                                <i class="fa-solid fa-trash"></i>
                            </Btn>
                        </div>
                    </template>
                    <template v-else>
                        <span class="text-base-400">Glissez-déposez un fichier ou cliquez pour sélectionner</span>
                    </template>
                </div>
                <div v-if="progress !== null || $slots.progress" class="mt-2 w-full">
                    <slot name="progress">
                        <Progress v-if="progress !== null" :value="progress" color="primary" />
                    </slot>
                </div>
                <div v-if="$slots.actions" class="mt-2">
                    <slot name="actions" :files="files" />
                </div>
            </div>
            <div v-if="helper || $slots.helper" class="mt-2 text-sm text-base-500">
                <slot name="helper">{{ helper }}</slot>
            </div>
            <div v-if="error" class="mt-2 text-sm text-error">
                {{ error }}
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped>
.fileinput-molecule {
    width: 100%;
    max-width: 28rem;
}
</style>
