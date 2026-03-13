<script setup>
/**
 * Page Paramètres du compte.
 * Page dédiée aux paramètres (indépendante des notifications), avec onglets (Notifications, etc.).
 * L’ancre #notifications permet d’ouvrir directement l’onglet Notifications (ex. depuis le centre de notifications).
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';

const page = usePage();
const { success, error } = useNotificationStore();

const TAB_NOTIFICATIONS = 'notifications';
const TAB_CONNECTIONS = 'connections';
const activeTab = ref(TAB_NOTIFICATIONS);

const oauthProviders = computed(() => page.props.oauthProviders ?? page.props.oauth_enabled_providers ?? []);
const linkedProviders = computed(() => {
    const accounts = user.value?.oauth_accounts || [];
    return accounts.map((a) => a.provider);
});
const hasPassword = computed(() => user.value?.has_password ?? true);

const formConvert = useForm({
    password: '',
    password_confirmation: '',
});

function isProviderLinked(provider) {
    return linkedProviders.value.includes(provider);
}

/**
 * Retourne le compte OAuth lié pour un provider donné (avatar, pseudo).
 * @param {string} provider - Nom du provider (github, discord, steam)
 * @returns {{ provider: string, provider_name?: string, avatar_url?: string } | null}
 */
function getLinkedAccount(provider) {
    const accounts = user.value?.oauth_accounts || [];
    return accounts.find((a) => a.provider === provider) ?? null;
}

function canUnlink(provider) {
    return hasPassword.value || linkedProviders.value.length > 1;
}

function providerLabel(provider) {
    const labels = { github: 'GitHub', discord: 'Discord', steam: 'Steam' };
    return labels[provider] ?? provider;
}

function providerIcon(provider) {
    const icons = { github: 'fa-brands fa-github', discord: 'fa-brands fa-discord', steam: 'fa-brands fa-steam' };
    return icons[provider] ?? 'fa-solid fa-link';
}

function submitConvert() {
    formConvert.post(route('user.oauth.convert'), {
        preserveScroll: true,
        onSuccess: () => {
            success('Compte converti. Tu peux maintenant te connecter avec ton email et ton mot de passe.');
            formConvert.reset();
        },
        onError: () => error('Erreur lors de la conversion.'),
    });
}

function unlinkProvider(provider) {
    if (!canUnlink(provider)) return;
    router.delete(route('user.oauth.unlink', { provider }), {
        preserveScroll: true,
        onSuccess: () => success('Compte délié.'),
        onError: () => error('Impossible de délier ce compte.'),
    });
}

/**
 * Données utilisateur pour Settings.
 * Combine page.props.user (controller) et auth.user (partagé) pour garantir
 * oauth_accounts disponible (état des connexions OAuth liées).
 */
const user = computed(() => {
    const pageUser = page.props.user;
    const pageUnwrapped = pageUser?.data?.id ? pageUser.data : pageUser;
    const authUser = page.props.auth?.user ?? {};

    const primary = pageUnwrapped?.id ? pageUnwrapped : authUser;
    if (!primary) return {};

    // Fallback sur auth.user si page.user n'a pas oauth_accounts (ex. wrapping JsonResource)
    const oauthAccounts =
        (primary.oauth_accounts?.length ? primary.oauth_accounts : null)
        ?? (authUser.oauth_accounts?.length ? authUser.oauth_accounts : null)
        ?? [];
    return {
        ...primary,
        oauth_accounts: Array.isArray(oauthAccounts) ? oauthAccounts : [],
    };
});

const formNotifications = useForm({
    notification_preferences: {},
});

const notificationTypesFiltered = computed(() => {
    const types = page.props.notificationTypes || {};
    const userRole = user.value?.role ?? 1;
    const roleNames = { 1: 'user', 2: 'player', 3: 'game_master', 4: 'admin', 5: 'super_admin' };
    const currentRoleName = roleNames[userRole] || 'user';
    return Object.entries(types).filter(([, config]) => {
        if (!config.roles) return true;
        return config.roles.includes(currentRoleName);
    });
});

const notificationChannelOptions = [
    { value: [], label: 'Aucune' },
    { value: ['database'], label: 'Sur le site uniquement' },
    { value: ['mail'], label: 'Par email uniquement' },
    { value: ['database', 'mail'], label: 'Sur le site et par email' },
];

function setNotificationPreference(typeKey, channels) {
    if (!formNotifications.notification_preferences) {
        formNotifications.notification_preferences = {};
    }
    formNotifications.notification_preferences[typeKey] = Array.isArray(channels) ? [...channels].sort() : [];
}

function preferenceChannelsValue(typeKey) {
    const ch = formNotifications.notification_preferences?.[typeKey];
    if (!Array.isArray(ch)) return '[]';
    return JSON.stringify([...ch].sort());
}

