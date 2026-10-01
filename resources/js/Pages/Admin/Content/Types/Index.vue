<script setup>
/**
 * Registres de types — page commune (sous-menu équipements / ressources / consommables / races / sorts).
 */
import { computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AdminArea from "@/Pages/Layouts/AdminArea.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";
import TypeManagerTable from "@/Pages/Organismes/type-management/TypeManagerTable.vue";
import { TYPE_REGISTRY_KINDS, getTypeRegistryKind } from "@/Utils/content/typeRegistryKinds";

defineOptions({ layout: AdminArea });

const props = defineProps({
    kind: { type: String, required: true },
    can: { type: Object, default: () => ({}) },
});

const current = computed(() => getTypeRegistryKind(props.kind) || TYPE_REGISTRY_KINDS[0]);

/**
 * @param {string} key
 */
function visitKind(key) {
    if (key === props.kind) {
        return;
    }
    router.visit(route("admin.content.types.show", { kind: key }), { preserveState: false });
}
</script>

<template>
    <Head :title="current.title" />

    <Container class="space-y-6 pb-8">
        <PageHeader :title="current.title" :subtitle="current.description">
            <template #tabs>
                <nav class="flex flex-wrap gap-2" aria-label="Registres de types">
                    <Btn
                        v-for="item in TYPE_REGISTRY_KINDS"
                        :key="item.key"
                        size="sm"
                        :color="item.key === kind ? 'primary' : ''"
                        :variant="item.key === kind ? 'glass' : 'outline'"
                        :aria-current="item.key === kind ? 'page' : undefined"
                        @click="visitKind(item.key)"
                    >
                        <Icon :source="item.icon" pack="solid" size="sm" class="mr-2" alt="" />
                        {{ item.shortTitle }}
                    </Btn>
                </nav>
            </template>
        </PageHeader>

        <TypeManagerTable
            :key="current.key"
            :title="current.title"
            :list-url="current.listUrl"
            :bulk-url="current.bulkUrl"
            :delete-url-base="current.deleteUrlBase"
            :move-category-url-base="current.canMove ? current.moveCategoryUrlBase : ''"
            :current-category="current.currentCategory"
            :spell-type-name-cell="current.spellTypeNameCell"
            :has-dofus-meta="current.hasDofusMeta"
        />
    </Container>
</template>
