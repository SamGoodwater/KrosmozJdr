<script setup>
/**
 * Home component (refonte Atomic Design)
 * Page d'accueil KrosmozJDR : intro, démo composants, présentation projet.
 *
 * - Utilise la molecule Hero pour l'intro
 * - Container pour le contenu principal
 * - Démo boutons/icônes inline (à supprimer plus tard)
 * - Présentation projet en prose
 * - Section de test des notifications
 *
 * @author
 */
import { ref, onMounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from '@/Composables/store/useNotificationStore';


// Molecules
import Hero from "@/Pages/Molecules/navigation/Hero.vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
// Atoms
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
// Molecules d'input
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import TextareaField from "@/Pages/Molecules/data-input/TextareaField.vue";
import FileField from "@/Pages/Molecules/data-input/FileField.vue";
import ColorField from "@/Pages/Molecules/data-input/ColorField.vue";
import DateField from "@/Pages/Molecules/data-input/DateField.vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";

// Données partagées
const page = usePage();
const { setPageTitle } = usePageTitle();

// Notifications
const notificationStore = useNotificationStore();
const { 
    success, 
    error, 
    info, 
    warning, 
    primary, 
    secondary,
    addNotification 
} = notificationStore;



// Données réactives pour les tests d'inputs
const testForm = ref({
    name: '',
    email: '',
    password: '',
    role: '',
    description: '',
    avatar: null,
    favoriteColor: '#3b82f6',
    birthDate: '',
    search: '',
    number: 0,
    range: 50,
    rating: 3,
    checkbox: false,
    radio: 'option1',
    toggle: false
});

// Options pour les selects
const roleOptions = [
    { label: 'Sélectionner un rôle', value: '' },
    { label: 'Administrateur', value: 'admin' },
    { label: 'Modérateur', value: 'moderator' },
    { label: 'Utilisateur', value: 'user' },
    { label: 'Invité', value: 'guest' }
];

const radioOptions = [
    { label: 'Option 1', value: 'option1' },
    { label: 'Option 2', value: 'option2' },
    { label: 'Option 3', value: 'option3' }
];

// Tests de validation avec le nouveau système granulaire
const nameValidationRules = computed(() => [
    {
        rule: (value) => value && value.length >= 3,
        message: 'Le nom doit contenir au moins 3 caractères',
        state: 'error',
        trigger: 'blur'
    },
    {
        rule: (value) => value && value.length >= 5,
        message: 'Nom valide !',
        state: 'success',
        trigger: 'change'
    }
]);

const emailValidationRules = computed(() => [
    {
        rule: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: 'Email invalide',
        state: 'error',
        trigger: 'blur'
    },
    {
        rule: (value) => value && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        message: 'Email valide !',
        state: 'success',
        trigger: 'change'
    }
]);

const passwordValidationRules = computed(() => [
    {
        rule: (value) => value && value.length >= 6,
        message: 'Le mot de passe doit contenir au moins 6 caractères',
        state: 'error',
        trigger: 'blur'
    },
    {
        rule: (value) => value && /\d/.test(value),
        message: 'Le mot de passe doit contenir au moins un chiffre',
        state: 'warning',
        trigger: 'change'
    },
    {
        rule: (value) => value && value.length >= 6 && /\d/.test(value),
        message: 'Mot de passe sécurisé !',
        state: 'success',
        trigger: 'change'
    }
]);

const roleValidationRules = computed(() => [
    {
        rule: (value) => value && value !== '',
        message: 'Veuillez sélectionner un rôle',
        state: 'error',
        trigger: 'blur'
    },
    {
        rule: (value) => value && value !== '',
        message: 'Rôle sélectionné !',
        state: 'success',
        trigger: 'change'
    }
]);

const descriptionValidationRules = computed(() => [
    {
        rule: (value) => value && value.length >= 10,
        message: 'La description doit contenir au moins 10 caractères',
        state: 'error',
        trigger: 'blur'
    },
    {
        rule: (value) => value && value.length >= 10,
        message: 'Description complète !',
        state: 'success',
        trigger: 'change'
    }
]);

// Tests d'actions contextuelles
function testInputActions() {
    success('Test des actions contextuelles activé !');
}

// Tests de styles
function testInputStyles() {
    info('Test des différents styles d\'inputs');
}

onMounted(() => {
    setPageTitle("Accueil");
});

// Test des notifications
function testSuccess() {
    success("Opération réussie ! Votre compte a été créé avec succès.", {
        duration: 0
    });
}

function testError() {
    error("Erreur lors de la sauvegarde. Veuillez réessayer.", {
        duration: 10000
    });
}

function testInfo() {
    info("Nouvelle mise à jour disponible. Redémarrez l'application pour l'installer.", {
        duration: 15000
    });
}

function testWarning() {
    warning("Attention : Votre session expire dans 5 minutes.", {
        duration: 12000
    });
}

function testPrimary() {
    primary("Nouveau message reçu de l'équipe.", {
        duration: 6000
    });
}

function testSecondary() {
    secondary("Synchronisation terminée.", {
        duration: 5000
    });
}

function testWithActions() {
    addNotification({
        message: "Fichier téléchargé avec succès. Que souhaitez-vous faire ?",
        type: "success",
        duration: 20000,
        actions: [
            {
                label: "Ouvrir",
                onClick: () => {
                    info("Ouverture du fichier...");
                },
                color: "success",
                size: "xs"
            },
            {
                label: "Annuler",
                onClick: () => {
                    warning("Action annulée");
                },
                color: "neutral",
                size: "xs"
            }
        ]
    });
}

function testCustomIcon() {
    addNotification({
        message: "Nouveau commentaire sur votre post",
        type: "info",
        icon: "fa-comment",
        duration: 8000,
        onClick: () => {
            console.log("Voir le commentaire");
            info("Redirection vers le commentaire...");
        }
    });
}

function testAllNotifications() {
    console.log('Test all notifications');
    try {
        success("Succès !");
        error("Erreur !");
        info("Information !");
        warning("Attention !");
        primary("Primaire !");
        secondary("Secondaire !");
    } catch (error) {
        console.error('Error in testAllNotifications:', error);
    }
}

// Démo boutons
const demoButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "glass",
        label: "Primary",
        tooltip: "Bouton principal",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "glass",
        label: "Secondary",
        tooltip: "Bouton secondaire",
    },
    {
        color: "success",
        size: "sm",
        variant: "glass",
        label: "Success",
        tooltip: "Action réussie",
    },
    {
        color: "error",
        size: "sm",
        variant: "glass",
        label: "Error",
        tooltip: "Action d'erreur",
    },  
    {
        color: "info",
        size: "sm",
        variant: "glass",
        label: "Info",
        tooltip: "Action d'information",
    },
    {
        color: "warning",
        size: "sm",
        variant: "glass",
        label: "Warning",
        tooltip: "Action d'avertissement",
    },
    {
        color: "accent",
        size: "sm",
        variant: "glass",
        label: "Accent",
        tooltip: "Action accentuée",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "glass",
        label: "Neutral",
        tooltip: "Action neutre",
    },
];
const outlineButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "outline",
        label: "Primaire",
        tooltip: "Bouton principal en contour",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "outline",
        label: "Secondaire",
        tooltip: "Bouton secondaire en contour",
    },
    {
        color: "success",
        size: "sm",
        variant: "outline",
        label: "Succès",
        tooltip: "Bouton de validation en contour",
    },
    {
        color: "error",
        size: "sm",
        variant: "outline",
        label: "Erreur",
        tooltip: "Bouton d'annulation en contour",
    },
    {
        color: "info",
        size: "sm",
        variant: "outline",
        label: "Info",
        tooltip: "Bouton d'information en contour",
    },
    {
        color: "warning",
        size: "sm",
        variant: "outline",
        label: "Warning",
        tooltip: "Bouton d'avertissement en contour",
    },
    {
        color: "accent",
        size: "sm",
        variant: "outline",
        label: "Accent",
        tooltip: "Bouton accentué en contour",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "outline",
        label: "Neutral",
        tooltip: "Bouton neutre en contour",
    },
];
const linkButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "link",
        label: "Primaire",
        tooltip: "Lien principal",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "link",
        label: "Secondaire",
        tooltip: "Lien secondaire",
    },
    {
        color: "success",
        size: "sm",
        variant: "link",
        label: "Succès",
        tooltip: "Lien de validation",
    },
    {
        color: "error",
        size: "sm",
        variant: "link",
        label: "Erreur",
        tooltip: "Lien d'annulation",
    },
    {
        color: "info",
        size: "sm",
        variant: "link",
        label: "Info",
        tooltip: "Lien d'information",
    },
    {
        color: "warning",
        size: "sm",
        variant: "link",
        label: "Warning",
        tooltip: "Lien d'avertissement",
    },
    {
        color: "accent",
        size: "sm",
        variant: "link",
        label: "Accent",
        tooltip: "Lien accentué",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "link",
        label: "Neutral",
        tooltip: "Lien neutre",
    },
];
// Démo icônes
const demoIcons = [
    {
        size: "xs",
        source: "icons/characteristics/pa.png",
        tooltip: "Icône extra-small",
    },
    {
        size: "sm",
        source: "icons/characteristics/pm.png",
        tooltip: "Icône small",
    },
    {
        size: "md",
        source: "icons/characteristics/po.png",
        tooltip: "Icône medium",
    },
    {
        size: "lg",
        source: "icons/characteristics/tacle.png",
        tooltip: "Icône large",
    },
    {
        size: "xl",
        source: "icons/characteristics/res_terre.png",
        tooltip: "Icône extra-large",
    },
];

