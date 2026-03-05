<script setup>
/**
* Page compte utilisateur.
*
* - Affichage profil utilisateur (avatar, nom, email, rôle, actions)
* - Bloc capacités selon rôle
* - Bloc autorisations spécifiques via relations (accès dédiés)
* - Sans visuels de démonstration non connectés
*/
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

// Atoms & Molecules (nouveaux chemins)
import Avatar from '@/Pages/Atoms/data-display/Avatar.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import BadgeRole from '@/Pages/Molecules/user/BadgeRole.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EntityLabel from '@/Pages/Atoms/data-display/EntityLabel.vue';
import VerifyMailAlert from '@/Pages/Molecules/user/VerifyMailAlert.vue';

const page = usePage();
const RELATION_DISPLAY_LIMIT = 4;

// Le user est passé via page.props.user (UserResource)
// On utilise computed pour réactivité
// Note: Si les données sont dans user.data (pagination), on les extrait
const user = computed(() => {
    const userData = page.props.user || {};
    // Si les données sont dans user.data (cas de pagination), on les extrait
    if (userData.data && typeof userData.data === 'object') {
        return userData.data;
    }
    return userData;
});
const { setPageTitle } = usePageTitle();

const roleValue = computed(() => Number(user.value?.role ?? 0));
const canManageEntities = computed(() => roleValue.value >= 3);

const roleCapabilities = computed(() => {
    const isGameMasterOrMore = roleValue.value >= 3;
    const isAdminOrMore = roleValue.value >= 4;

    return [
        {
            key: 'manage-entities',
            label: 'Créer et gérer les contenus du jeu',
            enabled: isGameMasterOrMore,
            hint: isGameMasterOrMore
                ? 'Disponible à partir du rôle meneur de jeu.'
                : 'Réservé aux meneurs de jeu et aux administrateurs.',
        },
        {
            key: 'manage-content',
            label: 'Gérer les pages et les sections',
            enabled: isAdminOrMore,
            hint: isAdminOrMore
                ? 'Disponible pour les administrateurs.'
                : 'Réservé aux administrateurs.',
        },
    ];
});

const entityShortcuts = computed(() => ([
    {
        key: 'spells',
        label: 'Gérer les sorts',
        entityType: 'spell',
        routeName: 'entities.spells.index',
        colorVar: 'var(--color-spell-800)',
    },
    {
        key: 'resources',
        label: 'Gérer les ressources',
        entityType: 'resource',
        routeName: 'entities.resources.index',
        colorVar: 'var(--color-resource-800)',
    },
    {
        key: 'items',
        label: 'Gérer les équipements',
        entityType: 'item',
        routeName: 'entities.items.index',
        colorVar: 'var(--color-item-800)',
    },
    {
        key: 'consumables',
        label: 'Gérer les consommables',
        entityType: 'consumable',
        routeName: 'entities.consumables.index',
        colorVar: 'var(--color-consumable-800)',
    },
    {
        key: 'panoplies',
        label: 'Gérer les panoplies',
        entityType: 'panoply',
        routeName: 'entities.panoplies.index',
        colorVar: 'var(--color-panoply-800)',
    },
    {
        key: 'breeds',
        label: 'Gérer les classes',
        entityType: 'breed',
        routeName: 'entities.breeds.index',
        colorVar: 'var(--color-breed-800)',
    },
    {
        key: 'monsters',
        label: 'Gérer les monstres',
        entityType: 'monster',
        routeName: 'entities.monsters.index',
        colorVar: 'var(--color-monster-800)',
    },
]));

const getShortcutCardStyle = (colorVar) => ({
    backgroundColor: `color-mix(in srgb, ${colorVar} 8%, var(--color-base-100))`,
    borderColor: `color-mix(in srgb, ${colorVar} 35%, var(--color-base-300))`,
    boxShadow: `0 10px 24px -18px ${colorVar}`,
});

