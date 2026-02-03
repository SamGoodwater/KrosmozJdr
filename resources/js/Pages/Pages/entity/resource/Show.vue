<script setup>
/**
 * Resource Show Page
 *
 * @description
 * Page de lecture d'une ressource + affichage des pivots (niveau 1).
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Resource } from '@/Models/Entity/Resource';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ResourceViewLarge from '@/Pages/Molecules/entity/resource/ResourceViewLarge.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const resource = computed(() => new Resource(props.resource || page.props.resource || {}));

setPageTitle(`Ressource : ${resource.value.name || '-'}`);

const goEdit = () => {
    if (!resource.value.id) return;
    router.visit(route('entities.resources.edit', { resource: resource.value.id }));
};
</script>

<template>
    <Head :title="`Ressource : ${resource?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">{{ resource.name }}</h1>
                <p class="text-primary-300 mt-1">
                    {{ resource.resourceType?.name || '—' }}
                </p>
            </div>
            <Btn v-if="resource?.can?.update" color="primary" @click="goEdit">
                <i class="fa-solid fa-pen mr-2"></i>
                Modifier
            </Btn>
        </div>

        <ResourceViewLarge :resource="resource" :show-actions="true" />

        <!-- Pivots niveau 1: afficher les quantités quand présentes -->
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Recette (ingrédients)</h2>
                <ul v-if="(resource.recipeIngredients || []).length" class="space-y-1 text-sm">
                    <li v-for="ing in resource.recipeIngredients" :key="ing.id" class="flex justify-between gap-3">
                        <span>{{ ing.name }}</span>
                        <span class="text-primary-300">x{{ ing.pivot?.quantity ?? 0 }}</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucun ingrédient (ressource non craftable).</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Objets (recettes)</h2>
                <ul v-if="(resource.items || []).length" class="space-y-1 text-sm">
                    <li v-for="it in resource.items" :key="it.id" class="flex justify-between gap-3">
                        <span>{{ it.name }}</span>
                        <span class="text-primary-300">x{{ it.pivot?.quantity ?? 0 }}</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucun objet lié.</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Consommables</h2>
                <ul v-if="(resource.consumables || []).length" class="space-y-1 text-sm">
                    <li v-for="c in resource.consumables" :key="c.id" class="flex justify-between gap-3">
                        <span>{{ c.name }}</span>
                        <span class="text-primary-300">x{{ c.pivot?.quantity ?? 0 }}</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucun consommable lié.</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Créatures</h2>
                <ul v-if="(resource.creatures || []).length" class="space-y-1 text-sm">
                    <li v-for="cr in resource.creatures" :key="cr.id" class="flex justify-between gap-3">
                        <span>{{ cr.name }}</span>
                        <span class="text-primary-300">x{{ cr.pivot?.quantity ?? 0 }}</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucune créature liée.</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Boutiques</h2>
                <ul v-if="(resource.shops || []).length" class="space-y-2 text-sm">
                    <li v-for="s in resource.shops" :key="s.id" class="flex justify-between gap-3">
                        <div>
                            <div class="font-medium">{{ s.name }}</div>
                            <div v-if="s.pivot?.comment" class="text-xs text-primary-300">{{ s.pivot.comment }}</div>
                        </div>
                        <div class="text-right text-primary-300">
                            <div>x{{ s.pivot?.quantity ?? 0 }}</div>
                            <div v-if="typeof s.pivot?.price !== 'undefined'" class="text-xs">prix: {{ s.pivot.price }}</div>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucune boutique liée.</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Scénarios</h2>
                <ul v-if="(resource.scenarios || []).length" class="space-y-1 text-sm">
                    <li v-for="sc in resource.scenarios" :key="sc.id">
                        {{ sc.name }}
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucun scénario lié.</p>
            </div>

            <div class="rounded-lg border border-base-300 p-4">
                <h2 class="font-semibold text-primary-100 mb-2">Campagnes</h2>
                <ul v-if="(resource.campaigns || []).length" class="space-y-1 text-sm">
                    <li v-for="ca in resource.campaigns" :key="ca.id">
                        {{ ca.name }}
                    </li>
                </ul>
                <p v-else class="text-sm text-primary-300 italic">Aucune campagne liée.</p>
            </div>
        </div>
    </Container>
</template>


