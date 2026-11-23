<script setup>
/**
 * Page d'édition d'un trait de créature.
 *
 * @description
 * Édition sheet (header compact + formulaire descriptor-driven).
 */
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Aptitude } from '@/Models/Entity/Aptitude';
import { getAptitudeFieldDescriptors } from '@/Entities/aptitude/aptitude-descriptors';
import { APTITUDE_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/aptitude/aptitude-form-config';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    aptitude: {
        type: Object,
        required: true,
    },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const aptitude = computed(() => {
    const raw = props.aptitude || page.props.aptitude || {};
    return raw instanceof Aptitude ? raw : new Aptitude(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getAptitudeFieldDescriptors(ctx), ctx);
});

const fieldSections = APTITUDE_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('aptitudes') || isAdmin.value);

setPageTitle(`Modifier l'aptitude : ${aptitude.value.name || 'Sans nom'}`);
</script>

<template>
    <Head :title="`Modifier l'aptitude : ${aptitude?.name || 'Sans nom'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="aptitudes"
            :entity="aptitude"
            :form-ref="entityEditFormRef"
            :title="aptitude.name || 'Aptitude sans nom'"
            list-route-name="entities.aptitudes.index"
            delete-route-name="entities.aptitudes.delete"
            route-param-key="aptitude"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette aptitude ? Il sera placé en corbeille (récupération possible côté admin)."
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="aptitude"
            entity-type="aptitudes"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="sheet"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        />
    </Container>
</template>
