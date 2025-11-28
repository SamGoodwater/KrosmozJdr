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

/**
 * Normalise un rôle (chaîne ou entier) en entier
 * @param {string|number} role - Rôle sous forme de chaîne ('admin', 'user') ou entier (4, 1)
 * @returns {number|null} - Rôle normalisé en entier ou null si invalide
 */
function normalizeRole(role) {
    if (role === null || role === undefined) {
        return null;
    }
    
    // Si c'est déjà un entier, on le retourne tel quel
    if (typeof role === 'number') {
        return role;
    }
    
    // Si c'est une chaîne, on la convertit en entier
    if (typeof role === 'string') {
        const roleMap = {
            'guest': ROLES.GUEST,
            'user': ROLES.USER,
            'player': ROLES.PLAYER,
            'game_master': ROLES.GAME_MASTER,
            'admin': ROLES.ADMIN,
            'super_admin': ROLES.SUPER_ADMIN,
        };
        return roleMap[role] !== undefined ? roleMap[role] : null;
    }
    
    return null;
}

export function getRoleTranslation(role) {
    const normalizedRole = normalizeRole(role);
    
    // Protection contre les valeurs undefined/null
    if (normalizedRole === null || !ROLES_TRANSLATION[normalizedRole]) {
        return "Utilisateur·trice";
    }
    
    return (
        ROLES_TRANSLATION[normalizedRole].charAt(0).toUpperCase() +
        ROLES_TRANSLATION[normalizedRole].slice(1)
    );
}

export function getRoleColor(role) {
    const normalizedRole = normalizeRole(role);
    
    // Protection contre les valeurs undefined/null
    if (normalizedRole === null || !ROLES_COLORS[normalizedRole]) {
        return "blue-700"; // Couleur par défaut pour user
    }
    
    return ROLES_COLORS[normalizedRole];
}
