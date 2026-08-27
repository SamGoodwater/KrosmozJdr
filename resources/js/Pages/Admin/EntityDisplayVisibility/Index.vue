<script setup>
/**
 * Matrice admin : rôle minimal requis pour voir chaque type d’entité selon son état (raw, draft, auto, playable, archived).
 */
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { KREF_ENTITY_CONFIGS } from '@/Composables/richText/krefEntityRegistry';

defineOptions({ layout: AdminArea });

const { setPageTitle } = usePageTitle();
setPageTitle('Affichage des entités');

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

function submit() {
    form.patch(route('admin.entity-display-visibility.update'));
}
</script>

<template>
    <Head title="Affichage des entités" />

    <div class="space-y-6 pb-10">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Gérer l’affichage</h1>
            <p class="mt-2 text-sm text-base-content/70 max-w-3xl">
                Définissez le <strong>rôle minimal</strong> (invité à super admin) nécessaire pour qu’un utilisateur
                puisse <strong>voir</strong> une fiche dans un état donné. Les administrateurs conservent toujours un
                accès complet ; cette matrice complète les autres règles des policies (niveaux de lecture, etc.).
            </p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
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

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">
                    Enregistrer
                </button>
                <span v-if="form.processing" class="text-xs text-base-content/60">Enregistrement…</span>
                <span v-if="form.recentlySuccessful" class="text-xs text-success">Mis à jour.</span>
            </div>
        </form>
    </div>
</template>
