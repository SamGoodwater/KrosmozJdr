<script setup>
/**
 * Resource Show Page
 *
 * @description
 * Page de lecture d'une ressource. ResourceViewFull gère l'affichage complet :
 * image + propriétés, ingrédients, relations, bloc admin.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Resource } from '@/Models/Entity/Resource';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewFullWrapper from '@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue';
import ResourceViewFull from '@/Pages/Molecules/entity/resource/ResourceViewFull.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
});

const resource = computed(() => new Resource(props.resource || page.props.resource || {}));

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

setPageTitle(`Ressource : ${resource.value.name || '-'}`);

const goEdit = () => {
    if (!resource.value.id) return;
    router.visit(route('entities.resources.edit', { resource: resource.value.id }));
};
</script>

<template>
    <Head :title="`Ressource : ${resource?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper
            :show-back-button="true"
            back-route="entities.resources.index"
        >
            <div class="space-y-6">
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

                <ResourceViewFull
                    :resource="resource"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
