<script setup>
/**
 * Panneau 3 — Mapping : règles DofusDB → Krosmoz qui utilisent cette caractéristique.
 * Affichage en lecture seule + lien vers l’écran Mappings.
 */
import { Link } from '@inertiajs/vue3';

defineProps({
    /** Règles de mapping (source, entity, mapping_key, from_path, targets) */
    scrappingMappingsUsingThis: { type: Array, default: () => [] },
});
</script>

<template>
    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-base-content border-b border-base-300 pb-2">Panneau 3 — Mapping</h2>
        <div class="card shadow border border-base-200 border-glass-sm">
            <div class="card-body bg-base-100 rounded-lg">
                <p class="text-sm text-base-content/70 mb-4">
                    Règles de mapping qui lient une propriété de l’API DofusDB à cette caractéristique KrosmozJDR. Chaque règle est unique par entité (monster, spell, item, etc.). Pour créer ou modifier les règles, utilisez l’écran Mappings.
                </p>
                <Link
                    :href="route('admin.scrapping-mappings.index')"
                    class="btn btn-primary btn-sm gap-2 mb-4"
                >
                    <i class="fa fa-external-link-alt" />
                    Gérer les mappings (DofusDB → Krosmoz)
                </Link>
                <div v-if="scrappingMappingsUsingThis?.length" class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th class="bg-base-300">Source</th>
                                <th class="bg-base-300">Entité</th>
                                <th class="bg-base-300">Clé</th>
                                <th class="bg-base-300">Chemin DofusDB</th>
                                <th class="bg-base-300">Cibles Krosmoz</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in scrappingMappingsUsingThis" :key="m.id">
                                <td><code class="text-xs">{{ m.source }}</code></td>
                                <td><code class="text-xs">{{ m.entity }}</code></td>
                                <td><code class="text-xs">{{ m.mapping_key }}</code></td>
                                <td><code class="text-xs break-all">{{ m.from_path }}</code></td>
                                <td>
                                    <span v-for="(t, i) in (m.targets || [])" :key="i" class="badge badge-ghost badge-sm mr-1">{{ t.model }}.{{ t.field }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-sm text-base-content/60 italic">
                    Aucune règle de mapping n’utilise cette caractéristique pour l’instant.
                </p>
            </div>
        </div>
    </section>
</template>