const relationGroups = computed(() => ([
    {
        key: 'pages',
        label: 'Pages',
        entityType: 'page',
        routeName: 'pages.show',
        indexRouteName: 'pages.index',
    },
    {
        key: 'sections',
        label: 'Sections',
        entityType: 'section',
        routeName: 'sections.show',
        indexRouteName: 'sections.index',
    },
    {
        key: 'campaigns',
        label: 'Campagnes',
        entityType: 'campaign',
        routeName: 'entities.campaigns.show',
        indexRouteName: 'entities.campaigns.index',
    },
    {
        key: 'scenarios',
        label: 'Scénarios',
        entityType: 'scenario',
        routeName: 'entities.scenarios.show',
        indexRouteName: 'entities.scenarios.index',
    },
]));

const accessGroups = computed(() => {
    return relationGroups.value.map((group) => {
        const all = Array.isArray(user.value?.[group.key]) ? user.value[group.key] : [];
        return {
            ...group,
            total: all.length,
            visible: all.slice(0, RELATION_DISPLAY_LIMIT),
            hiddenCount: Math.max(0, all.length - RELATION_DISPLAY_LIMIT),
        };
    });
});

const hasSpecificAccess = computed(() => accessGroups.value.some((group) => group.total > 0));

const resolveItemLabel = (item) => {
    return item?.name || item?.title || item?.slug || (item?.id ? `#${item.id}` : 'Sans nom');
};

const resolveItemRouteParam = (item) => item?.slug || item?.id || null;

const resolveItemHref = (routeName, item) => {
    const routeParam = resolveItemRouteParam(item);
    if (!routeParam || typeof route === 'undefined') return '#';
    try {
        return route(routeName, routeParam);
    } catch {
        return '#';
    }
};

const hasPivotAccess = (item) => Boolean(item?.pivot);

const levelSummary = (item) => {
    const readLevel = Number.isInteger(item?.read_level) ? item.read_level : null;
    const writeLevel = Number.isInteger(item?.write_level) ? item.write_level : null;
    if (readLevel === null && writeLevel === null) return '';
    if (readLevel !== null && writeLevel !== null) {
        return `Lecture ${readLevel} - Édition ${writeLevel}`;
    }
    return readLevel !== null ? `Lecture ${readLevel}` : `Édition ${writeLevel}`;
};

onMounted(() => {
    setPageTitle('Mon Compte');
});
</script>

