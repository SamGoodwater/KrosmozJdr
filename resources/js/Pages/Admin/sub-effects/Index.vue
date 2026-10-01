<script setup>
/**
 * Admin Sous-effets — Vue dédiée au référentiel des sous-effets.
 * Liste en lecture (slug, type, template, nb effets associés).
 */
import { Head } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';

defineProps({
    subEffects: { type: Array, required: true },
});

defineOptions({ layout: AdminArea });
</script>

<template>
    <Head title="Sous-effets" />
    <div class="space-y-6 pb-8">
        <PageHeader
            title="Sous-effets"
            subtitle="Référentiel des atomes d'effet (frapper, soigner, booster…). Utilisés dans les Effets."
        >
            <template #actions>
                <Route :href="route('admin.effects.index')" class="no-underline">
                    <Btn variant="ghost" size="sm" type="button" class="gap-1.5">
                        <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                        Voir les effets
                    </Btn>
                </Route>
            </template>
        </PageHeader>

        <div class="overflow-x-auto rounded-box border border-base-300 bg-base-100">
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
