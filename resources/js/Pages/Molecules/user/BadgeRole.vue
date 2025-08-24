<script setup>
/**
* BadgeRole Molecule (Atomic Design, DaisyUI)
*
* @description
* Affiche le rôle d'un utilisateur sous forme de badge coloré avec un tooltip explicatif.
* - Utilise l'atom Badge pour l'affichage
* - Utilise l'atom Tooltip pour l'aide contextuelle
* - Couleur et traduction du rôle via RoleManager
*
* @props {String} role - Rôle de l'utilisateur (user, admin, super_admin, player, game_master, guest)
*
* @see Badge, Tooltip
*/
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getRoleTranslation, getRoleColor } from "@/Utils/user/RoleManager";

const props = defineProps({
    role: {
        type: String,
        default: "user",
        validator: (value) => {
            // Si la valeur est undefined/null, on accepte (sera géré par RoleManager)
            if (!value) return true;
            
            const validRoles = ['user', 'admin', 'super_admin', 'player', 'game_master', 'guest'];
            return validRoles.includes(value);
        }
    },
});

const roleColor = computed(() => getRoleColor(props.role));
const roleTranslation = computed(() => getRoleTranslation(props.role));
</script>

<template>
    <Tooltip :content="`Rôle de l'utilisateur·trice : ${roleTranslation}`" placement="bottom">
        <Badge :color="roleColor" size="md" class="uppercase">
            {{ roleTranslation || 'Utilisateur·trice' }}
        </Badge>
    </Tooltip>
</template>
