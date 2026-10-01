<script setup>
/**
 * Matrice admin : rôle minimal requis pour voir chaque type d’entité selon son état (raw, draft, auto, playable, archived).
 */
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import { usePageForms } from '@/Composables/form/usePageForms';
import { KREF_ENTITY_CONFIGS } from '@/Composables/richText/krefEntityRegistry';

defineOptions({ layout: AdminArea });

const props = defineProps({
    matrix: { type: Object, required: true },
    entityKeys: { type: Array, required: true },
    states: { type: Array, required: true },
    roles: { type: Array, required: true },
});

const labelByEntityType = computed(() => {
    const map = {};
    for (const cfg of KREF_ENTITY_CONFIGS) {
        map[cfg.entityType] = cfg.label;
    }
    return map;
});

/**
 * @param {string} key
 * @returns {string}
 */
function entityLabel(key) {
    return labelByEntityType.value[key] || key;
}

const form = useForm({
    rules: JSON.parse(JSON.stringify(props.matrix)),
});

const pageForms = usePageForms();
pageForms.register('rules', form, (callbacks) =>
    form.patch(route('admin.entity-display-visibility.update'), {
        preserveScroll: true,
        preserveState: true,
        ...callbacks,
    }),
);
</script>

<template>
    <Head title="Affichage des entités" />

    <div class="space-y-6 pb-10">
        <PageHeader title="Affichage des entités" :forms="pageForms">
            <template #subtitle>
                Rôle minimal nécessaire pour <strong>voir</strong> une fiche selon son état. Les administrateurs
                gardent un accès complet ; cette matrice complète les autres règles (niveaux de lecture, etc.).
            </template>
        </PageHeader>

        <form class="space-y-4" @submit.prevent="pageForms.saveAll()">
            <div class="overflow-x-auto rounded-box border border-base-content/10 bg-base-100/60">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Type d’entité</th>
                            <th v-for="s in states" :key="s.value" class="whitespace-nowrap text-center">
                                {{ s.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ek in entityKeys" :key="ek">
                            <th class="align-middle font-medium whitespace-nowrap">{{ entityLabel(ek) }}</th>
                            <td v-for="s in states" :key="`${ek}-${s.value}`" class="align-middle">
                                <select
                                    v-model.number="form.rules[ek][s.value]"
                                    class="select select-bordered select-sm w-full min-w-38"
                                >
                                    <option v-for="r in roles" :key="r.value" :value="r.value">
                                        {{ r.label }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</template>
