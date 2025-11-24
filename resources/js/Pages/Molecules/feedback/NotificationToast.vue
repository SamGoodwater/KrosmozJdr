<script setup>
defineOptions({ inheritAttrs: false });

/**
 * NotificationToast Molecule (Toast + Alert + Animations avancées)
 *
 * @description
 * Molecule pour afficher une notification toast avec animations avancées, cycle full/contracted,
 * barre de progression, et interactions hover.
 * - Props : id, message, type, icon, onClick, actions, onClose, placement, duration
 * - Cycle de vie : 40% full → 60% contracted → auto-dismiss
 * - Animations : fade-in, contract/expand, hover interactions
 * - Barre de progression : bordure bas qui se remplit selon le temps restant
 * - Intégration avec l'atom Icon pour les icônes par défaut
 * - Accessibilité (role alert, aria-live, etc.)
 *
 * @see https://daisyui.com/components/toast/
 * @see https://daisyui.com/components/alert/
 *
 * @example
 * <NotificationToast message="Enregistré !" type="success" :onClose="closeFn" />
 *
 * @props {Number} id - Identifiant unique de la notification
 * @props {String} message - Message à afficher
 * @props {String} type - Type de notification ('success', 'error', 'info', 'warning', 'primary', 'secondary')
 * @props {String} icon - Icône personnalisée (optionnel)
 * @props {Function} onClick - Action au click (optionnel)
 * @props {Array|undefined} actions - Actions custom (boutons, etc.)
 * @props {Function|undefined} onClose - Callback fermeture (optionnel)
 * @props {String} placement - Placement du toast (optionnel)
 * @props {Number} duration - Durée d'affichage (ms)
 * @props {Number} createdAt - Timestamp de création
 * @props {Number} fullDisplayTime - Temps en mode full
 * @props {Number} contractedDisplayTime - Temps en mode contracted
 * @slot default - Contenu custom (remplace le message)
 * @slot actions - Slot pour actions custom
 */
import { computed, ref, onMounted, onUnmounted } from 'vue';
import Toast from '@/Pages/Atoms/feedback/Toast.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    id: { type: [Number, String], required: true },
    message: { type: String, default: '' },
    type: { type: String, default: '', validator: v => ['', 'success', 'error', 'info', 'warning', 'primary', 'secondary'].includes(v) },
    icon: { type: String, default: null },
    onClick: { type: Function, default: null },
    actions: { type: [Array, null], default: null },
    onClose: { type: Function, default: null },
    placement: { type: String, default: '' },
    duration: { type: Number, default: 12000 },
    createdAt: { type: Number, default: 0 },
    fullDisplayTime: { type: Number, default: 0 },
    contractedDisplayTime: { type: Number, default: 0 },
});

const { getProgressPercentage, getNotificationState } = useNotificationStore();

// État local
const isHovered = ref(false);
const isExpanded = ref(false);
const progressInterval = ref(null);
// Ref réactive pour forcer la mise à jour des computed basés sur le temps
const currentTime = ref(Date.now());

// Icône par défaut selon le type (FontAwesome)
const defaultIconMap = {
    success: 'fa-check-circle',
    error: 'fa-exclamation-circle',
    info: 'fa-info-circle',
    warning: 'fa-exclamation-triangle',
    primary: 'fa-star',
    secondary: 'fa-circle',
    '': 'fa-bell',
};

const iconSource = computed(() => {
    return props.icon || defaultIconMap[props.type] || defaultIconMap[''];
});

// État de la notification (full/contracted)
// Utilise currentTime pour forcer la réactivité
const notificationState = computed(() => {
    // Force la dépendance réactive
    currentTime.value; // eslint-disable-line no-unused-expressions
    if (isHovered.value && isExpanded.value) return 'expanded';
    return getNotificationState(props);
});

// Barre de progression
// Utilise currentTime pour forcer la réactivité
const progressPercentage = computed(() => {
    // Force la dépendance réactive
    currentTime.value; // eslint-disable-line no-unused-expressions
    return getProgressPercentage(props);
});

// Classes CSS pour les animations
const notificationClasses = computed(() => {
    const baseClasses = [
        'notification-toast',
        'transition-all duration-300 ease-in-out',
        'cursor-pointer',
        'relative',
        'overflow-hidden',
    ];

    // Classes selon l'état
    if (notificationState.value === 'full' || notificationState.value === 'expanded') {
        baseClasses.push('w-80 max-w-sm');
    } else {
        baseClasses.push('w-12 h-12 rounded-full');
        // Justification selon le placement pour les notifications contractées
        if (props.placement?.includes('right')) {
            baseClasses.push('justify-self-end');
        } else {
            baseClasses.push('justify-self-start');
        }
    }

    return baseClasses;
});

