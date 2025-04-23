/**
 * BadgeRole component that displays a user's role with a colored badge and tooltip.
 * Utilizes Atoms components for consistent styling and behavior.
 *
 * Props:
 * - role (String): The role of the user. Default is "user".
 *   Valid values are defined in the Roles utility.
 *
 * Computed:
 * - roleColor: The color associated with the role.
 * - roleTranslation: The translated name of the role.
 *
 * Methods:
 * - getRoleColor: Gets the color associated with a role.
 * - getRoleTranslation: Gets the translated name of a role.
 */
<script setup>
import { computed } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import Badge from "@/Pages/Atoms/text/Badge.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import { getRoleTranslation, getRoleColor } from "@/Utils/Roles";

const props = defineProps({
    ...commonProps,
    role: {
        type: String,
        default: "user",
        validator: (value) => {
            const validRoles = ['user', 'admin', 'moderator', 'contributor'];
            return validRoles.includes(value);
        }
    },
});

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const roleColor = computed(() => getRoleColor(props.role));
const roleTranslation = computed(() => getRoleTranslation(props.role));

</script>

<template>
    <BaseTooltip
        :tooltip="`Rôle de l'utilisateur·trice : ${roleTranslation}`"
        tooltip-position="bottom"
    >
        <div class="transition-all duration-200 hover:scale-105 hover:shadow-md">
            <Badge
                :color="roleColor"
                size="md"
                :theme="theme"
                class="uppercase"
            >
                {{ roleTranslation }}
            </Badge>
        </div>
    </BaseTooltip>
</template>