// États pour les modals de démonstration
const modalStates = ref({
    // Tailles
    sizeXs: false,
    sizeSm: false,
    sizeMd: false,
    sizeLg: false,
    sizeXl: false,
    sizeFull: false,
    sizeAuto: false,
    
    // Variants
    variantGlass: false,
    variantDash: false,
    variantOutline: false,
    variantSoft: false,
    variantGhost: false,
    
    // Couleurs
    colorPrimary: false,
    colorSecondary: false,
    colorAccent: false,
    colorInfo: false,
    colorSuccess: false,
    colorWarning: false,
    colorError: false,
    colorNeutral: false,
    
    // Animations
    animationNone: false,
    animationFade: false,
    animationZoom: false,
    animationSlide: false,
    
    // Placements
    placementTopStart: false,
    placementTopCenter: false,
    placementTopEnd: false,
    placementMiddleStart: false,
    placementMiddleCenter: false,
    placementMiddleEnd: false,
    placementBottomStart: false,
    placementBottomCenter: false,
    placementBottomEnd: false,
    
    // Options
    noOverlay: false,
    noCloseOnOutsideClick: false,
    closeOnEsc: false,
    noCloseOnButton: false,
    resizable: false,
    notDraggable: false,
    
    // Combinaisons
    combo1: false,
    combo2: false,
    combo3: false,
});
</script>