// Gestion des événements
function handleClick() {
    if (props.onClick) {
        props.onClick();
    } else {
        // Par défaut, fermer la notification
        if (props.onClose) {
            props.onClose();
        }
    }
}

function handleMouseEnter() {
    isHovered.value = true;
    if (notificationState.value === 'contracted') {
        isExpanded.value = true;
    }
}

function handleMouseLeave() {
    isHovered.value = false;
    isExpanded.value = false;
}

function handleClose(e) {
    e.stopPropagation();
    if (props.onClose) {
        props.onClose();
    }
}

// Mise à jour de la barre de progression et de l'état
function startProgressUpdate() {
    progressInterval.value = setInterval(() => {
        // Mettre à jour currentTime pour forcer le recalcul des computed
        currentTime.value = Date.now();
    }, 100);
}

function stopProgressUpdate() {
    if (progressInterval.value) {
        clearInterval(progressInterval.value);
        progressInterval.value = null;
    }
}

onMounted(() => {
    startProgressUpdate();
});

onUnmounted(() => {
    stopProgressUpdate();
});
</script>

<template>

    <!-- Notification avec animations -->
    <div 
        :class="notificationClasses"
        @click="handleClick"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        role="alert"
        :aria-label="`Notification ${type}: ${message}`"
    >
        <Toast :vertical="placement?.includes('top') ? 'top' : 'bottom'"
        :horizontal="placement?.includes('right') ? 'end' : 'start'"
        :type="type">
        
            <!-- Barre de progression (bordure bas) -->
            <div 
                class="absolute bottom-0 left-0 h-0.5 bg-current opacity-50 transition-all duration-100 ease-linear"
                :style="{ width: `${progressPercentage}%` }"
            ></div>

            <!-- Contenu de la notification -->
            <div class="flex items-center gap-3 p-3">
                <!-- Icône -->
                <div class="flex-shrink-0">
                    <Icon 
                        :source="iconSource" 
                        :alt="`Icône ${type}`" 
                        size="md"
                    />
                </div>

                <!-- Contenu (message + actions) -->
                <div v-if="notificationState === 'full' || notificationState === 'expanded'" 
                     class="flex-1 min-w-0">
                    
                    <!-- Message -->
                    <div class="text-sm font-medium">
                        <slot>{{ message }}</slot>
                    </div>

                    <!-- Actions -->
                    <div v-if="actions && actions.length" class="flex items-center gap-2 mt-2">
                        <template v-for="(action, idx) in actions" :key="idx">
                            <Btn 
                                v-bind="action" 
                                @click.stop="action.onClick" 
                                size="xs" 
                                variant="ghost" 
                            />
                        </template>
                    </div>
                    <slot name="actions" />
                </div>
            </div>

            <!-- Bouton fermeture -->
             <Tooltip :content="'Fermer la notification'"  class="absolute top-2 right-2 z-10" placement="left">
                <Btn 
                    v-if="onClose && (notificationState === 'full' || notificationState === 'expanded')"
                    size="xs" 
                    variant="ghost" 
                    @click="handleClose"
                   
                    :aria-label="'Fermer la notification'"
                >
                    <template #content>
                        <Icon source="fa-xmark" size="xs" />
                        </template>
                    </Btn>
            </Tooltip>
        </Toast>
    </div>
</template>

<style scoped>
.notification-toast {
    display: block;
    width: 100%;
    margin-bottom: 0.5rem; /* Espacement entre les notifications */
}

.notification-toast:last-child {
    margin-bottom: 0; /* Pas de marge pour la dernière notification */
}

.notification-toast:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px hsl(var(--bc) / 0.1), 0 4px 6px -2px hsl(var(--bc) / 0.05);
}

/* Mode contracted */
.notification-toast.w-12 {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem !important; /* Force la largeur en mode contracted */
    height: 3rem !important;
    min-width: 3rem;
    min-height: 3rem;
    /* Assure que la notification contractée prend sa place dans le grid */
    margin-bottom: 0;
}

.notification-toast.w-12 .toast-custom {
    min-height: 0;
    padding: 0;
    width: 100%;
    height: 100%;
}

.notification-toast.w-12 .flex {
    justify-content: center;
    padding: 0;
    gap: 0;
}

.notification-toast.w-12 .flex-1 {
    display: none;
}

/* Animation de contraction/expansion */
.notification-toast {
    transition: width 0.3s ease-in-out, height 0.3s ease-in-out;
}
</style>
