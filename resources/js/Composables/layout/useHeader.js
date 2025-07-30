import { ref, onMounted, onUnmounted } from "vue";

const isHeaderOpen = ref(true);

export function useHeader() {
    // Détection desktop (lg+)
    const isDesktop = () => {
        return window.matchMedia('(min-width: 1024px)').matches;
    };
    
    // Détection mobile (< 768px)
    const isMobile = () => {
        return window.innerWidth < 768;
    };
    
    // Charger l'état depuis localStorage
    const loadHeaderState = () => {
        try {
            const saved = localStorage.getItem('header-state');
            if (saved !== null) {
                const savedState = JSON.parse(saved);
                // Ne restaurer que si ce n'est pas en mobile
                if (!isMobile()) {
                    isHeaderOpen.value = savedState;
                }
            }
        } catch (error) {
            console.warn('Erreur lors du chargement de l\'état du header:', error);
        }
    };
    
    // Sauvegarder l'état dans localStorage
    const saveHeaderState = () => {
        try {
            localStorage.setItem('header-state', JSON.stringify(isHeaderOpen.value));
        } catch (error) {
            console.warn('Erreur lors de la sauvegarde de l\'état du header:', error);
        }
    };
    
    // Affichage auto selon breakpoint
    const updateHeaderVisibility = () => {
        if (isMobile()) {
            // En mobile : toujours masqué
            isHeaderOpen.value = false;
        } else {
            // En desktop/tablet : affiché par défaut, sauf si l'utilisateur l'a explicitement masqué
            const saved = localStorage.getItem('header-state');
            if (saved === null) {
                // Première visite : affiché par défaut
                isHeaderOpen.value = true;
            } else {
                // Restaurer l'état sauvegardé
                try {
                    isHeaderOpen.value = JSON.parse(saved);
                } catch (error) {
                    isHeaderOpen.value = true;
                }
            }
        }
    };
    
    // Toggle manuel avec persistance
    const toggleHeader = () => {
        // Ne permettre le toggle que si pas en mobile
        if (!isMobile()) {
            isHeaderOpen.value = !isHeaderOpen.value;
            saveHeaderState();
        }
    };
    
    // Raccourci clavier ALT+H
    const handleKeydown = (event) => {
        // Empêcher le déclenchement si on est dans un champ de saisie
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.contentEditable === 'true') {
            return;
        }
        
        if (event.altKey && event.key === "h") {
            event.preventDefault();
            toggleHeader();
        }
    };
    
    onMounted(() => {
        updateHeaderVisibility();
        window.addEventListener("resize", updateHeaderVisibility);
        window.addEventListener("keydown", handleKeydown);
    });
    
    onUnmounted(() => {
        window.removeEventListener("resize", updateHeaderVisibility);
        window.removeEventListener("keydown", handleKeydown);
    });
    
    return {
        isHeaderOpen,
        toggleHeader,
        isDesktop,
        isMobile,
        saveHeaderState,
    };
}
