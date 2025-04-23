<script setup>
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import Avatar from "@/Pages/Molecules/images/Avatar.vue";
import Dropdown from "@/Pages/Atoms/actions/Dropdown.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import { usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const page = usePage();
const user = ref(page.props.auth.user);
const avatar = ref(user.value.avatar);
const pseudo = ref(user.value.name);

watch(
    () => page.props.auth.user,
    (newUser) => {
        user.value = newUser;
        avatar.value = newUser.avatar;
        pseudo.value = newUser.name;
    },
    { deep: true }
);
</script>

<template>
    <div class="flex justify-end">
        <div class="flex items-center mx-6 max-sm:mx-4">
            <Dropdown position="bottom-end">
                <BaseTooltip :tooltip="{ custom: true }" tooltip-position="bottom">
                    <div class="indicator">
                        <span class="indicator-item badge bg-secondary/80 badge-xs text-content-light">0</span>
                        <Btn theme="neutral" variant="link">
                            <i class="text-2xl fa-regular fa-bell"></i>
                        </Btn>
                    </div>
                    <template #tooltip>
                        <div>
                            <p>Vous avez 0 notifications</p>
                        </div>
                    </template>
                </BaseTooltip>

                <template #list>
                    <div id="notifications-panel" class="flex flex-col items-center justify-center min-h-32 max-h-96 overflow-y-auto text-content-dark light:bg-base-100/80 dark:bg-base-900/80 p-2">
                        <p>Vous avez 0 notifications</p>
                    </div>
                </template>
            </Dropdown>
        </div>
        <div class="flex flex-col text-center">
            <Dropdown position="bottom-end">
                <div class="flex items-center space-x-2">
                    <Avatar
                        :source="avatar"
                        :altText="pseudo"
                        size="sm"
                        rounded="full"
                        theme="primary"
                    />
                    <span>{{ pseudo.charAt(0).toUpperCase() + pseudo.slice(1) }}</span>
                </div>
                <template #list>
                    <li>
                        <Route route="user.dashboard">
                            <Btn theme="neutral" variant="link" size="md" label="Mon compte" />
                        </Route>
                    </li>
                    <li>
                        <Route route="logout" method="post">
                            <Btn theme="neutral" variant="link" size="md" label="Se déconnecter" />
                        </Route>
                    </li>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
