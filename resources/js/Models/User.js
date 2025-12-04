/**
 * Modèle User pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données d'utilisateur côté frontend.
 * 
 * @example
 * const user = new User(props.user);
 * console.log(user.name); // Accès normalisé
 * console.log(user.roleName); // Nom du rôle
 * console.log(user.avatar); // URL de l'avatar
 */
import { BaseModel } from './BaseModel';

export class User extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get email() {
        return this._data.email || '';
    }

    get role() {
        return this._data.role || 0;
    }

    get roleName() {
        return this._data.role_name || 'guest';
    }

    get avatar() {
        return this._data.avatar || '';
    }

    get avatarIsDefault() {
        return this._data.avatar_is_default || false;
    }

    get notificationsEnabled() {
        return this._data.notifications_enabled ?? true;
    }

    get notificationChannels() {
        return this._data.notification_channels || ['database'];
    }

    get isVerified() {
        return this._data.is_verified || false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get pages() {
        return this._data.pages || [];
    }

    get sections() {
        return this._data.sections || [];
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Vérifie si l'utilisateur est un administrateur
     * @returns {boolean}
     */
    get isAdmin() {
        return this.role >= 4; // admin ou super_admin
    }

    /**
     * Vérifie si l'utilisateur est un super administrateur
     * @returns {boolean}
     */
    get isSuperAdmin() {
        return this.role === 5;
    }

    /**
     * Vérifie si l'utilisateur est un maître de jeu
     * @returns {boolean}
     */
    get isGameMaster() {
        return this.role >= 3;
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            name: this.name,
            email: this.email,
            role: this.role,
            notifications_enabled: this.notificationsEnabled,
            notification_channels: this.notificationChannels
        };
    }
}

export default User;

