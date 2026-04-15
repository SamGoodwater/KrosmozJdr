<script setup>
/**
 * Admin Effects — Données communes (nom, groupe, cible, description) puis onglets par degré
 * (slug, zone, sous-effets). « Ajouter un degré » duplique l’effet courant côté serveur.
 */
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import SidebarNav from '@/Pages/Organismes/layout/SidebarNav.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import EffectGroupEditorForm from '@/Pages/Organismes/entity/EffectGroupEditorForm.vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import { AREA_NOTATION_HELP, isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';

const { setPageTitle } = usePageTitle();

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
setPageTitle('Effets');

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
    <Head title="Effets" />

    <div
        v-if="!adminUnlocked"
        class="rounded-box border border-warning/40 bg-warning/10 p-8 mx-auto max-w-lg my-8 text-center space-y-4"
    >
        <p class="text-warning-content">
            La définition des effets modifie la base de données. Confirme ton mot de passe pour continuer.
        </p>
        <Btn color="primary" @click="showAdminConfirmModal = true">
            Accéder à l’administration des effets
        </Btn>
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
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected === 'new' ? 'Nouvel effet' : (selected.name || selected.slug || 'Effet') }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    <template v-if="isGroupEdit">
                        Champs communs à tous les degrés du groupe, puis un onglet par degré (slug, <strong>zone</strong> et sous-effets peuvent différer par degré).
                        Le <strong>niveau de créature requis</strong> par degré se règle dans chaque onglet (champ dédié), y compris depuis la fiche sort.
                    </template>
                    <template v-else>
                        Nom, description optionnelle, groupe et degré. Liste des sous-effets avec ordre, contexte (Général / Combat / Hors combat) et paramètres.
                    </template>
                </p>

                <!-- ——— Édition groupe (effet existant avec groupEffects) ——— -->
                <template v-if="isGroupEdit">
                    <EffectGroupEditorForm
                        ref="groupEditorRef"
                        :options="options"
                        :group-effects="groupEffects"
                        :selected-effect-id="Number(groupEffects[0]?.id) || 0"
                        :patch-url="route('admin.effects.group-update', selected.id)"
                        :show-admin-degree-delete="true"
                        :admin-effect-id="Number(selected?.id)"
                    />
                    <div class="flex flex-wrap gap-2 items-center mt-4">
                        <button
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing || groupEditorRef?.saving"
                            @click="duplicateDegree"
                        >
                            Ajouter un degré
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing || groupEditorRef?.saving"
                            @click="duplicateEffect"
                        >
                            Dupliquer l'effet
                        </button>
                        <button
                            type="button"
                            class="btn btn-ghost btn-error"
                            :disabled="form.processing || groupEditorRef?.saving"
                            @click="destroyDefinition"
                        >
                            Supprimer la définition
                        </button>
                    </div>
                </template>

                <!-- ——— Création (nouvel effet) ——— -->
                <form v-else-if="selected === 'new'" class="space-y-6" @submit.prevent="submit">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Nouvelle définition</h2>
                            <p class="text-sm text-base-content/70 mb-2">
                                Un premier degré (D1) est créé automatiquement. Ajoutez les sous-effets après enregistrement.
                            </p>
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

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Enregistrement…' : 'Créer' }}
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
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
        confirm-label="Accéder"
        @confirmed="onAdminPasswordConfirmed"
    />
</template>
