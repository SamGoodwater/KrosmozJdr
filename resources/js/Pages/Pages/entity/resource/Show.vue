<script setup>
/**
 * Resource Show Page
 *
 * @description
 * Page de lecture d'une ressource. ResourceViewLarge gère l'affichage complet :
 * image + propriétés, ingrédients, relations, bloc admin.
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
    </Container>
</template>
