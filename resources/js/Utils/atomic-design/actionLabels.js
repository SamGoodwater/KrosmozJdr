/**
 * Lexique commun des boutons d’action (libellés, libellés de traitement, icônes).
 *
 * @description
 * Source unique des termes utilisés par les en-têtes de page, les docks d’édition et les
 * formulaires. Règles :
 * - `save` : action principale d’un formulaire ou d’une page (« Enregistrer ») ;
 * - `discard` : revient à l’état enregistré, on reste sur la page ;
 * - `back` : quitte la page ; `close` : ferme une modale ou un panneau ;
 * - `create`, `delete`, `run`, `confirm` : actions spécifiques.
 *
 * @example
 * import { ACTION } from '@/Utils/atomic-design/actionLabels';
 * <Btn>{{ processing ? ACTION.save.processing : ACTION.save.label }}</Btn>
 */

/** @typedef {{ label: string, processing?: string, icon: string }} ActionDescriptor */

/** @type {Readonly<Record<string, ActionDescriptor>>} */
export const ACTION = Object.freeze({
    save: { label: 'Enregistrer', processing: 'Enregistrement…', icon: 'fa-solid fa-floppy-disk' },
    discard: { label: 'Annuler les modifications', icon: 'fa-solid fa-arrow-rotate-left' },
    back: { label: 'Retour', icon: 'fa-solid fa-arrow-left' },
    close: { label: 'Fermer', icon: 'fa-solid fa-xmark' },
    create: { label: 'Créer', processing: 'Création…', icon: 'fa-solid fa-plus' },
    edit: { label: 'Modifier', icon: 'fa-solid fa-pen' },
    delete: { label: 'Supprimer', icon: 'fa-solid fa-trash' },
    run: { label: 'Lancer', processing: 'Lancement…', icon: 'fa-solid fa-play' },
    confirm: { label: 'Confirmer', icon: 'fa-solid fa-check' },
    refresh: { label: 'Actualiser', icon: 'fa-solid fa-rotate' },
});

/** Messages liés aux modifications non enregistrées. */
export const UNSAVED = Object.freeze({
    badge: 'Modifications non enregistrées',
    leaveTitle: 'Modifications non enregistrées',
    leaveMessage: 'Des modifications n’ont pas été enregistrées. Quitter la page les fera perdre.',
    leaveConfirm: 'Quitter sans enregistrer',
    leaveCancel: 'Rester sur la page',
});
