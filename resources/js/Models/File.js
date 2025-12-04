/**
 * Modèle File pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de fichier côté frontend.
 * 
 * @example
 * const file = new File(props.file);
 * console.log(file.title); // Accès normalisé
 * console.log(file.url); // URL du fichier
 */
import { BaseModel } from './BaseModel';

export class File extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get file() {
        return this._data.file || '';
    }

    get title() {
        return this._data.title || '';
    }

    get comment() {
        return this._data.comment || '';
    }

    get description() {
        return this._data.description || '';
    }

    // ============================================
    // RELATIONS
    // ============================================

    get sections() {
        return this._data.sections || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne l'URL du fichier
     * @returns {string}
     */
    get url() {
        if (!this.file) return '';
        // Si c'est déjà une URL complète, la retourner
        if (this.file.startsWith('http://') || this.file.startsWith('https://')) {
            return this.file;
        }
        // Sinon, construire l'URL relative
        return `/${this.file}`;
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            file: this.file,
            title: this.title,
            comment: this.comment,
            description: this.description
        };
    }
}

export default File;

