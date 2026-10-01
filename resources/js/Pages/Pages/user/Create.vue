<script setup>
/**
 * Création d’un compte utilisateur (admin). « Créer » dans l’en-tête, confirmé par mot de passe.
 */
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import { useProtectedAdminAction } from '@/Composables/auth/useProtectedAdminAction';
import { getRoleTranslation } from '@/Utils/user/RoleManager';
import { ACTION } from '@/Utils/atomic-design/actionLabels';

defineOptions({ layout: AdminArea });

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

const {
    showPasswordModal,
    passwordModalTitle,
    passwordModalMessage,
    passwordModalConfirmLabel,
    requirePassword,
    onPasswordConfirmed,
    onPasswordModalCancel,
} = useProtectedAdminAction();

const roleOptions = computed(() => {
    return Object.entries(props.roles || {})
        .map(([value, roleName]) => ({
            value: Number(value),
            label: getRoleTranslation(roleName),
        }))
        .filter((opt) => opt.value !== 5);
});

const submit = () => {
    requirePassword(
        'Confirmer la création',
        'Entre ton mot de passe pour créer ce compte utilisateur.',
        'Créer',
        () => {
            form.post(route('user.store'), {
                preserveScroll: true,
            });
        },
    );
};
</script>
<template>
    <section class="space-y-5">
        <PageHeader
            title="Créer un compte utilisateur"
            subtitle="Ajoutez une personne à la plateforme et définissez son niveau d'accès."
            back-route="user.index"
        >
            <template #primary>
                <Btn type="submit" form="user-create-form" color="primary" size="sm" :disabled="form.processing">
                    <i :class="ACTION.create.icon" class="mr-1.5" aria-hidden="true"></i>
                    {{ form.processing ? ACTION.create.processing : ACTION.create.label }}
                </Btn>
            </template>
        </PageHeader>

        <div class="rounded-(--radius-box) border border-base-300 bg-base-100 p-5">
            <form id="user-create-form" class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submit">
                <InputField v-model="form.name" label="Nom" required :validation="form.errors.name ? { state: 'error', message: form.errors.name } : null" />
                <InputField v-model="form.email" type="email" label="Email" required :validation="form.errors.email ? { state: 'error', message: form.errors.email } : null" />
                <InputField v-model="form.password" type="password" label="Mot de passe" required :validation="form.errors.password ? { state: 'error', message: form.errors.password } : null" />
                <InputField v-model="form.password_confirmation" type="password" label="Confirmer le mot de passe" required />
                <SelectField
                    v-model="form.role"
                    label="Niveau d'accès"
                    :options="roleOptions"
                    :validation="form.errors.role ? { state: 'error', message: form.errors.role } : null"
                    :searchable="false"
                />
                <div class="md:col-span-2 alert alert-info alert-soft">
                    Le rôle super administrateur ne peut pas être attribué depuis cet écran.
                </div>
            </form>
        </div>

        <ConfirmPasswordModal
            v-model:open="showPasswordModal"
            :title="passwordModalTitle"
            :message="passwordModalMessage"
            :confirm-label="passwordModalConfirmLabel"
            @confirmed="onPasswordConfirmed"
            @cancel="onPasswordModalCancel"
        />
    </section>
</template>
