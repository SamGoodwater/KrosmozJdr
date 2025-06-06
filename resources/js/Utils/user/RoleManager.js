export const ROLES = {
    GUEST: 0,
    USER: 1,
    PLAYER: 2,
    GAME_MASTER: 3,
    ADMIN: 4,
    SUPER_ADMIN: 5,
};

export const ROLES_TRANSLATION = {
    [ROLES.GUEST]: "Invité·e",
    [ROLES.USER]: "Utilisateur·trice",
    [ROLES.PLAYER]: "Joueur·euse",
    [ROLES.GAME_MASTER]: "Maître du jeu",
    [ROLES.ADMIN]: "Administrateur·trice",
    [ROLES.SUPER_ADMIN]: "Super administrateur·trice",
};

export const ROLES_COLORS = {
    [ROLES.GUEST]: "gray-700",
    [ROLES.USER]: "blue-700",
    [ROLES.PLAYER]: "green-700",
    [ROLES.GAME_MASTER]: "yellow-700",
    [ROLES.ADMIN]: "orange-700",
    [ROLES.SUPER_ADMIN]: "red-700",
};

export function verifyRole(userRole, requiredRole) {
    // Si l'utilisateur est super_admin, il a tous les droits
    if (userRole === ROLES.SUPER_ADMIN) {
        return true;
    }
    return userRole >= requiredRole;
}

export function getRoleTranslation(role) {
    return (
        ROLES_TRANSLATION[role].charAt(0).toUpperCase() +
        ROLES_TRANSLATION[role].slice(1)
    );
}

export function getRoleColor(role) {
    return ROLES_COLORS[role];
}