function initNotificationForm() {
    const data = user.value;
    if (!data?.id) return;
    const types = page.props.notificationTypes || {};
    const prefs = data.notification_preferences || {};
    const defaultPrefs = Object.fromEntries(
        Object.keys(types).map((k) => [k, types[k].channels_default || ['database']])
    );
    formNotifications.notification_preferences = { ...defaultPrefs, ...prefs };
}

function saveNotifications() {
    const url = `${route('user.update')}?redirect=settings`;
    formNotifications.patch(url, {
        preserveScroll: true,
        onSuccess: () => success('Préférences de notification enregistrées.'),
        onError: () => error('Erreur lors de l’enregistrement des préférences.'),
    });
}

function setActiveTabFromHash() {
    const hash = window.location.hash?.replace('#', '') || TAB_NOTIFICATIONS;
    if (hash === TAB_CONNECTIONS) activeTab.value = TAB_CONNECTIONS;
    else activeTab.value = TAB_NOTIFICATIONS;
}

function syncHashToTab() {
    const hash = activeTab.value === TAB_CONNECTIONS ? TAB_CONNECTIONS : TAB_NOTIFICATIONS;
    const url = new URL(window.location.href);
    url.hash = hash;
    window.history.replaceState(null, '', url.toString());
}

watch(user, (newUser) => {
    if (newUser?.id) initNotificationForm();
}, { immediate: true, deep: true });

watch(activeTab, syncHashToTab);

onMounted(() => {
    setActiveTabFromHash();
    initNotificationForm();
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('hashchange', setActiveTabFromHash);
    }
});

if (typeof window !== 'undefined') {
    window.addEventListener('hashchange', setActiveTabFromHash);
}

function goBackToProfile() {
    router.visit(route('user.show'));
}
</script>

