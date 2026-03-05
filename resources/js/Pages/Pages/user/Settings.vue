<script setup>
/**
 * Page Paramètres du compte.
 * Page dédiée aux paramètres (indépendante des notifications), avec onglets (Notifications, etc.).
 * L’ancre #notifications permet d’ouvrir directement l’onglet Notifications (ex. depuis le centre de notifications).
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Tab from '@/Pages/Molecules/navigation/Tab.vue';
import TabItem from '@/Pages/Atoms/navigation/TabItem.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const page = usePage();
const { success, error } = useNotificationStore();

const TAB_NOTIFICATIONS = 'notifications';
const activeTab = ref(TAB_NOTIFICATIONS);

const user = computed(() => {
    const userData = page.props.user || {};
    if (userData.data && typeof userData.data === 'object' && userData.data.id) {
        return userData.data;
    }
    return userData;
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
    if (hash === TAB_NOTIFICATIONS) activeTab.value = TAB_NOTIFICATIONS;
}

function syncHashToTab() {
    if (activeTab.value === TAB_NOTIFICATIONS) {
        const url = new URL(window.location.href);
        url.hash = TAB_NOTIFICATIONS;
        window.history.replaceState(null, '', url.toString());
    }
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

        <Tab variant="lift" size="md" class="mb-4">
            <TabItem
                :id="TAB_NOTIFICATIONS"
                :active="activeTab === TAB_NOTIFICATIONS"
                label="Notifications"
                icon="fa-bell"
                @click.prevent="activeTab = TAB_NOTIFICATIONS"
            />
        </Tab>

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
                    Aucune préférence de notification configurée pour votre rôle.
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
    </div>
</template>
