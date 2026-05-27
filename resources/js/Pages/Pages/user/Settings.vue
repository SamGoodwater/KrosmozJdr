<script setup>
/**
 * Page Paramètres du compte.
 * Page dédiée aux paramètres (indépendante des notifications), avec onglets (Notifications, etc.).
 * L’ancre #notifications permet d’ouvrir directement l’onglet Notifications (ex. depuis le centre de notifications).
 */
import { ref, computed, watch, onMounted, onUnmounted, inject } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';

const page = usePage();
const notificationStore = inject('notificationStore', null);
const showToast = (type, message) => {
    if (!notificationStore || !message) return;
    notificationStore[type](message, { duration: 8000, placement: 'top-right' });
};

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
            showToast('success', 'Compte converti. Tu peux maintenant te connecter avec ton email et ton mot de passe.');
            formConvert.reset();
        },
        onError: () => showToast('error', 'Erreur lors de la conversion.'),
    });
}

function unlinkProvider(provider) {
    if (!canUnlink(provider)) return;
    router.delete(route('user.oauth.unlink', { provider }), {
        preserveScroll: true,
        onSuccess: () => showToast('success', 'Compte délié.'),
        onError: () => showToast('error', 'Impossible de délier ce compte.'),
    });
}

/**
 * Utilisateur de la page paramètres (source complète : préférences, OAuth, etc.).
 * Ne pas utiliser auth.user seul : il n’expose pas notification_preferences.
 */
const settingsUser = computed(() => {
    const pageUser = page.props.user;
    const pageUnwrapped = pageUser?.data?.id ? pageUser.data : pageUser;
    return pageUnwrapped?.id ? pageUnwrapped : null;
});

/**
 * Données utilisateur pour Settings (OAuth, affichage).
 * Combine page.props.user et auth.user pour oauth_accounts.
 */
