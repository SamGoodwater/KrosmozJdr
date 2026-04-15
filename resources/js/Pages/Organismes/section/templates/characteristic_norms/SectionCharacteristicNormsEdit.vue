<script setup>
/**
 * Édition de la configuration d'une section CharacteristicNorms.
 * Sauvegarde via useSectionSave (aligné sur SectionEntityTableEdit).
 */
import { ref, watch, nextTick } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import InlineSaveStatus from '@/Pages/Atoms/feedback/InlineSaveStatus.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

const syncFromProps = ref(false);
const lastPersistSignature = ref('');
const saveState = ref('idle');
let saveStateTimer = null;

function setSaveState(state) {
    saveState.value = state;
    if (saveStateTimer) {
        clearTimeout(saveStateTimer);
        saveStateTimer = null;
    }
    if (state === 'saved') {
        saveStateTimer = setTimeout(() => {
            saveState.value = 'idle';
        }, 1600);
    }
}

const localSettings = ref({
    characteristic_key: '',
    group: 'creature',
    entity: '*',
});

function normalizeForPersist() {
    const ent = String(localSettings.value.entity ?? '').trim();
    return {
        characteristic_key: String(localSettings.value.characteristic_key || '').trim(),
        group: String(localSettings.value.group || 'creature'),
        entity: ent === '' ? '*' : ent,
    };
}

function safeStringify(obj) {
    try {
        return JSON.stringify(obj);
    } catch {
        return '';
    }
}

function persist() {
    const sectionId = props.section?.id;
    if (!sectionId || syncFromProps.value) {
        return;
    }

    const normalized = normalizeForPersist();
    const signature = safeStringify(normalized);
    if (signature === lastPersistSignature.value) {
        return;
    }
    lastPersistSignature.value = signature;

    saveSection(
        sectionId,
        {
            settings: {
                ...props.settings,
                ...normalized,
            },
        },
        {
            onQueued: () => setSaveState('saving'),
            onSuccess: () => {
                setSaveState('saved');
                emit('data-updated');
            },
            onError: () => setSaveState('error'),
        }
    );
}

watch(
    () => props.settings,
    async (s) => {
        if (!s) {
            return;
        }
        syncFromProps.value = true;
        localSettings.value = {
            characteristic_key: s.characteristic_key ?? '',
            group: s.group ?? 'creature',
            entity: s.entity ?? '*',
        };
        await nextTick();
        lastPersistSignature.value = safeStringify(normalizeForPersist());
        syncFromProps.value = false;
    },
    { deep: true, immediate: true }
);

watch(localSettings, () => persist(), { deep: true });
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-end">
            <InlineSaveStatus :state="saveState" />
        </div>

        <InputField
            label="Clé de la caractéristique"
            v-model="localSettings.characteristic_key"
            placeholder="strength_creature"
            helper="Clé unique, visible dans l'administration des caractéristiques."
        />

        <div>
            <label class="label"><span class="label-text">Groupe</span></label>
            <select v-model="localSettings.group" class="select select-bordered select-sm w-full">
                <option value="creature">Créature</option>
                <option value="object">Objet</option>
                <option value="spell">Sort</option>
            </select>
        </div>

        <InputField
            label="Entité"
            v-model="localSettings.entity"
            placeholder="*"
            helper="* = toutes les entités du groupe. Sinon : monster, class, item, spell, etc."
        />
    </div>
</template>
