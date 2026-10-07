<script setup>
/**
 * Contexte état (condition) pour appliquer-état.
 */
import { ref } from 'vue';
import axios from 'axios';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    conditionId: { type: [Number, String, null], default: null },
    conditionName: { type: String, default: '' },
    dispellable: { type: Boolean, default: false },
});

const emit = defineEmits(['update:conditionId', 'update:conditionMeta', 'update:dispellable', 'dirty']);

const notificationStore = useNotificationStore();
const creating = ref(false);
const query = ref('');

async function createFromQuery() {
    const name = String(query.value ?? '').trim();
    if (!name || creating.value) return;
    creating.value = true;
    try {
        const { data } = await axios.post(route('entities.conditions.store'), {
            name,
            description: null,
            state: 'playable',
            read_level: 0,
            write_level: 4,
        });
        emit('update:conditionId', data.id);
        emit('update:conditionMeta', {
            condition_dofusdb_id: data.dofusdb_id ?? '',
            condition_name: data.name ?? name,
        });
        query.value = '';
        notificationStore.success('État créé et sélectionné.', {
            duration: 2500,
            placement: 'top-right',
        });
        emit('dirty');
    } catch {
        notificationStore.error('Impossible de créer l’état.', {
            duration: 5000,
            placement: 'top-center',
        });
    } finally {
        creating.value = false;
    }
}
</script>

<template>
    <div class="space-y-2 max-w-xl">
        <label class="text-xs font-medium text-base-content/80">État</label>
        <EntityPickerCore
            entity-type="conditions"
            :model-value="conditionId || null"
            variant="compact"
            placeholder="Rechercher un état…"
            @update:model-value="
                emit('update:conditionId', $event);
                emit('dirty');
            "
        />
        <div class="flex flex-wrap items-end gap-2">
            <input
                v-model="query"
                type="search"
                class="input input-bordered input-sm flex-1 min-w-40"
                placeholder="Ou créer : nom de l’état…"
                autocomplete="off"
            />
            <button
                type="button"
                class="btn btn-outline btn-sm"
                :disabled="creating || !query.trim()"
                @click="createFromQuery"
            >
                Créer
            </button>
        </div>
        <p v-if="conditionName" class="text-xs text-base-content/70">
            Sélection : {{ conditionName }}
        </p>
        <label class="flex items-center gap-2 cursor-pointer">
            <input
                type="checkbox"
                class="checkbox checkbox-sm"
                :checked="dispellable"
                @change="
                    emit('update:dispellable', $event.target.checked);
                    emit('dirty');
                "
            />
            <span class="text-xs">Dissipable</span>
        </label>
        <p class="text-xs text-base-content/70">
            L’état appliqué à la cible. Créable à la volée s’il n’existe pas encore.
        </p>
    </div>
</template>
