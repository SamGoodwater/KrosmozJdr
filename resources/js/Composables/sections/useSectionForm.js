/**
 * Composable pour gérer la logique de formulaire des sections (création et édition).
 * Centralise la gestion du formulaire Inertia, les options des selects, et la logique partagée.
 *
 * @param {Object} options - Options de configuration
 * @param {Boolean} options.isEdit - Indique si le formulaire est en mode édition
 * @param {Object|ComputedRef|null} options.initialSectionData - Données initiales de la section (pour l'édition)
 * @param {Number|String} options.pageId - ID de la page parente
 * @param {Function} options.onSuccess - Callback en cas de succès de la soumission
 * @param {Function} options.onClose - Callback lors de la fermeture du modal
 * @returns {Object} { form, submit, handleClose, initializeForm, visibilityOptions, stateOptions, templateOptions }
 */
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { SectionParameterService } from '@/Utils/Services';
import { useTemplateRegistry } from '@/Pages/Organismes/section/composables/useTemplateRegistry';
import { Visibility } from '@/Utils/enums/Visibility';
import { PageState } from '@/Utils/enums/PageState';

export function useSectionForm({ isEdit, initialSectionData, pageId, onSuccess, onClose }) {
    const registry = useTemplateRegistry();

    // Options pour les selects
    const visibilityOptions = computed(() => SectionParameterService.getVisibilityOptions());
    const stateOptions = computed(() => SectionParameterService.getStateOptions());
    const templateOptions = computed(() => registry.getOptions());

    // Formulaire Inertia
    const form = useForm({
        page_id: pageId || null,
        title: '',
        slug: '',
        order: 0,
        template: null,
        settings: {},
        data: {},
        is_visible: Visibility.GUEST.value,
        can_edit_role: Visibility.ADMIN.value,
        state: PageState.DRAFT.value,
    });

    /**
     * Initialise le formulaire avec les données de la section existante (mode édition)
     * ou réinitialise pour la création.
     */
    const initializeForm = () => {
        if (isEdit && initialSectionData?.value) {
            const section = initialSectionData.value;
            form.page_id = section.page_id || pageId;
            form.title = section.title || '';
            form.slug = section.slug || '';
            form.order = section.order || 0;
            form.template = section.template || null;
            form.settings = section.settings || {};
            form.data = section.data || {};
            form.is_visible = section.is_visible || Visibility.GUEST.value;
            form.can_edit_role = section.can_edit_role || Visibility.ADMIN.value;
            form.state = section.state || PageState.DRAFT.value;
        } else {
            // Mode création : valeurs par défaut
            form.reset();
            form.clearErrors();
            form.page_id = pageId;
            
            // Si un template est fourni, récupérer ses defaults
            if (form.template) {
                const defaults = registry.getDefaults(form.template);
                form.settings = defaults.settings || {};
                form.data = defaults.data || {};
            }
        }
    };

    /**
     * Met à jour les settings d'un paramètre spécifique
     * @param {String} key - Clé du paramètre
     * @param {*} value - Nouvelle valeur
     */
    const updateSetting = (key, value) => {
        form.settings = {
            ...form.settings,
            [key]: value
        };
    };

    /**
     * Met à jour les data d'un champ spécifique
     * @param {String} key - Clé du champ
     * @param {*} value - Nouvelle valeur
     */
    const updateData = (key, value) => {
        form.data = {
            ...form.data,
            [key]: value
        };
    };

    /**
     * Initialise settings et data depuis un template
     * @param {String} templateValue - Valeur du template
     */
    const initializeFromTemplate = (templateValue) => {
        if (!templateValue) return;

        const defaults = registry.getDefaults(templateValue);
        form.template = templateValue;
        form.settings = defaults.settings || {};
        form.data = defaults.data || {};
    };

    // Soumission du formulaire
    const submit = () => {
        const routeName = isEdit ? 'sections.update' : 'sections.store';
        const routeParams = isEdit ? { section: initialSectionData.value.id } : {};
        const method = isEdit ? 'patch' : 'post';

        form[method](route(routeName, routeParams), {
            preserveScroll: true,
            onSuccess: () => {
                onSuccess();
            },
            onError: (errors) => {
                console.error('Form submission error:', errors);
            }
        });
    };

    // Gérer la fermeture du modal/formulaire
    const handleClose = () => {
        form.reset();
        form.clearErrors();
        onClose();
    };

    return {
        form,
        submit,
        handleClose,
        initializeForm,
        updateSetting,
        updateData,
        initializeFromTemplate,
        visibilityOptions,
        stateOptions,
        templateOptions,
    };
}
