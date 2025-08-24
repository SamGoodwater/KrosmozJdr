import { ref, computed, nextTick } from 'vue';

/**
 * Composable pour le positionnement intelligent d'éléments flottants
 * Réutilisable pour dropdowns, tooltips, popovers, etc.
 * 
 * @param {Object} options - Options de configuration
 * @param {string} options.placement - Placement initial ('top', 'bottom', 'left', 'right', etc.)
 * @param {number} options.offset - Distance entre trigger et contenu
 * @param {boolean} options.autoPlacement - Auto-ajustement selon l'espace disponible
 * @param {boolean} options.responsive - Adaptation mobile/desktop
 * @returns {Object} - Méthodes et propriétés réactives
 */
export function usePositioning(options = {}) {
  const {
    placement = 'bottom-end',
    offset = 8,
    autoPlacement = true,
    responsive = true
  } = options;

  const triggerRef = ref(null);
  const contentRef = ref(null);
  const currentPlacement = ref(placement);
  const positionStyle = ref({});

  // Calcul de la position optimale
  const calculatePosition = async () => {
    if (!triggerRef.value || !contentRef.value) return;

    await nextTick();

    const trigger = triggerRef.value.getBoundingClientRect();
    const content = contentRef.value.getBoundingClientRect();
    const viewport = {
      width: window.innerWidth,
      height: window.innerHeight
    };

    let finalPlacement = currentPlacement.value;
    let position = {};

    // Auto-placement intelligent
    if (autoPlacement) {
      finalPlacement = calculateOptimalPlacement(trigger, content, viewport, placement);
    }

    // Calcul de la position selon le placement
    position = calculatePositionByPlacement(trigger, content, finalPlacement, offset);

    // Ajustements responsive
    if (responsive) {
      position = adjustForMobile(position, trigger, content, viewport);
    }

    // Application des styles
    positionStyle.value = {
      position: 'fixed',
      zIndex: 1000,
      ...position
    };

    currentPlacement.value = finalPlacement;
  };

  // Calcul du placement optimal selon l'espace disponible
  const calculateOptimalPlacement = (trigger, content, viewport, preferredPlacement) => {
    const placements = [
      'bottom-end', 'bottom-start', 'bottom-center',
      'top-end', 'top-start', 'top-center',
      'left-end', 'left-start', 'left-center',
      'right-end', 'right-start', 'right-center'
    ];

    // Essayer le placement préféré en premier
    if (canFit(trigger, content, viewport, preferredPlacement)) {
      return preferredPlacement;
    }

    // Essayer les autres placements dans l'ordre de préférence
    for (const placement of placements) {
      if (canFit(trigger, content, viewport, placement)) {
        return placement;
      }
    }

    // Fallback : placement par défaut
    return 'bottom-end';
  };

  // Vérifier si un placement peut tenir dans le viewport
  const canFit = (trigger, content, viewport, placement) => {
    const [direction, alignment] = placement.split('-');
    
    let fits = true;
    
    switch (direction) {
      case 'bottom':
        fits = trigger.bottom + content.height + offset <= viewport.height;
        break;
      case 'top':
        fits = trigger.top - content.height - offset >= 0;
        break;
      case 'left':
        fits = trigger.left - content.width - offset >= 0;
        break;
      case 'right':
        fits = trigger.right + content.width + offset <= viewport.width;
        break;
    }

    return fits;
  };

  // Calcul de la position selon le placement
  const calculatePositionByPlacement = (trigger, content, placement, offset) => {
    const [direction, alignment] = placement.split('-');
    
    let left, top;

    switch (direction) {
      case 'bottom':
        top = trigger.bottom + offset;
        left = calculateHorizontalAlignment(trigger, content, alignment);
        break;
      case 'top':
        top = trigger.top - content.height - offset;
        left = calculateHorizontalAlignment(trigger, content, alignment);
        break;
      case 'left':
        left = trigger.left - content.width - offset;
        top = calculateVerticalAlignment(trigger, content, alignment);
        break;
      case 'right':
        left = trigger.right + offset;
        top = calculateVerticalAlignment(trigger, content, alignment);
        break;
    }

    return { left: `${left}px`, top: `${top}px` };
  };

  // Calcul de l'alignement horizontal
  const calculateHorizontalAlignment = (trigger, content, alignment) => {
    switch (alignment) {
      case 'start':
        return trigger.left;
      case 'center':
        return trigger.left + (trigger.width - content.width) / 2;
      case 'end':
        return trigger.right - content.width;
      default:
        return trigger.left;
    }
  };

  // Calcul de l'alignement vertical
  const calculateVerticalAlignment = (trigger, content, alignment) => {
    switch (alignment) {
      case 'start':
        return trigger.top;
      case 'center':
        return trigger.top + (trigger.height - content.height) / 2;
      case 'end':
        return trigger.bottom - content.height;
      default:
        return trigger.top;
    }
  };

  // Ajustements pour mobile
  const adjustForMobile = (position, trigger, content, viewport) => {
    const isMobile = viewport.width < 768;
    
    if (!isMobile) return position;

    // Sur mobile, centrer horizontalement et positionner en bas
    const mobilePosition = {
      ...position,
      left: '50%',
      transform: 'translateX(-50%)',
      maxWidth: `${viewport.width - 32}px`, // Marges de 16px de chaque côté
      width: 'auto'
    };

    // Si le contenu est trop haut, le positionner en haut
    if (parseInt(position.top) + content.height > viewport.height - 32) {
      mobilePosition.top = '16px';
      mobilePosition.bottom = 'auto';
    }

    return mobilePosition;
  };

  // Mise à jour de la position lors du resize
  const updatePosition = () => {
    calculatePosition();
  };

  // Classes CSS pour le placement
  const placementClasses = computed(() => {
    const [direction, alignment] = currentPlacement.value.split('-');
    return [
      `dropdown-${direction}`,
      `dropdown-${alignment}`
    ];
  });

  return {
    // Refs
    triggerRef,
    contentRef,
    
    // Propriétés réactives
    currentPlacement,
    positionStyle,
    placementClasses,
    
    // Méthodes
    calculatePosition,
    updatePosition
  };
}
