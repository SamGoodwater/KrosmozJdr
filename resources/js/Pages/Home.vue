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
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { useValidation } from '@/Composables/form/useValidation';

// Molecules
import Hero from "@/Pages/Molecules/navigation/Hero.vue";
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

// Validation
const { validateField, setFieldError, setFieldSuccess } = useValidation();

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

// Tests de validation
function testNameValidation() {
    if (testForm.value.name.length < 3) {
        setFieldError('name', 'Le nom doit contenir au moins 3 caractères');
    } else {
        setFieldSuccess('name', 'Nom valide !');
    }
}

function testEmailValidation() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(testForm.value.email)) {
        setFieldError('email', 'Email invalide');
    } else {
        setFieldSuccess('email', 'Email valide !');
    }
}

function testPasswordValidation() {
    if (testForm.value.password.length < 6) {
        setFieldError('password', 'Le mot de passe doit contenir au moins 6 caractères');
    } else if (!/\d/.test(testForm.value.password)) {
        setFieldError('password', 'Le mot de passe doit contenir au moins un chiffre');
    } else {
        setFieldSuccess('password', 'Mot de passe sécurisé !');
    }
}

function testRoleValidation() {
    if (!testForm.value.role) {
        setFieldError('role', 'Veuillez sélectionner un rôle');
    } else {
        setFieldSuccess('role', 'Rôle sélectionné !');
    }
}

function testDescriptionValidation() {
    if (testForm.value.description.length < 10) {
        setFieldError('description', 'La description doit contenir au moins 10 caractères');
    } else {
        setFieldSuccess('description', 'Description complète !');
    }
}

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
                                <div class="space-y-4">
                                    <!-- Nom avec validation -->
                                    <InputField
                                        label="Nom complet"
                                        v-model="testForm.name"
                                        placeholder="Votre nom"
                                        variant="glass"
                                        :validation="{ state: '', message: '' }"
                                        @blur="testNameValidation"
                                        helper="Minimum 3 caractères"
                                    />
                                    
                                    <!-- Email avec validation -->
                                    <InputField
                                        label="Email"
                                        type="email"
                                        v-model="testForm.email"
                                        placeholder="votre@email.com"
                                        variant="glass"
                                        :validation="{ state: '', message: '' }"
                                        @blur="testEmailValidation"
                                        helper="Format email valide requis"
                                    />
                                    
                                    <!-- Mot de passe avec toggle -->
                                    <InputField
                                        label="Mot de passe"
                                        type="password"
                                        v-model="testForm.password"
                                        placeholder="Votre mot de passe"
                                        variant="glass"
                                        :validation="{ state: '', message: '' }"
                                        @blur="testPasswordValidation"
                                        helper="Minimum 6 caractères avec chiffre"
                                        :actions="['password']"
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
                                        :validation="{ state: '', message: '' }"
                                        @change="testRoleValidation"
                                        helper="Sélectionnez votre rôle"
                                    />
                                    
                                    <!-- Textarea avec validation -->
                                    <TextareaField
                                        label="Description"
                                        v-model="testForm.description"
                                        placeholder="Décrivez-vous..."
                                        variant="glass"
                                        :validation="{ state: '', message: '' }"
                                        @blur="testDescriptionValidation"
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
    </div>

</template>
