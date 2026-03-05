<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import { getRoleTranslation } from '@/Utils/user/RoleManager';

const props = defineProps({
    roles: { type: Object, default: () => ({}) },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 1,
});

const roleOptions = computed(() => {
    return Object.entries(props.roles || {})
        .map(([value, roleName]) => ({
            value: Number(value),
            label: getRoleTranslation(roleName),
        }))
        .filter((opt) => opt.value !== 5);
});

const submit = () => {
    form.post(route('user.store'), {
        preserveScroll: true,
    });
};
</script>
<template>
    <section class="space-y-5">
        <header class="space-y-2">
            <Route route="user.index">
                <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Retour à la liste
                </Btn>
            </Route>
            <div>
                <h1 class="text-2xl font-bold">Créer un compte utilisateur</h1>
                <p class="text-sm opacity-70">
                    Ajoutez une personne à la plateforme et définissez son niveau d'accès.
                </p>
            </div>
        </header>

        <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-5">
            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submit">
                <InputField v-model="form.name" label="Nom" required :validation="form.errors.name ? { state: 'error', message: form.errors.name } : null" />
                <InputField v-model="form.email" type="email" label="Email" required :validation="form.errors.email ? { state: 'error', message: form.errors.email } : null" />
                <InputField v-model="form.password" type="password" label="Mot de passe" required :validation="form.errors.password ? { state: 'error', message: form.errors.password } : null" />
                <InputField v-model="form.password_confirmation" type="password" label="Confirmer le mot de passe" required />
                <SelectField
                    v-model="form.role"
                    label="Niveau d'accès"
                    :options="roleOptions"
                    :validation="form.errors.role ? { state: 'error', message: form.errors.role } : null"
                />
                <div class="md:col-span-2 alert alert-info alert-soft">
                    Le rôle super administrateur ne peut pas être attribué depuis cet écran.
                </div>
                <div class="md:col-span-2 flex items-center gap-2 pt-2">
                    <Btn color="primary" size="sm" :disabled="form.processing" @click="submit">
                        Créer le compte
                    </Btn>
                    <Route route="user.index">
                        <Btn color="neutral" variant="ghost" size="sm" :disabled="form.processing">
                            Annuler
                        </Btn>
                    </Route>
                </div>
            </form>
        </div>
    </section>
</template>

