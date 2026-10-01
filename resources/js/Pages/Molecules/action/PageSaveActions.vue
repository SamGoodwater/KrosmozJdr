<script setup>
/**
 * PageSaveActions — Bloc « Annuler les modifications » + « Enregistrer » d’une page.
 *
 * @description
 * Utilisé par défaut dans {@link PageHeader} quand la page passe un `usePageForms`.
 * « Annuler les modifications » n’apparaît que s’il y a des modifications ; « Enregistrer »
 * est désactivé tant que rien n’a changé (sauf `alwaysEnabled`). Ctrl+S / Cmd+S déclenche
 * l’enregistrement tant que le composant est monté.
 *
 * @example
 * <PageSaveActions :dirty="forms.isDirty.value" :processing="forms.processing.value"
 *     :status="forms.status.value" @save="forms.saveAll()" @discard="forms.discardAll()" />
 *
 * @props {Boolean} dirty - Des modifications sont en attente
 * @props {Boolean} processing - Enregistrement en cours
 * @props {Boolean} disabled - Bloque l’enregistrement (ex. formulaire invalide)
 * @props {Boolean} alwaysEnabled - « Enregistrer » actif même sans modification détectée
 * @props {String} status - idle | saving | saved | error (indicateur inline)
 * @props {String} saveLabel - Libellé principal (défaut : « Enregistrer »)
 * @props {Boolean} shortcut - Active Ctrl+S / Cmd+S (défaut : true)
 * @emits save, discard
 */
import { onBeforeUnmount, onMounted } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { ACTION, UNSAVED } from '@/Utils/atomic-design/actionLabels';
import { registerSaveShortcut } from '@/Composables/utils/saveShortcutRegistry';

const props = defineProps({
    dirty: { type: Boolean, default: false },
    processing: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    alwaysEnabled: { type: Boolean, default: false },
    status: {
        type: String,
        default: 'idle',
        validator: (v) => ['idle', 'saving', 'saved', 'error'].includes(v),
    },
    saveLabel: { type: String, default: ACTION.save.label },
    shortcut: { type: Boolean, default: true },
});

const emit = defineEmits(['save', 'discard']);

function canSave() {
    return !props.disabled && !props.processing && (props.dirty || props.alwaysEnabled);
}

function onSave() {
    if (canSave()) {
        emit('save');
    }
}

let unregister = () => {};
onMounted(() => {
    if (props.shortcut) {
        unregister = registerSaveShortcut(() => onSave());
    }
});
onBeforeUnmount(() => unregister());
</script>

<template>
    <div class="page-save-actions flex flex-wrap items-center justify-end gap-2">
        <span
            v-if="dirty && !processing"
            class="badge badge-warning badge-soft badge-sm"
            data-testid="page-save-dirty"
        >
            {{ UNSAVED.badge }}
        </span>
        <InlineSaveStatus v-else-if="status === 'saved' || status === 'error'" :state="status" />
        <Btn
            v-if="dirty"
            type="button"
            variant="ghost"
            size="sm"
            :disabled="processing"
            data-testid="page-save-discard"
            @click="emit('discard')"
        >
            <i :class="ACTION.discard.icon" class="mr-1.5" aria-hidden="true"></i>
            {{ ACTION.discard.label }}
        </Btn>
        <Btn
            type="button"
            color="primary"
            size="sm"
            :disabled="!canSave()"
            data-testid="page-save-primary"
            @click="onSave"
        >
            <i :class="ACTION.save.icon" class="mr-1.5" aria-hidden="true"></i>
            {{ processing ? ACTION.save.processing : saveLabel }}
        </Btn>
    </div>
</template>