<template>
    <div>
        <!-- Hero d'intro -->
        <Hero minHeight="min-h-[200px] border-glass-primary-xs">
            <template #content>
                <div class="text-center space-y-4">
                    <h1 class="text-title">
                        Bienvenue sur KrosmozJDR
                    </h1>
                    <p class="text-lg text-subtitle">
                        L'aventure épique dans l'univers du Monde des Douze commence
                        ici !
                    </p>
                    <Btn color="primary" size="md" variant="glass">Rejoindre l'aventure</Btn>
                </div>
            </template>
        </Hero>

        <Container class="space-y-8 mt-8">
            <!-- Section test des notifications -->
            <section class="space-y-6 mb-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Test du Système de Notifications</h2>
                    <p class="text-base-content/70 mb-6">Cliquez sur les boutons pour tester les différents types de notifications</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto">
                        <!-- Tests par type -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Types de Notifications</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="testSuccess" color="success" size="sm">
                                        Test Success
                                    </Btn>
                                    <Btn @click="testError" color="error" size="sm">
                                        Test Error
                                    </Btn>
                                    <Btn @click="testInfo" color="info" size="sm">
                                        Test Info
                                    </Btn>
                                    <Btn @click="testWarning" color="warning" size="sm">
                                        Test Warning
                                    </Btn>
                                    <Btn @click="testPrimary" color="primary" size="sm">
                                        Test Primary
                                    </Btn>
                                    <Btn @click="testSecondary" color="secondary" size="sm">
                                        Test Secondary
                                    </Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Tests avancés -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Tests Avancés</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="testWithActions" color="accent" size="sm">
                                        Avec Actions
                                    </Btn>
                                    <Btn @click="testCustomIcon" color="neutral" size="sm">
                                        Icône Personnalisée
                                    </Btn>
                                    <Btn @click="testAllNotifications" color="primary" size="sm">
                                        Toutes en même temps
                                    </Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Fonctionnalités</h3>
                                <ul class="text-sm space-y-1 text-left">
                                    <li>✅ 4 placements (top-left, top-right, bottom-left, bottom-right)</li>
                                    <li>✅ Limite de 20 notifications par placement</li>
                                    <li>✅ Cycle full (40%) → contracted (60%)</li>
                                    <li>✅ Hover pour étendre les notifications contractées</li>
                                    <li>✅ Barre de progression en bas</li>
                                    <li>✅ Animations d'entrée/sortie</li>
                                    <li>✅ Scroll automatique</li>
                                    <li>✅ Actions personnalisées</li>
                                    <li>✅ Icônes par défaut selon le type</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section test du système d'inputs -->
            <section class="space-y-6 mb-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Test du Système d'Inputs Factorisé</h2>
                    <p class="text-base-content/70 mb-6">Testez toutes les fonctionnalités du nouveau système d'inputs : validation, notifications, actions contextuelles, styles</p>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
                        <!-- Tests des inputs de base -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Inputs de Base</h3>
                                <form @submit.prevent>
                                    <div class="space-y-4">
                                        <!-- Nom avec validation -->
                                        <InputField
                                            label="Nom complet"
                                            v-model="testForm.name"
                                            placeholder="Votre nom"
                                            variant="glass"
                                            :validation-rules="nameValidationRules"
                                            helper="Minimum 3 caractères"
                                        />
                                        
                                        <!-- Email avec validation -->
                                        <InputField
                                            label="Email"
                                            type="email"
                                            v-model="testForm.email"
                                            placeholder="votre@email.com"
                                            variant="glass"
                                            :validation-rules="emailValidationRules"
                                            helper="Format email valide requis"
                                        />
                                        
                                        <!-- Mot de passe avec toggle -->
                                        <InputField
                                            label="Mot de passe"
                                            type="password"
                                            v-model="testForm.password"
                                            placeholder="Votre mot de passe"
                                            variant="glass"
                                            :validation-rules="passwordValidationRules"
                                            helper="Minimum 6 caractères avec chiffre"
                                            :actions="['password']"
                                            autocomplete="new-password"
                                        />
                                        
                                        <!-- Recherche avec actions -->
                                        <InputField
                                            label="Recherche"
                                            type="search"
                                            v-model="testForm.search"
                                            placeholder="Rechercher..."
                                            variant="glass"
                                            :actions="['clear', 'copy']"
                                            helper="Utilisez les actions contextuelles"
                                        />
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tests des inputs avancés -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Inputs Avancés</h3>
                                <div class="space-y-4">
                                    <!-- Select avec validation -->
                                    <SelectField
                                        label="Rôle utilisateur"
                                        v-model="testForm.role"
                                        :options="roleOptions"
                                        variant="glass"
                                        :validation-rules="roleValidationRules"
                                        helper="Sélectionnez votre rôle"
                                    />
                                    
                                    <!-- Textarea avec validation -->
                                    <TextareaField
                                        label="Description"
                                        v-model="testForm.description"
                                        placeholder="Décrivez-vous..."
                                        variant="glass"
                                        :validation-rules="descriptionValidationRules"
                                        helper="Minimum 10 caractères"
                                        rows="3"
                                    />
                                    
                                    <!-- File upload -->
                                    <FileField
                                        label="Avatar"
                                        v-model="testForm.avatar"
                                        variant="glass"
                                        helper="Formats acceptés : JPG, PNG, GIF"
                                        accept="image/*"
                                    />
                                    
                                    <!-- Color picker -->
                                    <ColorField
                                        label="Couleur préférée"
                                        v-model="testForm.favoriteColor"
                                        variant="glass"
                                        helper="Choisissez votre couleur"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Tests du système d'avatar -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Système d'Avatar</h3>
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-md font-semibold mb-3">Avatars avec images</h4>
                                        <div class="flex gap-4 items-center">
                                            <Avatar src="https://picsum.photos/200/200?random=1" alt="Avatar test" size="lg" />
                                            <Avatar src="https://picsum.photos/200/200?random=2" alt="Avatar test" size="md" />
                                            <Avatar src="https://picsum.photos/200/200?random=3" alt="Avatar test" size="sm" />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold mb-3">Avatars avec initiales (1 mot)</h4>
                                        <div class="flex gap-4 items-center">
                                            <Avatar label="John" alt="John" size="lg" />
                                            <Avatar label="Marie" alt="Marie" size="md" />
                                            <Avatar label="Pierre" alt="Pierre" size="sm" />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold mb-3">Avatars avec initiales (2+ mots)</h4>
                                        <div class="flex gap-4 items-center">
                                            <Avatar label="John Doe" alt="John Doe" size="lg" />
                                            <Avatar label="Marie Dupont" alt="Marie Dupont" size="md" />
                                            <Avatar label="Pierre Martin" alt="Pierre Martin" size="sm" />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold mb-3">Avatars avec image par défaut</h4>
                                        <div class="flex gap-4 items-center">
                                            <Avatar 
                                                defaultAvatar="https://picsum.photos/200/200?random=4" 
                                                alt="Utilisateur" 
                                                size="lg" 
                                            />
                                            <Avatar 
                                                defaultAvatar="https://picsum.photos/200/200?random=5" 
                                                alt="Test" 
                                                size="md" 
                                            />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-md font-semibold mb-3">Avatars avec fallback avatar par défaut</h4>
                                        <div class="flex gap-4 items-center">
                                            <Avatar alt="Sans label ni image" size="lg" />
                                            <Avatar alt="Sans label ni image" size="md" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tests des inputs spécialisés -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Inputs Spécialisés</h3>
                                <div class="space-y-4">
                                    <!-- Date picker -->
                                    <DateField
                                        label="Date de naissance"
                                        v-model="testForm.birthDate"
                                        variant="glass"
                                        helper="Sélectionnez votre date de naissance"
                                    />
                                    
                                    <!-- Number input -->
                                    <InputField
                                        label="Âge"
                                        type="number"
                                        v-model="testForm.number"
                                        placeholder="25"
                                        variant="glass"
                                        min="0"
                                        max="120"
                                        helper="Votre âge en années"
                                    />
                                    
                                    <!-- Range slider -->
                                    <InputField
                                        label="Niveau d'expérience"
                                        type="range"
                                        v-model="testForm.range"
                                        variant="glass"
                                        min="0"
                                        max="100"
                                        helper="Glissez pour ajuster le niveau"
                                    />
                                    
                                    <!-- Rating -->
                                    <InputField
                                        label="Note globale"
                                        type="rating"
                                        v-model="testForm.rating"
                                        variant="glass"
                                        max="5"
                                        helper="Cliquez pour noter de 1 à 5"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Tests des inputs de sélection -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Inputs de Sélection</h3>
                                <div class="space-y-4">
                                    <!-- Checkbox -->
                                    <InputField
                                        label="J'accepte les conditions"
                                        type="checkbox"
                                        v-model="testForm.checkbox"
                                        variant="glass"
                                        helper="Cochez pour accepter"
                                    />
                                    
                                    <!-- Radio buttons -->
                                    <div class="space-y-2">
                                        <label class="label">
                                            <span class="label-text">Option préférée</span>
                                        </label>
                                        <div class="flex gap-4">
                                            <InputField
                                                v-for="option in radioOptions"
                                                :key="option.value"
                                                :label="option.label"
                                                type="radio"
                                                :value="option.value"
                                                v-model="testForm.radio"
                                                variant="glass"
                                                name="radio-group"
                                            />
                                        </div>
                                        <div class="text-xs text-base-content/60">Sélectionnez une option</div>
                                    </div>
                                    
                                    <!-- Toggle switch -->
                                    <InputField
                                        label="Notifications activées"
                                        type="toggle"
                                        v-model="testForm.toggle"
                                        variant="glass"
                                        helper="Activez/désactivez les notifications"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Tests des styles et variants -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Tests de Styles</h3>
                                <div class="space-y-4">
                                    <Btn @click="testInputStyles" color="primary" size="sm" class="w-full">
                                        Tester les Styles
                                    </Btn>
                                    
                                    <div class="grid grid-cols-1 gap-3">
                                        <InputField
                                            label="Style Glass"
                                            variant="glass"
                                            placeholder="Glass style"
                                            helper="Variant glass par défaut"
                                        />
                                        <InputField
                                            label="Style Outline"
                                            variant="outline"
                                            placeholder="Outline style"
                                            helper="Variant outline"
                                        />
                                        <InputField
                                            label="Style Ghost"
                                            variant="ghost"
                                            placeholder="Ghost style"
                                            helper="Variant ghost"
                                        />
                                        <InputField
                                            label="Style Soft"
                                            variant="soft"
                                            placeholder="Soft style"
                                            helper="Variant soft"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tests des actions contextuelles -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Actions Contextuelles</h3>
                                <div class="space-y-4">
                                    <Btn @click="testInputActions" color="accent" size="sm" class="w-full">
                                        Activer les Actions
                                    </Btn>
                                    
                                    <InputField
                                        label="Champ avec Reset"
                                        placeholder="Tapez quelque chose..."
                                        variant="glass"
                                        :actions="['reset']"
                                        helper="Action reset disponible"
                                    />
                                    
                                    <InputField
                                        label="Champ avec Copy"
                                        placeholder="Contenu à copier"
                                        variant="glass"
                                        :actions="['copy']"
                                        helper="Action copy disponible"
                                    />
                                    
                                    <InputField
                                        label="Champ avec Clear"
                                        placeholder="Contenu à effacer"
                                        variant="glass"
                                        :actions="['clear']"
                                        helper="Action clear disponible"
                                    />
                                    
                                    <InputField
                                        label="Champ avec Back"
                                        placeholder="Modifiez puis revenez en arrière"
                                        variant="glass"
                                        :actions="['back']"
                                        helper="Action back disponible"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section test des modals -->
            <section class="space-y-6 mb-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Test du Système de Modals</h2>
                    <p class="text-base-content/70 mb-6">Testez toutes les fonctionnalités et variantes des modals : tailles, variants, couleurs, animations, placements, options</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                        <!-- Tailles -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Tailles</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.sizeXs = true" color="primary" size="sm">XS</Btn>
                                    <Btn @click="modalStates.sizeSm = true" color="primary" size="sm">SM</Btn>
                                    <Btn @click="modalStates.sizeMd = true" color="primary" size="sm">MD</Btn>
                                    <Btn @click="modalStates.sizeLg = true" color="primary" size="sm">LG</Btn>
                                    <Btn @click="modalStates.sizeXl = true" color="primary" size="sm">XL</Btn>
                                    <Btn @click="modalStates.sizeFull = true" color="primary" size="sm">Full</Btn>
                                    <Btn @click="modalStates.sizeAuto = true" color="primary" size="sm">Auto</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Variants -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Variants</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.variantGlass = true" color="primary" size="sm">Glass</Btn>
                                    <Btn @click="modalStates.variantDash = true" color="primary" size="sm">Dash</Btn>
                                    <Btn @click="modalStates.variantOutline = true" color="primary" size="sm">Outline</Btn>
                                    <Btn @click="modalStates.variantSoft = true" color="primary" size="sm">Soft</Btn>
                                    <Btn @click="modalStates.variantGhost = true" color="primary" size="sm">Ghost</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Couleurs -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Couleurs</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.colorPrimary = true" color="primary" size="sm">Primary</Btn>
                                    <Btn @click="modalStates.colorSecondary = true" color="secondary" size="sm">Secondary</Btn>
                                    <Btn @click="modalStates.colorAccent = true" color="accent" size="sm">Accent</Btn>
                                    <Btn @click="modalStates.colorInfo = true" color="info" size="sm">Info</Btn>
                                    <Btn @click="modalStates.colorSuccess = true" color="success" size="sm">Success</Btn>
                                    <Btn @click="modalStates.colorWarning = true" color="warning" size="sm">Warning</Btn>
                                    <Btn @click="modalStates.colorError = true" color="error" size="sm">Error</Btn>
                                    <Btn @click="modalStates.colorNeutral = true" color="neutral" size="sm">Neutral</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Animations -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Animations</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.animationNone = true" color="primary" size="sm">None</Btn>
                                    <Btn @click="modalStates.animationFade = true" color="primary" size="sm">Fade</Btn>
                                    <Btn @click="modalStates.animationZoom = true" color="primary" size="sm">Zoom</Btn>
                                    <Btn @click="modalStates.animationSlide = true" color="primary" size="sm">Slide</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Placements -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Placements</h3>
                                <div class="grid grid-cols-3 gap-2">
                                    <Btn @click="modalStates.placementTopStart = true" color="primary" size="xs">Top-Start</Btn>
                                    <Btn @click="modalStates.placementTopCenter = true" color="primary" size="xs">Top-Center</Btn>
                                    <Btn @click="modalStates.placementTopEnd = true" color="primary" size="xs">Top-End</Btn>
                                    <Btn @click="modalStates.placementMiddleStart = true" color="primary" size="xs">Mid-Start</Btn>
                                    <Btn @click="modalStates.placementMiddleCenter = true" color="primary" size="xs">Mid-Center</Btn>
                                    <Btn @click="modalStates.placementMiddleEnd = true" color="primary" size="xs">Mid-End</Btn>
                                    <Btn @click="modalStates.placementBottomStart = true" color="primary" size="xs">Bot-Start</Btn>
                                    <Btn @click="modalStates.placementBottomCenter = true" color="primary" size="xs">Bot-Center</Btn>
                                    <Btn @click="modalStates.placementBottomEnd = true" color="primary" size="xs">Bot-End</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Options</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.noOverlay = true" color="primary" size="sm">Sans Overlay</Btn>
                                    <Btn @click="modalStates.noCloseOnOutsideClick = true" color="primary" size="sm">Pas de fermeture extérieure</Btn>
                                    <Btn @click="modalStates.closeOnEsc = true" color="primary" size="sm">Fermeture ESC</Btn>
                                    <Btn @click="modalStates.noCloseOnButton = true" color="primary" size="sm">Sans bouton fermer</Btn>
                                    <Btn @click="modalStates.resizable = true" color="primary" size="sm">Redimensionnable</Btn>
                                    <Btn @click="modalStates.notDraggable = true" color="primary" size="sm">Non déplaçable</Btn>
                                </div>
                            </div>
                        </div>

                        <!-- Combinaisons -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Combinaisons</h3>
                                <div class="flex flex-col gap-2">
                                    <Btn @click="modalStates.combo1 = true" color="accent" size="sm">Combo 1</Btn>
                                    <Btn @click="modalStates.combo2 = true" color="accent" size="sm">Combo 2</Btn>
                                    <Btn @click="modalStates.combo3 = true" color="accent" size="sm">Combo 3</Btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section démo boutons (inline, à supprimer plus tard) -->
            <section class="space-y-6">
                <div class="text-center space-y-6">
                    <!-- Boutons glass -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in demoButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Boutons outline -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in outlineButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Boutons link -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in linkButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Icônes de démonstration -->
                    <div class="flex gap-4 justify-center items-center">
                        <Tooltip v-for="icon in demoIcons" :key="icon.source" :content="icon.tooltip">
                            <Icon :size="icon.size" :source="icon.source" alt="Icône de démo" />
                        </Tooltip>
                    </div>
                </div>
            </section>

            <!-- Présentation du projet -->
            <section class="prose text-content max-sm:prose-sm lg:prose-lg">
                <h3 class="text-title">
                    Bienvenue dans <strong>KrosmozJDR</strong>, l'aventure épique
                    dans l'univers du monde des Douze !
                </h3>
                <p>
                    Plongez dans le monde des Douze, un univers riche et vibrant
                    issu de l'imaginaire de <em>Dofus</em>, où l'aventure, la
                    stratégie et la magie s'entrelacent pour créer une expérience
                    unique de jeu de rôle. Ici, chaque partie est une porte ouverte
                    vers des terres fascinantes, des créatures captivantes et des
                    combats épiques.
                </p>
                <h4 class="text-subtitle">Explorez des lieux mythiques</h4>
                <p>
                    De la cité lumineuse de Bonta aux mystères d'Astrub, en passant
                    par les plaines sauvages des Craqueleurs et les secrets
                    d'Amakna, découvrez un monde vaste et varié où chaque région est
                    le théâtre d'histoires légendaires. Préparez-vous à croiser la
                    route des Wabbits malicieux, des Bouftous sauvages, des Chafeurs
                    redoutables, et à affronter les puissants maîtres des donjons
                    comme Kardorim, Groloum ou encore le Comte Harebourg.
                </p>
                <h4 class="text-subtitle">Incarnez une classe iconique</h4>
                <p>
                    Choisissez parmi une grande variété de classes emblématiques :
                    serez-vous un Crâ précis et implacable, un Iop téméraire et
                    valeureux, un Osamodas lié aux créatures, ou encore un Eliotrope
                    maître des portails ? Que vous soyez un soigneur Eniripsa, un
                    stratège Roublard ou un manipulateur du temps Xélor, chaque
                    classe offre des mécaniques uniques et des sorts adaptés à
                    l'univers du jeu de rôle.
                </p>
                <h4 class="text-subtitle">Développez votre personnage</h4>
                <p>
                    Grâce à un système de compétences et d'aptitudes inspiré de
                    D&amp;D, personnalisez votre héros pour qu'il reflète votre
                    style de jeu. Enrichissez vos stratégies avec des
                    spécialisations uniques, inspirées des subtilités des races de
                    D&amp;D, et explorez des gameplays toujours plus variés et
                    profonds.
                </p>
                <h4 class="text-subtitle">Un gameplay enrichi et immersif</h4>
                <p>
                    Notre jeu de rôle fusionne les règles classiques du JdR avec
                    l'univers riche de Dofus, pour offrir une expérience immersive
                    et accessible à tous, débutants comme vétérans. Préparez-vous à
                    vivre des quêtes épiques, à forger des alliances mémorables, et
                    à écrire votre légende dans un monde en perpétuelle évolution.
                </p>
                <hr class="border-primary-700/50" />
                <h3 class="text-title">Êtes-vous prêt à rejoindre l'aventure ?</h3>
                <p>
                    Rassemblez vos compagnons, lancez vos dés et partez à la
                    découverte du monde des Douze. Votre destin est entre vos mains
                    !
                </p>
            </section>
        </Container>

        <!-- Modals de démonstration -->
        <!-- Tailles -->
        <Modal :open="modalStates.sizeXs" size="xs" @close="modalStates.sizeXs = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal XS</h3>
            </template>
            <p>Ceci est un modal de taille XS (extra-small).</p>
            <template #actions>
                <Btn @click="modalStates.sizeXs = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeSm" size="sm" @close="modalStates.sizeSm = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal SM</h3>
            </template>
            <p>Ceci est un modal de taille SM (small).</p>
            <template #actions>
                <Btn @click="modalStates.sizeSm = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeMd" size="md" @close="modalStates.sizeMd = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal MD</h3>
            </template>
            <p>Ceci est un modal de taille MD (medium).</p>
            <template #actions>
                <Btn @click="modalStates.sizeMd = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeLg" size="lg" @close="modalStates.sizeLg = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal LG</h3>
            </template>
            <p>Ceci est un modal de taille LG (large).</p>
            <template #actions>
                <Btn @click="modalStates.sizeLg = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeXl" size="xl" @close="modalStates.sizeXl = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal XL</h3>
            </template>
            <p>Ceci est un modal de taille XL (extra-large).</p>
            <template #actions>
                <Btn @click="modalStates.sizeXl = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeFull" size="full" @close="modalStates.sizeFull = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal Full</h3>
            </template>
            <p>Ceci est un modal de taille Full (plein écran).</p>
            <template #actions>
                <Btn @click="modalStates.sizeFull = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.sizeAuto" size="auto" @close="modalStates.sizeAuto = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal Auto</h3>
            </template>
            <p>Ceci est un modal de taille Auto (s'adapte au contenu).</p>
            <template #actions>
                <Btn @click="modalStates.sizeAuto = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Variants -->
        <Modal :open="modalStates.variantGlass" variant="glass" @close="modalStates.variantGlass = false">
            <template #header>
                <h3 class="text-lg font-bold">Variant Glass</h3>
            </template>
            <p>Modal avec variant glass (effet glassmorphisme).</p>
            <template #actions>
                <Btn @click="modalStates.variantGlass = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.variantDash" variant="dash" @close="modalStates.variantDash = false">
            <template #header>
                <h3 class="text-lg font-bold">Variant Dash</h3>
            </template>
            <p>Modal avec variant dash (bordure pointillée).</p>
            <template #actions>
                <Btn @click="modalStates.variantDash = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.variantOutline" variant="outline" @close="modalStates.variantOutline = false">
            <template #header>
                <h3 class="text-lg font-bold">Variant Outline</h3>
            </template>
            <p>Modal avec variant outline (bordure visible).</p>
            <template #actions>
                <Btn @click="modalStates.variantOutline = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.variantSoft" variant="soft" @close="modalStates.variantSoft = false">
            <template #header>
                <h3 class="text-lg font-bold">Variant Soft</h3>
            </template>
            <p>Modal avec variant soft (fond doux).</p>
            <template #actions>
                <Btn @click="modalStates.variantSoft = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.variantGhost" variant="ghost" @close="modalStates.variantGhost = false">
            <template #header>
                <h3 class="text-lg font-bold">Variant Ghost</h3>
            </template>
            <p>Modal avec variant ghost (fond transparent).</p>
            <template #actions>
                <Btn @click="modalStates.variantGhost = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Couleurs -->
        <Modal :open="modalStates.colorPrimary" color="primary" @close="modalStates.colorPrimary = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Primary</h3>
            </template>
            <p>Modal avec couleur primary.</p>
            <template #actions>
                <Btn @click="modalStates.colorPrimary = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorSecondary" color="secondary" @close="modalStates.colorSecondary = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Secondary</h3>
            </template>
            <p>Modal avec couleur secondary.</p>
            <template #actions>
                <Btn @click="modalStates.colorSecondary = false" color="secondary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorAccent" color="accent" @close="modalStates.colorAccent = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Accent</h3>
            </template>
            <p>Modal avec couleur accent.</p>
            <template #actions>
                <Btn @click="modalStates.colorAccent = false" color="accent" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorInfo" color="info" @close="modalStates.colorInfo = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Info</h3>
            </template>
            <p>Modal avec couleur info.</p>
            <template #actions>
                <Btn @click="modalStates.colorInfo = false" color="info" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorSuccess" color="success" @close="modalStates.colorSuccess = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Success</h3>
            </template>
            <p>Modal avec couleur success.</p>
            <template #actions>
                <Btn @click="modalStates.colorSuccess = false" color="success" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorWarning" color="warning" @close="modalStates.colorWarning = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Warning</h3>
            </template>
            <p>Modal avec couleur warning.</p>
            <template #actions>
                <Btn @click="modalStates.colorWarning = false" color="warning" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorError" color="error" @close="modalStates.colorError = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Error</h3>
            </template>
            <p>Modal avec couleur error.</p>
            <template #actions>
                <Btn @click="modalStates.colorError = false" color="error" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.colorNeutral" color="neutral" @close="modalStates.colorNeutral = false">
            <template #header>
                <h3 class="text-lg font-bold">Couleur Neutral</h3>
            </template>
            <p>Modal avec couleur neutral.</p>
            <template #actions>
                <Btn @click="modalStates.colorNeutral = false" color="neutral" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Animations -->
        <Modal :open="modalStates.animationNone" animation="none" @close="modalStates.animationNone = false">
            <template #header>
                <h3 class="text-lg font-bold">Animation None</h3>
            </template>
            <p>Modal sans animation.</p>
            <template #actions>
                <Btn @click="modalStates.animationNone = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.animationFade" animation="fade" @close="modalStates.animationFade = false">
            <template #header>
                <h3 class="text-lg font-bold">Animation Fade</h3>
            </template>
            <p>Modal avec animation fade (par défaut).</p>
            <template #actions>
                <Btn @click="modalStates.animationFade = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.animationZoom" animation="zoom" @close="modalStates.animationZoom = false">
            <template #header>
                <h3 class="text-lg font-bold">Animation Zoom</h3>
            </template>
            <p>Modal avec animation zoom.</p>
            <template #actions>
                <Btn @click="modalStates.animationZoom = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.animationSlide" animation="slide" @close="modalStates.animationSlide = false">
            <template #header>
                <h3 class="text-lg font-bold">Animation Slide</h3>
            </template>
            <p>Modal avec animation slide.</p>
            <template #actions>
                <Btn @click="modalStates.animationSlide = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Placements -->
        <Modal :open="modalStates.placementTopStart" placement="top-start" @close="modalStates.placementTopStart = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Top-Start</h3>
            </template>
            <p>Modal positionné en haut à gauche.</p>
            <template #actions>
                <Btn @click="modalStates.placementTopStart = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementTopCenter" placement="top-center" @close="modalStates.placementTopCenter = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Top-Center</h3>
            </template>
            <p>Modal positionné en haut au centre.</p>
            <template #actions>
                <Btn @click="modalStates.placementTopCenter = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementTopEnd" placement="top-end" @close="modalStates.placementTopEnd = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Top-End</h3>
            </template>
            <p>Modal positionné en haut à droite.</p>
            <template #actions>
                <Btn @click="modalStates.placementTopEnd = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementMiddleStart" placement="middle-start" @close="modalStates.placementMiddleStart = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Middle-Start</h3>
            </template>
            <p>Modal positionné au milieu à gauche.</p>
            <template #actions>
                <Btn @click="modalStates.placementMiddleStart = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementMiddleCenter" placement="middle-center" @close="modalStates.placementMiddleCenter = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Middle-Center</h3>
            </template>
            <p>Modal positionné au centre (par défaut).</p>
            <template #actions>
                <Btn @click="modalStates.placementMiddleCenter = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementMiddleEnd" placement="middle-end" @close="modalStates.placementMiddleEnd = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Middle-End</h3>
            </template>
            <p>Modal positionné au milieu à droite.</p>
            <template #actions>
                <Btn @click="modalStates.placementMiddleEnd = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementBottomStart" placement="bottom-start" @close="modalStates.placementBottomStart = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Bottom-Start</h3>
            </template>
            <p>Modal positionné en bas à gauche.</p>
            <template #actions>
                <Btn @click="modalStates.placementBottomStart = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementBottomCenter" placement="bottom-center" @close="modalStates.placementBottomCenter = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Bottom-Center</h3>
            </template>
            <p>Modal positionné en bas au centre.</p>
            <template #actions>
                <Btn @click="modalStates.placementBottomCenter = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.placementBottomEnd" placement="bottom-end" @close="modalStates.placementBottomEnd = false">
            <template #header>
                <h3 class="text-lg font-bold">Placement Bottom-End</h3>
            </template>
            <p>Modal positionné en bas à droite.</p>
            <template #actions>
                <Btn @click="modalStates.placementBottomEnd = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Options -->
        <Modal :open="modalStates.noOverlay" :overlay="false" @close="modalStates.noOverlay = false">
            <template #header>
                <h3 class="text-lg font-bold">Sans Overlay</h3>
            </template>
            <p>Modal sans overlay (arrière-plan).</p>
            <template #actions>
                <Btn @click="modalStates.noOverlay = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.noCloseOnOutsideClick" :close-on-outside-click="false" @close="modalStates.noCloseOnOutsideClick = false">
            <template #header>
                <h3 class="text-lg font-bold">Pas de Fermeture Extérieure</h3>
            </template>
            <p>Ce modal ne se ferme pas en cliquant à l'extérieur. Utilisez le bouton de fermeture ou ESC.</p>
            <template #actions>
                <Btn @click="modalStates.noCloseOnOutsideClick = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.closeOnEsc" close-on-esc @close="modalStates.closeOnEsc = false">
            <template #header>
                <h3 class="text-lg font-bold">Fermeture ESC</h3>
            </template>
            <p>Ce modal se ferme avec la touche ESC. Appuyez sur ESC pour tester.</p>
            <template #actions>
                <Btn @click="modalStates.closeOnEsc = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.noCloseOnButton" :close-on-button="false" @close="modalStates.noCloseOnButton = false">
            <template #header>
                <h3 class="text-lg font-bold">Sans Bouton Fermer</h3>
            </template>
            <p>Ce modal n'a pas de bouton de fermeture en haut à droite. Utilisez le bouton ci-dessous ou cliquez à l'extérieur.</p>
            <template #actions>
                <Btn @click="modalStates.noCloseOnButton = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.resizable" :resizable="true" @close="modalStates.resizable = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal Redimensionnable</h3>
            </template>
            <p>Ce modal peut être redimensionné. Glissez depuis le coin inférieur droit pour redimensionner.</p>
            <template #actions>
                <Btn @click="modalStates.resizable = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal :open="modalStates.notDraggable" :draggable="false" @close="modalStates.notDraggable = false">
            <template #header>
                <h3 class="text-lg font-bold">Modal Non Déplaçable</h3>
            </template>
            <p>Ce modal ne peut pas être déplacé. Le curseur reste normal sur le header.</p>
            <template #actions>
                <Btn @click="modalStates.notDraggable = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <!-- Combinaisons -->
        <Modal 
            :open="modalStates.combo1" 
            size="lg" 
            color="primary" 
            variant="glass" 
            animation="zoom"
            placement="middle-center"
            close-on-esc
            @close="modalStates.combo1 = false"
        >
            <template #header>
                <h3 class="text-lg font-bold">Combo 1 : Glass + Primary + Zoom</h3>
            </template>
            <div class="space-y-4">
                <p>Modal avec plusieurs options combinées :</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Taille : LG</li>
                    <li>Couleur : Primary</li>
                    <li>Variant : Glass</li>
                    <li>Animation : Zoom</li>
                    <li>Placement : Middle-Center</li>
                    <li>Fermeture ESC activée</li>
                </ul>
            </div>
            <template #actions>
                <Btn @click="modalStates.combo1 = false" color="primary" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal 
            :open="modalStates.combo2" 
            size="xl" 
            color="success" 
            variant="outline" 
            animation="slide"
            placement="top-center"
            :resizable="true"
            @close="modalStates.combo2 = false"
        >
            <template #header>
                <h3 class="text-lg font-bold">Combo 2 : Outline + Success + Slide + Redimensionnable</h3>
            </template>
            <div class="space-y-4">
                <p>Modal avec plusieurs options combinées :</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Taille : XL</li>
                    <li>Couleur : Success</li>
                    <li>Variant : Outline</li>
                    <li>Animation : Slide</li>
                    <li>Placement : Top-Center</li>
                    <li>Redimensionnable activé</li>
                </ul>
            </div>
            <template #actions>
                <Btn @click="modalStates.combo2 = false" color="success" size="sm">Fermer</Btn>
            </template>
        </Modal>

        <Modal 
            :open="modalStates.combo3" 
            size="md" 
            color="error" 
            variant="dash" 
            animation="fade"
            placement="bottom-end"
            :close-on-outside-click="false"
            close-on-esc
            @close="modalStates.combo3 = false"
        >
            <template #header>
                <h3 class="text-lg font-bold">Combo 3 : Dash + Error + Fade + Options</h3>
            </template>
            <div class="space-y-4">
                <p>Modal avec plusieurs options combinées :</p>
                <ul class="list-disc list-inside space-y-2">
                    <li>Taille : MD</li>
                    <li>Couleur : Error</li>
                    <li>Variant : Dash</li>
                    <li>Animation : Fade</li>
                    <li>Placement : Bottom-End</li>
                    <li>Pas de fermeture extérieure</li>
                    <li>Fermeture ESC activée</li>
                </ul>
            </div>
            <template #actions>
                <Btn @click="modalStates.combo3 = false" color="error" size="sm">Fermer</Btn>
            </template>
        </Modal>
    </div>

</template>