<template>

    <Head title="Mon Compte" />
    <Container class="space-y-6">
        <!-- Profil utilisateur -->
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between gap-6 max-sm:gap-3 flex-wrap">
                <!-- Infos utilisateur -->
                <div
                    class="flex items-center gap-4 max-md:gap-6 max-sm:gap-2 max-[930px]:flex-wrap justify-between w-full">
                    <div class="flex items-center justify-center space-x-4">
                        <Avatar 
                            :src="user?.avatar || ''" 
                            :label="user?.name || 'Utilisateur'" 
                            :alt="user?.name || 'Utilisateur'" 
                            size="xl" 
                            rounded="full" 
                        />
                        <div>
                            <h2 class="text-2xl font-bold text-primary-100">{{ user?.name || 'Utilisateur' }}</h2>
                            <p class="text-primary-200">{{ user?.email || 'email@example.com' }}</p>
                            <div class="mt-2">
                                <BadgeRole :role="user?.role_name || 'user'" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 max-[930px]:w-full">
                        <Tooltip content="Paramètres du compte (notifications, etc.)" placement="top">
                            <Route route="user.settings">
                                <Btn color="neutral" variant="outline" size="sm">Paramètres</Btn>
                            </Route>
                        </Tooltip>
                        <Tooltip content="Modifier mon profil" placement="top">
                            <Route route="user.edit">
                                <Btn color="primary" size="sm">Éditer</Btn>
                            </Route>
                        </Tooltip>
                    </div>
                </div>
                <div v-if="user && !user.is_verified">
                    <VerifyMailAlert />
                </div>
            </div>
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <div class="rounded-(--radius-box) border border-base-300 bg-base-200/30 p-4 space-y-3">
                <h3 class="text-lg font-bold text-primary-100">Capacités selon votre rôle</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div
                        v-for="capability in roleCapabilities"
                        :key="capability.key"
                        class="rounded-(--radius-field) border border-base-300 bg-base-100/60 px-3 py-2"
                    >
                        <div class="flex items-center gap-2">
                            <Icon
                                :source="capability.enabled ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark'"
                                size="sm"
                                :class="capability.enabled ? 'text-success' : 'text-warning'"
                            />
                            <p class="font-semibold text-primary-100">{{ capability.label }}</p>
                        </div>
                        <p class="mt-1 text-sm text-primary-200">{{ capability.hint }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-(--radius-box) border border-base-300 bg-base-200/30 p-4 space-y-3">
                <h3 class="text-lg font-bold text-primary-100">Raccourcis de gestion</h3>
                <p class="text-sm text-primary-200">
                    Accès rapide aux contenus principaux du jeu.
                </p>

                <div v-if="canManageEntities" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2">
                    <Route
                        v-for="shortcut in entityShortcuts"
                        :key="shortcut.key"
                        :route="shortcut.routeName"
                        class="no-underline"
                    >
                        <div
                            class="rounded-(--radius-field) border px-3 py-2 transition-all duration-200 hover:-translate-y-px hover:brightness-105"
                            :style="getShortcutCardStyle(shortcut.colorVar)"
                        >
                            <div class="flex items-center gap-2">
                                <EntityLabel
                                    :entity="shortcut.entityType"
                                    variant="icon-inline"
                                    size="xs"
                                />
                                <span class="text-sm font-semibold text-primary-100">
                                    {{ shortcut.label }}
                                </span>
                            </div>
                        </div>
                    </Route>
                </div>

                <div v-else class="rounded-(--radius-field) border border-base-300 bg-base-100/60 p-3">
                    <p class="text-sm text-primary-200">
                        Ces raccourcis de gestion sont disponibles a partir du role meneur de jeu.
                    </p>
                </div>
            </div>

            <div class="rounded-(--radius-box) border border-base-300 bg-base-200/30 p-4 space-y-3">
                <h3 class="text-lg font-bold text-primary-100">Accès personnalisés</h3>
                <p class="text-sm text-primary-200">
                    Voici les accès supplémentaires accordés sur certains contenus, en plus des droits liés à votre rôle.
                </p>

                <div v-if="hasSpecificAccess" class="space-y-4">
                    <div
                        v-for="group in accessGroups"
                        :key="group.key"
                        v-show="group.total > 0"
                        class="space-y-2"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-primary-100">{{ group.label }}</p>
                            <span class="badge badge-ghost badge-sm">{{ group.total }}</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div
                                v-for="item in group.visible"
                                :key="`${group.key}-${item.id || item.slug}`"
                                class="flex items-center justify-between gap-3 rounded-(--radius-field) border border-base-300 bg-base-100/60 px-3 py-2"
                            >
                                <div class="flex items-center gap-2 min-w-0">
                                    <EntityLabel
                                        :entity="group.entityType"
                                        variant="icon-inline"
                                        size="xs"
                                        :label="group.label"
                                    />
                                    <Route
                                        :href="resolveItemHref(group.routeName, item)"
                                        color="primary"
                                        hover
                                        class="truncate"
                                    >
                                        {{ resolveItemLabel(item) }}
                                    </Route>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span v-if="hasPivotAccess(item)" class="badge badge-info badge-xs">Accès accordé</span>
                                    <span v-if="levelSummary(item)" class="badge badge-ghost badge-xs">{{ levelSummary(item) }}</span>
                                </div>
                            </div>
                        </div>

                        <p v-if="group.hiddenCount > 0" class="text-xs text-primary-300">
                            +{{ group.hiddenCount }} autre(s) {{ group.label.toLowerCase() }} non affiché(s) pour garder une vue compacte.
                        </p>
                        <div v-if="group.hiddenCount > 0" class="flex justify-end">
                            <Route
                                :route="group.indexRouteName"
                                color="primary"
                                hover
                                class="text-xs"
                            >
                                Voir tout
                            </Route>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded-(--radius-field) border border-base-300 bg-base-100/60 p-3">
                    <p class="text-sm text-primary-200">
                        Aucun accès personnalisé supplémentaire pour le moment.
                    </p>
                </div>
            </div>
        </div>
    </Container>
</template>