const user = computed(() => {
    const pageUnwrapped = settingsUser.value;
    const authUser = page.props.auth?.user ?? {};

    const primary = pageUnwrapped?.id ? pageUnwrapped : authUser;
    if (!primary?.id) return {};

    const oauthAccounts =
        (primary.oauth_accounts?.length ? primary.oauth_accounts : null)
        ?? (authUser.oauth_accounts?.length ? authUser.oauth_accounts : null)
        ?? [];
    return {
        ...primary,
        notification_preferences: pageUnwrapped?.notification_preferences ?? primary.notification_preferences ?? {},
        oauth_accounts: Array.isArray(oauthAccounts) ? oauthAccounts : [],
    };
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

const formNotifications = useForm({
    notifications_enabled: true,
    notification_preferences: {},
});

const notificationFrequencies = computed(() => page.props.notificationFrequencies || {});

const notificationGroups = computed(() => {
    const personal = [];
    const admin = [];
    for (const entry of notificationTypesFiltered.value) {
        const category = entry[1].category ?? 'personal';
        if (category === 'admin' || category === 'admin_action') {
            admin.push(entry);
        } else {
            personal.push(entry);
        }
    }
    return { personal, admin };
});

const notificationChannelOptions = [
    { value: [], label: 'Aucune' },
    { value: ['database'], label: 'Sur le site uniquement' },
    { value: ['mail'], label: 'Par email uniquement' },
    { value: ['database', 'mail'], label: 'Sur le site et par email' },
];

function frequencyFromPref(val) {
    if (val && typeof val === 'object' && typeof val.frequency === 'string') {
        return val.frequency;
    }
    return 'instant';
}

function setNotificationPreference(typeKey, channels) {
    if (!formNotifications.notification_preferences) {
        formNotifications.notification_preferences = {};
    }
    const current = formNotifications.notification_preferences[typeKey] ?? {};
    formNotifications.notification_preferences[typeKey] = {
        channels: Array.isArray(channels) ? [...channels].sort() : [],
        frequency: frequencyFromPref(current),
    };
}

function setNotificationFrequency(typeKey, frequency) {
    if (!formNotifications.notification_preferences) {
        formNotifications.notification_preferences = {};
    }
    const current = formNotifications.notification_preferences[typeKey] ?? {};
    const channels = channelsFromPref(current);
    formNotifications.notification_preferences[typeKey] = {
        channels,
        frequency,
    };
}

/** Extrait les canaux depuis le format backend { channels: [...], frequency } ou tableau direct. */
function channelsFromPref(val) {
    if (Array.isArray(val)) return val;
    if (val && typeof val === 'object' && Array.isArray(val.channels)) return val.channels;
    return [];
}

function preferenceChannelsValue(typeKey) {
    const ch = channelsFromPref(formNotifications.notification_preferences?.[typeKey]);
    return JSON.stringify([...ch].sort());
}

function preferenceFrequencyValue(typeKey) {
    return frequencyFromPref(formNotifications.notification_preferences?.[typeKey]);
}

function isFrequencyEditable(typeKey, config) {
    if (config?.frequency_editable === false) return false;
    const channels = channelsFromPref(formNotifications.notification_preferences?.[typeKey]);
    return channels.length > 0;
}

function channelsSummary(typeKey) {
    const channels = channelsFromPref(formNotifications.notification_preferences?.[typeKey]);
    if (channels.length === 0) return 'Désactivée';
    const parts = [];
    if (channels.includes('database')) parts.push('site');
    if (channels.includes('mail')) parts.push('email');
    return parts.join(' + ');
}

/** Normalise les préférences backend en format formulaire { channels, frequency }. */
function normalizePrefs(prefs) {
    const result = {};
    for (const [key, config] of notificationTypesFiltered.value) {
        const val = prefs?.[key];
        const defaultChannels = config.channels_default || ['database'];
        const defaultFrequency = config.frequency_default || 'instant';
        result[key] = {
            channels: channelsFromPref(val ?? defaultChannels),
            frequency: val ? frequencyFromPref(val) : defaultFrequency,
        };
    }
    return result;
}

function buildNotificationPreferencesPayload() {
    const payload = {};
    for (const [typeKey] of notificationTypesFiltered.value) {
        const pref = formNotifications.notification_preferences?.[typeKey] ?? {};
        payload[typeKey] = {
            channels: channelsFromPref(pref),
            frequency: frequencyFromPref(pref),
        };
    }
    return payload;
}

function collectFormErrors(errors, out = []) {
    if (!errors || typeof errors !== 'object') return out;
    for (const val of Object.values(errors)) {
        if (Array.isArray(val)) {
            val.filter(Boolean).forEach((m) => out.push(String(m)));
        } else if (typeof val === 'string' && val.trim() !== '') {
            out.push(val.trim());
        } else if (val && typeof val === 'object') {
            collectFormErrors(val, out);
        }
    }
    return out;
}

function firstFormError(errors) {
    const messages = collectFormErrors(errors);
    return messages[0] ?? null;
}

const notificationSaveErrorMessages = computed(() => {
    if (!formNotifications.hasErrors) return [];
    const messages = collectFormErrors(formNotifications.errors);
    return messages.length > 0
        ? messages
        : ['Erreur lors de l’enregistrement des préférences.'];
});

function initNotificationForm() {
    const data = settingsUser.value;
    if (!data?.id) return;
    formNotifications.clearErrors();
    formNotifications.notifications_enabled = data.notifications_enabled ?? true;
    formNotifications.notification_preferences = normalizePrefs(data.notification_preferences || {});
}

function saveNotifications() {
    if (!settingsUser.value?.id) {
        showToast('error', 'Impossible de charger ton profil. Recharge la page.');
        return;
    }

    formNotifications.notification_preferences = buildNotificationPreferencesPayload();
    formNotifications.patch(`${route('user.update')}?redirect=settings`, {
        preserveScroll: true,
        onSuccess: () => {
            formNotifications.clearErrors();
            initNotificationForm();
        },
        onError: (errors) => {
            const msg = firstFormError(errors)
                ?? firstFormError(formNotifications.errors)
                ?? 'Erreur lors de l’enregistrement des préférences.';
            showToast('error', msg);
        },
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

watch(settingsUser, (newUser) => {
    if (newUser?.id && !formNotifications.processing) {
        initNotificationForm();
    }
}, { immediate: true });

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
            <div class="rounded-box border border-base-300 bg-base-200/30 p-4">
                <h2 class="text-lg font-medium text-content-300">Préférences de notification</h2>
                <p class="mt-1 text-sm text-content-600">
                    Choisissez quelles notifications recevoir, comment (site, email) et à quelle fréquence.
                    Si « Aucune » est sélectionné ou si les notifications sont désactivées globalement, aucun email ne part.
                </p>
                <label class="mt-4 flex cursor-pointer items-center justify-between gap-4 rounded-lg border border-base-300/60 bg-base-100/50 px-3 py-3">
                    <span>
                        <span class="block text-sm font-medium text-content-200">Activer les notifications</span>
                        <span class="block text-xs text-content-600">
                            Désactive l’envoi sur le site et par email, même si des canaux sont configurés ci-dessous.
                        </span>
                    </span>
                    <input
                        v-model="formNotifications.notifications_enabled"
                        type="checkbox"
                        class="toggle toggle-primary"
                        :disabled="formNotifications.processing"
                    />
                </label>

                <div
                    v-if="formNotifications.hasErrors"
                    class="alert alert-error mt-4"
                    role="alert"
                >
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li
                            v-for="(msg, idx) in notificationSaveErrorMessages"
                            :key="idx"
                        >
                            {{ msg }}
                        </li>
                    </ul>
                </div>

                <div
                    v-if="notificationTypesFiltered.length > 0"
                    class="mt-4 space-y-6"
                    :class="{ 'opacity-50 pointer-events-none': !formNotifications.notifications_enabled }"
                >
                    <section v-if="notificationGroups.personal.length > 0" class="space-y-3">
                        <h3 class="text-sm font-semibold text-content-300">Activité personnelle</h3>
                        <div
                            v-for="[typeKey, config] in notificationGroups.personal"
                            :key="typeKey"
                            class="flex flex-wrap items-center justify-between gap-3 py-2 border-b border-base-300/50 last:border-0"
                        >
                            <div class="min-w-0 flex-1">
                                <label class="text-sm font-medium text-content-200">{{ config.label }}</label>
                                <p class="text-xs text-content-600">Actuellement : {{ channelsSummary(typeKey) }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
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
                                <select
                                    v-if="isFrequencyEditable(typeKey, config)"
                                    :value="preferenceFrequencyValue(typeKey)"
                                    class="select select-bordered select-sm max-w-xs"
                                    @change="setNotificationFrequency(typeKey, $event.target.value)"
                                >
                                    <option
                                        v-for="(label, freqKey) in notificationFrequencies"
                                        :key="freqKey"
                                        :value="freqKey"
                                    >
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section v-if="notificationGroups.admin.length > 0" class="space-y-3">
                        <h3 class="text-sm font-semibold text-content-300">Administration</h3>
                        <p class="text-xs text-content-600">
                            Visible dans le centre de notifications, onglet « À traiter » ou « Administration ».
                        </p>
                        <div
                            v-for="[typeKey, config] in notificationGroups.admin"
                            :key="typeKey"
                            class="flex flex-wrap items-center justify-between gap-3 py-2 border-b border-base-300/50 last:border-0"
                        >
                            <div class="min-w-0 flex-1">
                                <label class="text-sm font-medium text-content-200">{{ config.label }}</label>
                                <p class="text-xs text-content-600">Actuellement : {{ channelsSummary(typeKey) }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
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
                                <select
                                    v-if="isFrequencyEditable(typeKey, config)"
                                    :value="preferenceFrequencyValue(typeKey)"
                                    class="select select-bordered select-sm max-w-xs"
                                    @change="setNotificationFrequency(typeKey, $event.target.value)"
                                >
                                    <option
                                        v-for="(label, freqKey) in notificationFrequencies"
                                        :key="freqKey"
                                        :value="freqKey"
                                    >
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </section>
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
            <div class="rounded-box border border-base-300 bg-base-200/30 p-4">
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
                        class="flex flex-wrap items-center justify-between gap-4 py-4 px-3 rounded-box bg-base-100/50 border border-base-300/50"
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
                class="rounded-box border border-base-300 bg-base-200/30 p-4"
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
