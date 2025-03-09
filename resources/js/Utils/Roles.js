export const ROLES = {
    GUEST: "guest",
    USER: "user",
    PLAYER: "player",
    GAME_MASTER: "game_master",
    CONTRIBUTOR: "contributor",
    MODERATOR: "moderator",
    ADMIN: "admin",
    SUPER_ADMIN: "super_admin",
};

export const ROLES_TRANSLATION = {
    [ROLES.GUEST]: "Invité·e",
    [ROLES.USER]: "Utilisateur·trice",
    [ROLES.PLAYER]: "Joueur·euse",
    [ROLES.GAME_MASTER]: "Maître du jeu",
    [ROLES.CONTRIBUTOR]: "Contributeur·trice",
    [ROLES.MODERATOR]: "Modérateur·trice",
    [ROLES.ADMIN]: "Administrateur·trice",
    [ROLES.SUPER_ADMIN]: "Super administrateur·trice",
};

export function verifyRole(userRole, requiredRole) {
    // Si l'utilisateur est super_admin, il a tous les droits
    if (userRole === ROLES.SUPER_ADMIN) {
        return true;
    }

    // Vérification des rôles
    switch (requiredRole) {
        case ROLES.GUEST:
            return [
                ROLES.GUEST,
                ROLES.USER,
                ROLES.PLAYER,
                ROLES.GAME_MASTER,
                ROLES.CONTRIBUTOR,
                ROLES.MODERATOR,
                ROLES.ADMIN,
                ROLES.SUPER_ADMIN,
            ].includes(userRole);

        case ROLES.USER:
            return [
                ROLES.USER,
                ROLES.PLAYER,
                ROLES.GAME_MASTER,
                ROLES.CONTRIBUTOR,
                ROLES.MODERATOR,
                ROLES.ADMIN,
                ROLES.SUPER_ADMIN,
            ].includes(userRole);

        case ROLES.PLAYER:
            return [
                ROLES.PLAYER,
                ROLES.GAME_MASTER,
                ROLES.CONTRIBUTOR,
                ROLES.MODERATOR,
                ROLES.ADMIN,
                ROLES.SUPER_ADMIN,
            ].includes(userRole);

        case ROLES.GAME_MASTER:
            return [
                ROLES.GAME_MASTER,
                ROLES.CONTRIBUTOR,
                ROLES.MODERATOR,
                ROLES.ADMIN,
                ROLES.SUPER_ADMIN,
            ].includes(userRole);

        case ROLES.CONTRIBUTOR:
            return [
                ROLES.CONTRIBUTOR,
                ROLES.MODERATOR,
                ROLES.ADMIN,
                ROLES.SUPER_ADMIN,
            ].includes(userRole);

        case ROLES.MODERATOR:
            return [ROLES.MODERATOR, ROLES.ADMIN, ROLES.SUPER_ADMIN].includes(
                userRole,
            );

        case ROLES.ADMIN:
            return [ROLES.ADMIN, ROLES.SUPER_ADMIN].includes(userRole);

        case ROLES.SUPER_ADMIN:
            return userRole === ROLES.SUPER_ADMIN;

        default:
            return false;
    }
}

export function getRoleTranslation(role) {
    return (
        ROLES_TRANSLATION[role].charAt(0).toUpperCase() +
        ROLES_TRANSLATION[role].slice(1)
    );
}
