<script setup>
/**
 * Admin Sous-effets — Vue dédiée au référentiel des sous-effets.
 * Liste en lecture (slug, type, template, nb effets associés).
 */
import { Head, Link } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const { setPageTitle } = usePageTitle();
setPageTitle('Sous-effets');

const props = defineProps({
    subEffects: { type: Array, required: true },
});

defineOptions({ layout: Main });
</script>

<template>
    <Head title="Sous-effets" />
    <div class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Sous-effets</h1>
                <p class="text-primary-200 mt-2">
                    Référentiel des atomes d'effet (frapper, soigner, booster…). Utilisés dans les Effets.
                </p>
            </div>
            <Link
                :href="route('admin.effects.index')"
                class="btn btn-outline btn-sm"
            >
                <Icon source="fa-solid fa-bolt" size="sm" class="mr-2" />
                Voir les Effets
            </Link>
        </div>

        <div class="overflow-x-auto rounded-lg border border-base-300 bg-base-100">
            <table class="table table-zebra table-pin-rows">
                <thead>
                    <tr class="bg-base-300/70 text-primary-200">
                        <th class="w-16 font-semibold">ID</th>
                        <th class="font-semibold">Slug</th>
                        <th class="font-semibold">Type</th>
                        <th class="font-semibold">Template</th>
                        <th class="w-24 font-semibold text-right">Effets</th>
                        <th class="w-20 font-semibold">DofusDB</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="s in subEffects"
                        :key="s.id"
                        class="border-b border-base-300/30"
                    >
                        <td class="font-mono text-primary-300">{{ s.id }}</td>
                        <td class="font-mono font-medium text-primary-100">{{ s.slug }}</td>
                        <td class="font-mono text-primary-300">{{ s.type_slug ?? '—' }}</td>
                        <td class="text-primary-200 text-sm max-w-md truncate" :title="s.template_text">
                            {{ s.template_text ?? '—' }}
                        </td>
                        <td class="text-right font-mono text-primary-300">{{ s.effects_count ?? 0 }}</td>
                        <td class="font-mono text-xs text-primary-400">{{ s.dofusdb_effect_id ?? '—' }}</td>
                    </tr>
                    <tr v-if="!subEffects.length">
                        <td colspan="6" class="text-center py-8 text-primary-400 italic">
                            Aucun sous-effet
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
