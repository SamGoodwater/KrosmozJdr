<script setup>
/**
 * Édition du catalogue de chartes : groupe, entité, filtre optionnel de clés.
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
    group: 'spell',
    entity: '*',
    characteristic_keys: [],
});

function normalizeForPersist() {
    const keys = Array.isArray(localSettings.value.characteristic_keys)
        ? [...localSettings.value.characteristic_keys].map(String).map((s) => s.trim()).filter(Boolean).sort()
        : [];
    const ent = String(localSettings.value.entity ?? '').trim();

    return {
        group: String(localSettings.value.group || 'spell'),
        entity: ent === '' ? '*' : ent,
        characteristic_keys: keys,
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
        const keys = Array.isArray(s.characteristic_keys) ? [...s.characteristic_keys] : [];
        localSettings.value = {
            group: s.group ?? 'spell',
            entity: s.entity ?? '*',
            characteristic_keys: keys,
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

        <div>
            <label class="label"><span class="label-text">Groupe</span></label>
            <select
                class="select select-bordered select-sm w-full"
                v-model="localSettings.group"
            >
                <option value="creature">Créature</option>
                <option value="object">Objet</option>
                <option value="spell">Sort</option>
            </select>
        </div>

        <InputField
            label="Entité"
            v-model="localSettings.entity"
            placeholder="*"
            helper="* = toutes les entités du groupe (ex. item, consumable, monster)."
        />

        <div>
            <label class="label"><span class="label-text">Filtrer par clés (optionnel)</span></label>
            <textarea
                class="textarea textarea-bordered textarea-sm w-full font-mono text-sm min-h-20"
                :value="localSettings.characteristic_keys.join('\n')"
                placeholder="Une clé par ligne"
                @input="
                    localSettings.characteristic_keys = $event.target.value
                        ? $event.target.value
                              .split(/[\n,]+/)
                              .map((s) => s.trim())
                              .filter(Boolean)
                        : []
                "
            />
            <p class="text-xs text-base-content/60 mt-1">
                Laisser vide pour toutes les chartes du groupe. Séparateur : ligne ou virgule.
            </p>
        </div>
    </div>
</template>
