<script setup>
/**
 * Admin Sous-effets — Liste à gauche, panneau création/édition à droite.
 * Référentiel des sous-effets (taper, soigner, vol_pa…). Template et formula sanitized côté serveur.
 */
import { watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    subEffects: { type: Array, required: true },
    selected: { type: [Object, String], default: null },
    options: {
        type: Object,
        default: () => ({ type_slugs: [], syntax_help_url: '' }),
    },
});

defineOptions({ layout: Main });
setPageTitle('Sous-effets');

function buildFormData(selected) {
    if (!selected || selected === 'new') {
        return {
            slug: '',
            type_slug: 'taper',
            template_text: '',
            formula: '',
            variables_allowed: [],
            dofusdb_effect_id: '',
        };
    }
    return {
        slug: selected.slug ?? '',
        type_slug: selected.type_slug ?? 'taper',
        template_text: selected.template_text ?? '',
        formula: selected.formula ?? '',
        variables_allowed: selected.variables_allowed ?? [],
        dofusdb_effect_id: selected.dofusdb_effect_id ?? '',
    };
}

const form = useForm(buildFormData(props.selected));

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s);
        form.slug = data.slug;
        form.type_slug = data.type_slug;
        form.template_text = data.template_text;
        form.formula = data.formula;
        form.variables_allowed = data.variables_allowed;
        form.dofusdb_effect_id = data.dofusdb_effect_id;
    },
    { immediate: true }
);

function submit() {
    if (props.selected === 'new') {
        const payload = { ...form };
        if (payload.dofusdb_effect_id === '' || payload.dofusdb_effect_id == null) {
            payload.dofusdb_effect_id = null;
        } else {
            payload.dofusdb_effect_id = Number(payload.dofusdb_effect_id);
        }
        form.transform(() => payload).post(route('admin.sub-effects.store'));
        return;
    }
    if (props.selected?.id) {
        const payload = { ...form };
        if (payload.dofusdb_effect_id === '' || payload.dofusdb_effect_id == null) {
            payload.dofusdb_effect_id = null;
        } else {
            payload.dofusdb_effect_id = Number(payload.dofusdb_effect_id);
        }
        form.transform(() => payload).patch(route('admin.sub-effects.update', props.selected.id));
    }
}

function destroy() {
    if (!props.selected?.id) return;
    if (confirm('Supprimer ce sous-effet ?')) {
        form.delete(route('admin.sub-effects.destroy', props.selected.id));
    }
}
</script>

<template>
    <Head title="Sous-effets" />
    <div class="flex h-full min-h-0 w-full">
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Sous-effets</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Référentiel des atomes d'effet. Variables [var], ndX. Template et formula sont nettoyés à l'enregistrement.
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <Link
                    :href="route('admin.sub-effects.create')"
                    class="rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors hover:bg-base-300 border-l-4 border-transparent"
                    :class="selected === 'new' ? 'bg-primary text-primary-content' : ''"
                >
                    + Nouveau sous-effet
                </Link>
                <Link
                    v-for="s in subEffects"
                    :key="s.id"
                    :href="route('admin.sub-effects.show', s.id)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-left transition-colors border-l-4 border-transparent"
                    :class="selected?.id === s.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                >
                    <span class="truncate">{{ s.slug }}</span>
                    <span class="text-xs opacity-70 shrink-0">{{ s.type_slug }}</span>
                </Link>
            </nav>
        </aside>

        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected === 'new' ? 'Nouveau sous-effet' : selected.slug }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Variables : [value], [agi], [level], [element]… Dés : ndX (ex. 2d6). Voir la syntaxe des formules en doc.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.slug" label="Slug" name="slug" required helper="Identifiant unique (ex. taper, soigner)." />
                                <div class="sm:col-span-2">
                                    <SelectFieldNative
                                        v-model="form.type_slug"
                                        label="Type"
                                        name="type_slug"
                                        :options="options.type_slugs"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputField
                                        v-model="form.template_text"
                                        label="Template texte"
                                        name="template_text"
                                        type="textarea"
                                        helper="Ex. Inflige [value] dégâts [element]. Variables entre crochets, dés ndX."
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputField
                                        v-model="form.formula"
                                        label="Formule (optionnel)"
                                        name="formula"
                                        type="textarea"
                                        helper="Ex. [level]*2 + [agi]. Même syntaxe que les caractéristiques."
                                    />
                                </div>
                                <InputField
                                    v-model="form.dofusdb_effect_id"
                                    label="ID effet DofusDB"
                                    name="dofusdb_effect_id"
                                    type="number"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Enregistrement…' : (selected === 'new' ? 'Créer' : 'Enregistrer') }}
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-ghost btn-error"
                            :disabled="form.processing"
                            @click="destroy"
                        >
                            Supprimer
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
                    </div>
                </form>
            </template>
            <template v-else>
                <p class="text-base-content/70">
                    Sélectionnez un sous-effet ou créez-en un nouveau.
                </p>
            </template>
        </main>
    </div>
</template>
