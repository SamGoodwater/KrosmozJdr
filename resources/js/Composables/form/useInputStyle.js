// Composable utilitaire pour générer dynamiquement les classes d'input selon le style, la couleur, la taille, la variante, etc.
// Usage : getInputClasses({ style: 'classic', color: 'primary', size: 'md', variant: 'outline', error: false })

const styleMap = {
    classic: ({ color, size, variant, error }) => [
        'input',
        color === 'neutral' && 'input-neutral',
        color === 'primary' && 'input-primary',
        color === 'secondary' && 'input-secondary',
        color === 'accent' && 'input-accent',
        color === 'info' && 'input-info',
        color === 'success' && 'input-success',
        color === 'warning' && 'input-warning',
        color === 'error' && 'input-error',
        size === 'xs' && 'input-xs',
        size === 'sm' && 'input-sm',
        size === 'md' && 'input-md',
        size === 'lg' && 'input-lg',
        size === 'xl' && 'input-xl',
        variant === 'ghost' && 'input-ghost',
        variant === 'outline' && 'input-outline',
        variant === 'bordered' && 'input-bordered',
        variant === 'glass' && 'glass',
        error && 'validator input-error',
    ].filter(Boolean).join(' '),
    floating: ({ color, size, variant, error }) => [
        'input',
        'floating-input', // à définir dans le CSS si besoin
        color === 'primary' && 'input-primary',
        size === 'md' && 'input-md',
        variant === 'outline' && 'input-outline',
        error && 'validator input-error',
    ].filter(Boolean).join(' '),
    minimal: ({ color, size, error }) => [
        'bg-transparent',
        'border-b',
        'focus:outline-none',
        color === 'primary' && 'border-primary',
        size === 'md' && 'text-base',
        error && 'border-error',
    ].filter(Boolean).join(' '),
};

export function getInputClasses({ style = 'classic', color = '', size = '', variant = '', error = false } = {}) {
    const fn = styleMap[style] || styleMap.classic;
    return fn({ color, size, variant, error });
} 