<template>
    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold">Paramètres du compte</h1>
            <div class="flex items-center gap-2">
                <a :href="route('user.privacy.index')" class="btn btn-outline btn-sm">
                    Mes données (RGPD)
                </a>
                <Btn
                    color="neutral"
                    variant="ghost"
                    size="sm"
                    class="gap-2"
                    @click="goBackToProfile"
                >
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Retour au profil
                </Btn>
            </div>
        </div>

        <div role="tablist" class="tabs tabs-lift tabs-md tabs-top bg-base-100 shadow-sm mb-4">
            <button
                type="button"
                role="tab"
                :aria-selected="activeTab === TAB_NOTIFICATIONS"
                :class="['tab', activeTab === TAB_NOTIFICATIONS && 'tab-active']"
                @click="activeTab = TAB_NOTIFICATIONS"
            >
                <i class="fa-solid fa-bell mr-2" aria-hidden="true"></i>
                Notifications
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="activeTab === TAB_CONNECTIONS"
                :class="['tab', activeTab === TAB_CONNECTIONS && 'tab-active']"
                @click="activeTab = TAB_CONNECTIONS"
            >
                <i class="fa-solid fa-link mr-2" aria-hidden="true"></i>
                Connexions
            </button>
        </div>

        <div
            :id="TAB_NOTIFICATIONS"
            v-show="activeTab === TAB_NOTIFICATIONS"
            class="space-y-4"
        >
            <div class="rounded-lg border border-base-300 bg-base-200/30 p-4">
                <h2 class="text-lg font-medium text-content-300">Préférences de notification</h2>
                <p class="mt-1 text-sm text-content-600">
                    Choisissez quelles notifications recevoir et comment (sur le site, par email, ou les deux).
                </p>
                <div v-if="notificationTypesFiltered.length > 0" class="mt-4 space-y-3">
                    <div
                        v-for="[typeKey, config] in notificationTypesFiltered"
                        :key="typeKey"
                        class="flex flex-wrap items-center justify-between gap-2 py-2 border-b border-base-300/50 last:border-0"
                    >
                        <label class="text-sm font-medium text-content-200">{{ config.label }}</label>
                        <select
                            :value="preferenceChannelsValue(typeKey)"
                            class="select select-bordered select-sm max-w-xs"
                            @change="setNotificationPreference(typeKey, JSON.parse($event.target.value))"
                        >
                            <option
                                v-for="opt in notificationChannelOptions"
                                :key="opt.label"
                                :value="JSON.stringify(opt.value)"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                </div>
                <p v-else class="mt-4 text-sm text-content-500">
                    Aucune préférence de notification configurée pour ton niveau d'accès.
                </p>
                <div v-if="notificationTypesFiltered.length > 0" class="mt-4 flex items-center gap-2">
                    <Btn
                        color="primary"
                        :disabled="formNotifications.processing"
                        @click="saveNotifications"
                    >
                        Enregistrer les préférences
                    </Btn>
                    <Btn
                        color="neutral"
                        variant="ghost"
                        :disabled="formNotifications.processing"
                        @click="initNotificationForm()"
                    >
                        Annuler
                    </Btn>
                </div>
            </div>
        </div>

        <div
            :id="TAB_CONNECTIONS"
            v-show="activeTab === TAB_CONNECTIONS"
            class="space-y-4"
        >
            <div class="rounded-lg border border-base-300 bg-base-200/30 p-4">
                <h2 class="text-lg font-medium text-content-300">Connexions OAuth</h2>
                <p class="mt-1 text-sm text-content-600">
                    Lie ou délie GitHub, Discord ou Steam pour te connecter avec plusieurs méthodes. Tu peux te connecter avec n'importe quel compte lié.
                </p>
                <p v-if="oauthProviders.length === 0" class="mt-4 text-sm text-content-500">
                    Aucun provider OAuth configuré (GitHub, Discord ou Steam).
                </p>
                <div v-else class="mt-4 space-y-4">
                    <p class="text-sm text-content-600">
                        Clique sur <strong>Lier</strong> pour ajouter une connexion, ou <strong>Délier</strong> pour la retirer (tu dois garder au moins une méthode de connexion).
                    </p>
                    <div
                        v-for="provider in oauthProviders"
                        :key="provider"
                        class="flex flex-wrap items-center justify-between gap-4 py-4 px-3 rounded-lg bg-base-100/50 border border-base-300/50"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <i :class="providerIcon(provider)" class="text-2xl text-content-400 shrink-0"></i>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium text-content-200">{{ providerLabel(provider) }}</span>
                                <!-- Compte lié : avatar + pseudo -->
                                <div
                                    v-if="isProviderLinked(provider) && getLinkedAccount(provider)"
                                    class="flex items-center gap-2 mt-2"
                                >
                                    <img
                                        v-if="getLinkedAccount(provider)?.avatar_url"
                                        :src="getLinkedAccount(provider).avatar_url"
                                        :alt="getLinkedAccount(provider).provider_name || provider"
                                        class="size-8 rounded-full object-cover shrink-0"
                                        referrerpolicy="no-referrer"
                                    />
                                    <div
                                        v-else
                                        class="size-8 rounded-full bg-base-300 flex items-center justify-center shrink-0"
                                    >
                                        <i :class="providerIcon(provider)" class="text-content-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm text-content-500 truncate">
                                        {{ getLinkedAccount(provider).provider_name || 'Compte lié' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <template v-if="!isProviderLinked(provider)">
                                <a
                                    :href="route('user.oauth.link', { provider })"
                                    class="btn btn-outline btn-primary btn-sm gap-1"
                                >
                                    <i class="fa-solid fa-link text-xs"></i>
                                    Lier
                                </a>
                            </template>
                            <template v-else>
                                <span class="badge badge-success badge-sm">Lié</span>
                                <Btn
                                    v-if="canUnlink(provider)"
                                    color="error"
                                    variant="outline"
                                    size="sm"
                                    class="gap-1"
                                    @click="unlinkProvider(provider)"
                                >
                                    <i class="fa-solid fa-unlink text-xs"></i>
                                    Délier
                                </Btn>
                                <span
                                    v-else
                                    class="text-xs text-content-500"
                                >
                                    (Délier impossible : garde au moins une méthode de connexion)
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="!hasPassword"
                class="rounded-lg border border-base-300 bg-base-200/30 p-4"
            >
                <h2 class="text-lg font-medium text-content-300">Définir un mot de passe</h2>
                <p class="mt-1 text-sm text-content-600">
                    Tu t'es inscrit avec GitHub, Discord ou Steam. Ajoute un mot de passe pour pouvoir te connecter aussi avec ton email et ton mot de passe.
                </p>
                <form class="mt-4 space-y-4 max-w-md" @submit.prevent="submitConvert">
                    <div class="space-y-1">
                        <InputField
                            v-model="formConvert.password"
                            type="password"
                            name="password"
                            label="Mot de passe"
                            placeholder="Nouveau mot de passe"
                            autocomplete="new-password"
                            :validation="formConvert.errors.password ? { state: 'error', message: formConvert.errors.password } : null"
                            :parent-control="false"
                        />
                    </div>
                    <div class="space-y-1">
                        <InputField
                            v-model="formConvert.password_confirmation"
                            type="password"
                            name="password_confirmation"
                            label="Confirmer le mot de passe"
                            placeholder="Confirmer"
                            autocomplete="new-password"
                            :validation="formConvert.errors.password_confirmation ? { state: 'error', message: formConvert.errors.password_confirmation } : null"
                            :parent-control="false"
                        />
                    </div>
                    <Btn
                        type="submit"
                        color="primary"
                        :disabled="formConvert.processing"
                    >
                        Convertir
                    </Btn>
                </form>
            </div>
        </div>
    </div>
</template>
