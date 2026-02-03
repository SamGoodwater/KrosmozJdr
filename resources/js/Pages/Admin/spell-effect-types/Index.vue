<script setup>
/**
 * Admin Types d'effets de sort — Liste à gauche, panneau d'édition à droite.
 * Référentiel des types d'effets (dégâts, soins, états, etc.) utilisés par les sorts.
 */
import { watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    spellEffectTypes: { type: Array, required: true },
    selected: { type: Object, default: null },
    options: {
        type: Object,
        default: () => ({ categories: [], value_types: [], elements: [] }),
    },
});

defineOptions({ layout: Main });
setPageTitle('Types d\'effets de sort');

function buildFormData(selected) {
    if (!selected) {
        return {
            name: '',
            slug: '',
            category: 'damage',
            description: '',
            value_type: 'fixed',
            element: '',
            unit: '',
            is_positive: false,
            sort_order: 0,
            dofusdb_effect_id: '',
        };
    }
    return {
        name: selected.name ?? '',
        slug: selected.slug ?? '',
        category: selected.category ?? 'damage',
        description: selected.description ?? '',
        value_type: selected.value_type ?? 'fixed',
        element: selected.element ?? '',
        unit: selected.unit ?? '',
        is_positive: selected.is_positive ?? false,
        sort_order: selected.sort_order ?? 0,
        dofusdb_effect_id: selected.dofusdb_effect_id ?? '',
    };
}

const form = useForm(buildFormData(props.selected));

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s);
        form.name = data.name;
        form.slug = data.slug;
        form.category = data.category;
        form.description = data.description;
        form.value_type = data.value_type;
        form.element = data.element;
        form.unit = data.unit;
        form.is_positive = data.is_positive;
        form.sort_order = data.sort_order;
        form.dofusdb_effect_id = data.dofusdb_effect_id;
    },
    { immediate: true }
);

function submit() {
    if (!props.selected?.id) return;
    const payload = { ...form };
    if (payload.dofusdb_effect_id === '' || payload.dofusdb_effect_id == null) {
        payload.dofusdb_effect_id = null;
    } else {
        payload.dofusdb_effect_id = Number(payload.dofusdb_effect_id);
    }
    form.transform(() => payload).patch(route('admin.spell-effect-types.update', props.selected.id));
}
</script>

<template>
    <Head title="Types d'effets de sort" />
    <div class="flex h-full min-h-0 w-full">
        <!-- Liste à gauche -->
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Types d'effets</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Référentiel des effets que peuvent produire les sorts. Sélectionnez un type pour l'éditer.
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <Link
                    v-for="t in spellEffectTypes"
                    :key="t.id"
                    :href="route('admin.spell-effect-types.show', t.id)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-left transition-colors border-l-4 border-transparent"
                    :class="selected?.id === t.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                >
                    <span class="truncate">{{ t.name }}</span>
                    <span class="text-xs opacity-70 shrink-0">{{ t.category }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Panneau à droite -->
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected.name || selected.slug }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Modifiez les champs puis cliquez sur « Enregistrer ». Le slug sert d'identifiant (ex. mapping DofusDB).
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" required />
                                <InputField
                                    v-model="form.slug"
                                    label="Slug"
                                    name="slug"
                                    required
                                    helper="Identifiant unique (ex. damage_fire, heal)."
                                />
                                <div class="sm:col-span-2">
                                    <SelectFieldNative
                                        v-model="form.category"
                                        label="Catégorie"
                                        name="category"
                                        :options="options.categories"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputField
                                        v-model="form.description"
                                        label="Description"
                                        name="description"
                                        type="textarea"
                                        helper="Description optionnelle de l'effet."
                                    />
                                </div>
                                <SelectFieldNative
                                    v-model="form.value_type"
                                    label="Type de valeur"
                                    name="value_type"
                                    :options="options.value_types"
                                />
                                <SelectFieldNative
                                    v-model="form.element"
                                    label="Élément"
                                    name="element"
                                    :options="options.elements"
                                />
                                <InputField
                                    v-model="form.unit"
                                    label="Unité"
                                    name="unit"
                                    helper="ex. PV, PA, %, tours."
                                />
                                <InputField
                                    v-model="form.sort_order"
                                    label="Ordre d'affichage"
                                    name="sort_order"
                                    type="number"
                                />
                                <div class="flex items-center gap-2 sm:col-span-2">
                                    <input
                                        v-model="form.is_positive"
                                        type="checkbox"
                                        class="checkbox checkbox-primary"
                                        name="is_positive"
                                    />
                                    <label for="is_positive" class="label cursor-pointer">
                                        <span class="label-text">Effet positif (buff, soin, etc.)</span>
                                    </label>
                                </div>
                                <InputField
                                    v-model="form.dofusdb_effect_id"
                                    label="ID effet DofusDB"
                                    name="dofusdb_effect_id"
                                    type="number"
                                    helper="Optionnel : identifiant de l'effet côté DofusDB pour le mapping."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Enregistrement…' : 'Enregistrer' }}
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
                    </div>
                </form>
            </template>
            <template v-else>
                <p class="text-base-content/70">
                    Sélectionnez un type d'effet dans la liste pour l'éditer.
                </p>
            </template>
        </main>
    </div>
</template>
