<script setup>
/**
 * Admin Effects — Données communes (nom, groupe, cible, description) puis onglets par degré
 * (slug, zone, sous-effets). « Ajouter un degré » duplique l’effet courant côté serveur.
 */
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { usePageForms } from '@/Composables/form/usePageForms';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import { ACTION } from '@/Utils/atomic-design/actionLabels';
import SidebarNav from '@/Pages/Organismes/layout/SidebarNav.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import EffectGroupEditorForm from '@/Pages/Organismes/entity/EffectGroupEditorForm.vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import { AREA_NOTATION_HELP, isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';

const props = defineProps({
    effects: { type: Array, required: true },
    groups: { type: Array, default: () => [] },
    selected: { type: [Object, String], default: null },
    /** Effets du même groupe (même ordre que les degrés), pour l’édition groupée */
    groupEffects: { type: Array, default: null },
    options: {
        type: Object,
        default: () => ({ effect_groups: [], sub_effects: [], scopes: [], characteristics: [], monsters: [] }),
    },
});

defineOptions({ layout: AdminArea });

const page = usePage();
const adminUnlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showAdminConfirmModal = ref(false);
function onAdminPasswordConfirmed() {
    adminUnlocked.value = true;
}

const isGroupEdit = computed(
    () => Boolean(props.selected && typeof props.selected === 'object' && props.selected.id && props.groupEffects?.length)
);

const groupEditorRef = ref(null);

const pageTitle = computed(() => {
    if (props.selected === 'new') return 'Nouvel effet';
    if (props.selected && typeof props.selected === 'object') {
        return props.selected.name || props.selected.slug || 'Effet';
    }
    return 'Effets';
});

/** Pont entre l’éditeur de groupe (état interne) et l’enregistrement de la page. */
const groupEditorForm = {
    get isDirty() {
        return Boolean(groupEditorRef.value?.isDirty);
    },
    reset() {
        groupEditorRef.value?.resetToSaved?.();
    },
    clearErrors() {},
};

const pageForms = usePageForms();
pageForms.register('group', groupEditorForm, (callbacks) => {
    const editor = groupEditorRef.value;
    if (!editor) {
        callbacks.onCancel();
        return;
    }
    editor
        .submitGroupAsync()
        .then((result) => (result?.ok ? callbacks.onSuccess() : callbacks.onError({ area: result?.reason })))
        .catch((error) => callbacks.onError(error?.errors));
});

const TARGET_TYPE_OPTIONS = [
    { value: 'direct', label: 'Direct' },
    { value: 'trap', label: 'Piège' },
    { value: 'glyph', label: 'Glyphe' },
];

function buildFormData(selected) {
    if (!selected || selected === 'new') {
        return {
            name: '',
            slug: '',
            description: '',
            target_type: 'direct',
            initial_area: '',
            initial_required_creature_level: '',
        };
    }
    return {
        name: selected.name ?? '',
        slug: selected.slug ?? '',
        description: selected.description ?? '',
        target_type: selected.target_type ?? 'direct',
        initial_area: '',
        initial_required_creature_level: '',
    };
}

const form = useForm(buildFormData(props.selected));
const duplicateForm = useForm({});

const initialAreaValidation = computed(() => {
    const raw = form.initial_area;
    if (raw == null || String(raw).trim() === '') return undefined;
    return isValidAreaNotation(raw)
        ? undefined
        : { state: 'error', message: `Notation invalide. ${AREA_NOTATION_HELP}` };
});

watch(
    () => props.selected,
    () => {
        if (isGroupEdit.value) {
            return;
        }
        const s = props.selected;
        const data = buildFormData(s);
        form.name = data.name;
        form.slug = data.slug;
        form.description = data.description;
        form.target_type = data.target_type;
        form.initial_area = data.initial_area;
        form.initial_required_creature_level = data.initial_required_creature_level;
    },
    { immediate: true }
);

function submit() {
    if (props.selected === 'new') {
        if (form.initial_area != null && String(form.initial_area).trim() !== '' && !isValidAreaNotation(form.initial_area)) {
            return;
        }
        const payload = {
            name: form.name || null,
            slug: form.slug || null,
            description: form.description || null,
            target_type: form.target_type || 'direct',
            initial_area: form.initial_area || null,
            initial_required_creature_level:
                form.initial_required_creature_level !== '' && form.initial_required_creature_level != null
                    ? Number(form.initial_required_creature_level)
                    : null,
        };
        form.transform(() => payload).post(route('admin.effects.store'));
    }
}

function duplicateDegree() {
    if (!props.selected?.id || props.selected === 'new') return;
    duplicateForm.post(route('admin.effects.duplicate-degree', props.selected.id));
}

/** Supprime toute la définition d’effet (tous les degrés, liaison sorts, etc.). */
function destroyDefinition() {
    if (!props.selected?.id) return;
    if (
        confirm(
            'Supprimer entièrement cette définition d’effet ? Tous les degrés et leurs sous-effets seront supprimés. Les sorts liés perdront cette définition.'
        )
    ) {
        form.delete(route('admin.effects.destroy', props.selected.id));
    }
}

function duplicateEffect() {
    if (!props.selected?.id) return;
    duplicateForm.post(route('admin.effects.duplicate', props.selected.id));
}
</script>

<template>
    <Head :title="pageTitle" />

    <PageHeader :title="pageTitle" :forms="isGroupEdit ? pageForms : null">
        <template v-if="adminUnlocked && isGroupEdit" #subtitle>
            Champs communs à tous les degrés, puis un onglet par degré (slug, <strong>zone</strong>, sous-effets et
            <strong>niveau de créature requis</strong> peuvent différer).
        </template>
        <template v-else-if="adminUnlocked && selected === 'new'" #subtitle>
            Un premier degré (D1) est créé automatiquement. Les sous-effets s’ajoutent après la création.
        </template>
        <template v-if="adminUnlocked && isGroupEdit" #actions>
            <Btn
                type="button"
                variant="ghost"
                size="sm"
                :disabled="duplicateForm.processing || groupEditorRef?.saving"
                @click="duplicateDegree"
            >
                <i :class="ACTION.create.icon" class="mr-1.5" aria-hidden="true"></i>
                Ajouter un degré
            </Btn>
            <Btn
                type="button"
                variant="ghost"
                size="sm"
                :disabled="duplicateForm.processing || groupEditorRef?.saving"
                @click="duplicateEffect"
            >
                <i class="fa-solid fa-copy mr-1.5" aria-hidden="true"></i>
                Dupliquer l’effet
            </Btn>
            <Btn
                type="button"
                variant="ghost"
                color="error"
                size="sm"
                :disabled="form.processing || groupEditorRef?.saving"
                @click="destroyDefinition"
            >
                <i :class="ACTION.delete.icon" class="mr-1.5" aria-hidden="true"></i>
                {{ ACTION.delete.label }}
            </Btn>
        </template>
        <template v-if="!adminUnlocked" #primary>
            <Btn color="primary" size="sm" @click="showAdminConfirmModal = true">
                <i :class="ACTION.confirm.icon" class="mr-1.5" aria-hidden="true"></i>
                {{ ACTION.confirm.label }}
            </Btn>
        </template>
        <template v-else-if="selected === 'new'" #primary>
            <Btn color="primary" size="sm" :disabled="form.processing" @click="submit">
                <i :class="ACTION.create.icon" class="mr-1.5" aria-hidden="true"></i>
                {{ form.processing ? ACTION.create.processing : ACTION.create.label }}
            </Btn>
        </template>
    </PageHeader>

    <div
        v-if="!adminUnlocked"
        class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm"
    >
        La définition des effets modifie la base de données. Confirme ton mot de passe (bouton « Confirmer » en haut)
        pour continuer.
    </div>

    <div v-else class="flex h-full min-h-0 w-full flex-col lg:flex-row">
        <SidebarNav
            title="Effets"
            description="Une entrée par définition d’effet ; le libellé secondaire indique le nombre de degrés."
            :items="groups"
            :get-item-href="(g) => route('admin.effects.show', g.id)"
            :is-item-active="(g) => selected && typeof selected === 'object' && g.id === selected.id"
            :get-item-label="(g) => g.label"
            :get-item-label-secondary="(g) => (g.effects.length > 1 ? `${g.effects.length} degrés` : (g.effects[0]?.degree != null ? `d${g.effects[0].degree}` : null))"
            :get-item-key="(g) => 'effect-' + g.id"
            searchable
            search-placeholder="Filtrer par nom…"
            :search-keys="['label']"
        >
            <template #nav-before>
                <Link
                    :href="route('admin.effects.create')"
                    :class="[
                        'sidebar-nav-item flex items-center gap-2 rounded-box border-l-4 border-transparent px-3 py-2 text-left text-sm font-medium transition-colors',
                        selected === 'new' && 'sidebar-nav-item-active'
                    ]"
                >
                    + Nouvel effet
                </Link>
            </template>
        </SidebarNav>

        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <EffectGroupEditorForm
                    v-if="isGroupEdit"
                    ref="groupEditorRef"
                    :options="options"
                    :group-effects="groupEffects"
                    :selected-effect-id="Number(groupEffects[0]?.id) || 0"
                    :patch-url="route('admin.effects.group-update', selected.id)"
                    :show-admin-degree-delete="true"
                    :admin-effect-id="Number(selected?.id)"
                    hide-submit-button
                />

                <!-- ——— Création (nouvel effet) ——— -->
                <form v-else-if="selected === 'new'" class="space-y-6" @submit.prevent="submit">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Nouvelle définition</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" />
                                <InputField v-model="form.slug" label="Slug" name="slug" helper="Optionnel, unique." />
                                <div class="sm:col-span-2">
                                    <InputField v-model="form.description" label="Description (aperçu)" name="description" type="textarea" />
                                </div>
                                <SelectField
                                    v-model="form.target_type"
                                    label="Type de cible"
                                    name="target_type"
                                    :options="TARGET_TYPE_OPTIONS"
                                    :searchable="false"
                                    helper="Direct, piège ou glyphe."
                                />
                                <InputField
                                    v-model="form.initial_required_creature_level"
                                    label="Niveau créature min. (D1)"
                                    name="initial_required_creature_level"
                                    type="number"
                                    helper="Vide = toujours actif."
                                />
                                <div class="flex items-end gap-2 sm:col-span-2 max-w-xl">
                                    <InputField
                                        v-model="form.initial_area"
                                        label="Zone (D1)"
                                        name="initial_area"
                                        helper="ex: point, line-1x9, shape-99…"
                                        class="flex-1"
                                        :validation="initialAreaValidation"
                                    />
                                    <AreaDisplay
                                        v-if="form.initial_area?.trim()"
                                        :area="form.initial_area"
                                        icon-size="sm"
                                        class="shrink-0 mb-1"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template v-else>
                <p class="text-base-content/70">
                    Sélectionnez un effet ou créez-en un nouveau.
                </p>
            </template>
        </main>
    </div>

    <ConfirmPasswordModal
        v-model:open="showAdminConfirmModal"
        title="Administration des effets"
        message="Cette section modifie les effets et degrés en base. Entre ton mot de passe pour confirmer ton identité."
        confirm-label="Confirmer"
        @confirmed="onAdminPasswordConfirmed"
    />
</template>
