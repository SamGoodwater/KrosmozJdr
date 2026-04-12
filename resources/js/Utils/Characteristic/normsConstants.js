/**
 * Constantes partagées pour le système de normes (chartes) des caractéristiques.
 * Utilisé par les composants admin et visualisation.
 */

export const POWER_LEVELS = ['very_weak', 'weak', 'neutral', 'strong', 'very_strong'];

export const POWER_LABELS = {
    very_weak: 'Très faible',
    weak: 'Faible',
    neutral: 'Neutre',
    strong: 'Fort',
    very_strong: 'Très fort',
};

export const POWER_COLORS = {
    very_weak: '#6b7280',
    weak: '#3b82f6',
    neutral: '#22c55e',
    strong: '#f59e0b',
    very_strong: '#ef4444',
};

export const POWER_BG_COLORS = {
    very_weak: 'rgba(107, 114, 128, 0.15)',
    weak: 'rgba(59, 130, 246, 0.15)',
    neutral: 'rgba(34, 197, 94, 0.15)',
    strong: 'rgba(245, 158, 11, 0.15)',
    very_strong: 'rgba(239, 68, 68, 0.15)',
};

export const NEUTRAL_INDEX = 2;
export const MAX_LEVEL = 20;

export const CONDITION_OPERATORS = ['>', '>=', '=', '<=', '<'];

export const CONDITION_TARGETS = [
    { value: 'power', label: 'Puissance (ligne)' },
    { value: 'level', label: 'Niveau (colonne)' },
];
