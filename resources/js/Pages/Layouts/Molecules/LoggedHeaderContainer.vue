<script setup>
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import Avatar from "@/Pages/Atoms/images/Avatar.vue";
import Dropdown from "@/Pages/Atoms/actions/Dropdown.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";

const page = usePage();
const user = ref(page.props.auth.user);
const image = ref(user.value.image);
const pseudo = ref(user.value.name);

watch(
    () => page.props.auth.user,
    (newUser) => {
        user.value = newUser;
        image.value = newUser.image;
        pseudo.value = newUser.name;
    },
);
</script>

<template>
    <div class="flex flex-col text-center">
        <Dropdown placement="bottom-end">
            <template #label>
                <div class="flex items-center space-x-2">
                    <Avatar
                        :source="image"
                        :altText="pseudo"
                        size="sm"
                        rounded="full"
                    />
                    <span>{{ pseudo.charAt(0).toUpperCase() + pseudo.slice(1) }}</span>
                </div>
            </template>
            <template #list>
                <li>
                    <Route route="user.dashboard">
                        <Btn theme="neutral link md" label="Mon compte" />
                    </Route>
                </li>
                <li>
                    <Route route="logout" method="post">
                        <Btn theme="neutral link md" label="Se déconnecter" />
                    </Route>
                </li>
            </template>
        </Dropdown>
    </div>
</template>
