---
Composant: " "
---
# Inputs
Les inputs fonctionnent en système de duo : un Core et un Field. Ici le mot input peut désigner à la fois la balise input html classique mais aussi select, textarea ou tout autres entrée d'un formulaire.
Le Core possède la balise input, select, radio, etc. à proprement parler ainsi que quelques labels internes pour certains et le Field englobe le Core et apporte la grande partie des labels, le composant helper et le composant validator.
La suite détaille ce système.
## Gestions des props et des attributs
Gérer via : useInputProps
- Gestion centraliser de toutes les props et les attributs dans un composable.
- A cela on rajoute les props et attr universel apporté par uiHelper (class, id, ariaLabel, role, disabled, tabindex et style).
- Les Fields intègrent les Cores, les props des Cores doivent être présents également dans les props des Fields. De cette façon, utiliser un fichier commun permet de ne pas répéter du code.
- Les attributs et les évènements doivent transmis in fine à l'input.
- Attention à la transmission des attributs html, events, etc car des props peuvent être pour des events (ex le type de l'input peut être prit pour onType). C'est une partie délicate où il faut faire attention de filtrer ce qui est transmis.
 
Tableau des props par type d'input et par FIELD ou CORE
```js
// --- PROPS COMMUNES ---
/**
 * Props communes à TOUS les inputs (core + field)
 */

const COMMON_PROPS = [

    { key: 'modelValue', type: [String, Number, Boolean, Array, Object], default: '' },
    { key: 'name', type: String, default: '' },
    { key: 'placeholder', type: String, default: '' },
    { key: 'required', type: Boolean, default: false },
    { key: 'readonly', type: Boolean, default: false },
    { key: 'autocomplete', type: String, default: '' },
    { key: 'autofocus', type: Boolean, default: false },
    { key: 'inputStyle', type: Object, default: null },
    { key: 'variant', type: String, default: 'glass', validator: validateVariant },
    { key: 'size', type: String, default: 'md', validator: validateSize },
    { key: 'color', type: String, default: 'primary', validator: validateColor },
    { key: 'animation', type: [String, Boolean], default: true },
    { key: 'ariaLabel', type: String, default: '' },
    { key: 'aria-invalid', type: [Boolean, String], default: undefined },
    { key: 'field', type: Object, default: null },
];

/**
 * Props communes aux FIELDS uniquement
 */
const COMMON_FIELD_PROPS = [
    { key: 'label', type: [String, Object], default: '', validator: validateLabel },
    { key: 'helper', type: [String, Object], default: '', validator: validateHelper },
    { key: 'defaultLabelPosition', type: String, default: 'floating', validator: v => ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd', 'floating'].includes(v) },
    { key: 'validation', type: [String, Boolean, Object, Number], default: undefined },
    { key: 'actions', type: [Array, Object, String], default: undefined },
];

/**
 * Événements communs à tous les inputs
 */
const COMMON_EVENTS = ['onFocus', 'onBlur'];

// --- PROPS SPÉCIFIQUES PAR TYPE ---
/**
 * Props spécifiques par type d'input
 */

const SPECIFIC_PROPS = {

    input: {

        core: [

            { key: 'labelFloating', type: Boolean, default: false },

            { key: 'labelInStart', type: String, default: '' },

            { key: 'labelInEnd', type: String, default: '' },

            { key: 'type', type: String, default: 'text' },

            { key: 'maxlength', type: [String, Number], default: undefined },

            { key: 'minlength', type: [String, Number], default: undefined },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onInput', 'onChange', 'onKeydown', 'onKeyup'],

    },

    select: {

        core: [

            { key: 'multiple', type: Boolean, default: false },

            { key: 'options', type: Array, default: () => [] },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    textarea: {

        core: [

            { key: 'labelFloating', type: Boolean, default: false },

            { key: 'labelInStart', type: String, default: '' },

            { key: 'labelInEnd', type: String, default: '' },

            { key: 'rows', type: Number, default: 3 },

            { key: 'cols', type: Number, default: 50 },

            { key: 'maxlength', type: [String, Number], default: undefined },

            { key: 'minlength', type: [String, Number], default: undefined },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onInput', 'onChange', 'onKeydown', 'onKeyup'],

    },

    radio: {

        core: [

            { key: 'value', type: [String, Number, Boolean], default: '' },

            { key: 'checked', type: Boolean, default: false },

            { key: 'type', type: String, default: 'radio' },

            { key: 'options', type: Array, default: () => [] },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    range: {

        core: [

            { key: 'min', type: [String, Number], default: 0 },

            { key: 'max', type: [String, Number], default: 100 },

            { key: 'step', type: [String, Number], default: 1 },

            { key: 'type', type: String, default: 'range' },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onInput', 'onChange'],

    },

    rating: {

        core: [

            { key: 'min', type: [String, Number], default: 0 },

            { key: 'max', type: [String, Number], default: 5 },

            { key: 'number', type: Number, default: 5 },

            { key: 'numberChecked', type: Number, default: 0 },

            { key: 'defaultMask', type: String, default: 'mask-star', validator: v => maskList.includes(v) },

            { key: 'items', type: Array, default: null },

            { key: 'type', type: String, default: 'radio' },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    toggle: {

        core: [

            { key: 'checked', type: Boolean, default: false },

            { key: 'indeterminate', type: Boolean, default: false },

            { key: 'styleState', type: Object, default: null },

            { key: 'type', type: String, default: 'checkbox' },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    filter: {

        core: [

            { key: 'value', type: [String, Number, Boolean], default: '' },

            { key: 'checked', type: Boolean, default: false },

            { key: 'type', type: String, default: 'radio' },

            { key: 'options', type: Array, default: () => [] },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onInput', 'onChange'],

    },

    file: {

        core: [

            { key: 'labelFloating', type: Boolean, default: false },

            { key: 'labelInStart', type: String, default: '' },

            { key: 'labelInEnd', type: String, default: '' },

            { key: 'accept', type: String, default: '' },

            { key: 'multiple', type: Boolean, default: false },

            { key: 'capture', type: String, default: '' },

            { key: 'maxSize', type: Number, default: 0 },

            { key: 'useProgress', type: Number, default: null },

            { key: 'showPreview', type: Boolean, default: true },

            { key: 'type', type: String, default: 'file' },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    checkbox: {

        core: [

            { key: 'indeterminate', type: Boolean, default: false },

            { key: 'styleState', type: Object, default: null },

            { key: 'value', type: [String, Number, Boolean], default: '' },

            { key: 'options', type: Array, default: () => [] },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    color: {

        core: [

            { key: 'type', type: String, default: 'color' },

            { key: 'format', type: String, default: 'hex', validator: v => ['hex', 'rgb', 'rgba', 'hsl', 'hsla'].includes(v) },

            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },

            { key: 'colorsDefault', type: Array, default: () => [

                '#000000', '#FFFFFF', '#FF1900', '#F47365', '#FFB243', '#FFE623',

                '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF', '#5D61FF', '#FF89CF',

                '#FC3CAD', '#BF3DCE', '#8E00A7', 'rgba(0,0,0,0)'

            ]},

            { key: 'colorsHistoryKey', type: String, default: 'vue-colorpicker-history' },

            { key: 'suckerHide', type: Boolean, default: true },

            { key: 'showValue', type: Boolean, default: true },

            { key: 'showPreview', type: Boolean, default: true },

            { key: 'showFormat', type: Boolean, default: true },

            { key: 'showRandom', type: Boolean, default: true },

            { key: 'showClear', type: Boolean, default: true },

            { key: 'colorPicker', type: Boolean, default: true },

        ],

        field: [],

        events: [...COMMON_EVENTS, 'onChange'],

    },

    date: {

        core: [

            { key: 'min', type: [Date, String], default: null },

            { key: 'max', type: [Date, String], default: null },

            { key: 'format', type: String, default: 'YYYY-MM-DD' },

            { key: 'locale', type: String, default: 'fr' },

            { key: 'value', type: [Date, String], default: null },

            { key: 'placeholder', type: String, default: 'Sélectionner une date' },

            { key: 'clearable', type: Boolean, default: true },

            { key: 'weekStart', type: Number, default: 1 },

            { key: 'firstDayOfWeek', type: Number, default: 1 },

            { key: 'showWeekNumbers', type: Boolean, default: false },

            { key: 'showToday', type: Boolean, default: true },

            { key: 'todayLabel', type: String, default: 'Aujourd\'hui' },

            { key: 'clearLabel', type: String, default: 'Effacer' },

            { key: 'previousLabel', type: String, default: 'Précédent' },

            { key: 'nextLabel', type: String, default: 'Suivant' },

            { key: 'monthLabel', type: String, default: 'Mois' },

            { key: 'yearLabel', type: String, default: 'Année' },

            { key: 'disabledDates', type: Array, default: () => [] },

            { key: 'enabledDates', type: Array, default: () => [] },

            { key: 'range', type: Boolean, default: false },

            { key: 'multiple', type: Boolean, default: false },

            { key: 'autoClose', type: Boolean, default: true },

            { key: 'position', type: String, default: 'bottom', validator: v => ['top', 'bottom', 'left', 'right'].includes(v) },

            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },

        ],

        field: [ ],

        events: [...COMMON_EVENTS, 'onChange', 'onSelect', 'onClear', 'onOpen', 'onClose'],

    },

};
```

## Validation des données
Gérer via useValidation et le composant Validator
- Props validation : objet {error : "msg d'erreur", success: "msg de succès", warning : "msg d'attention", info : "message d'information"}
- Slots pour plus de possibilités : validationError, validationSuccess, validationInfo, validationWarning
- affichage avec l'atom Validator
- Possibilité de transformer une validation en Notification via la prop :
  error : {notification:{ label:"msg d'erreur", duration: 4000, color:"error"}, success: etc }
## Helper
- Aide pour l'utilisateur via la prop helper et le slot helper qui renvoient vers le composant atom Helper. 
- La prop est soit une string avec un simple message soit un objet avec {message : msg d'helper, icon: lien pour l'icône, color: couleur, size: taille}. Si l'helper est une prop string alors c'est le style global qui est utilisé.
## Gestion des labels
Gérer via useInputLabel
-  Une prop label dans Field prenant soit une string soit un objet :
  l'objet peut prendre en paramètre chaque position de label avec un message associé.
  ex : {top : "msg au top", floating: "message flottant", start: "message de début"}
- Pour les Core (inline) :
  Il n'est pas possible que floating coexiste avec inStart ou inEnd mais inStart et inEnd peuvent coexister.
	- inStart, slot labelInStart
	- inEnd slot labelInEnd
	- floating slot labelFloating
	  A noter que certain Core n'ont aucune label inline (comme checkbox, radio, etc)
- Field (externe) :
  Tous ces labels peuvent coexister ensemble.
	- Top utilisation de l'atom Label, slot labelTop
	- Bottom utilisation de l'atom Label, slot labelBottom
	- Start utilisation de l'atom Label, slot labelStart
	- End utilisation de l'atom Label, slot labelEnd
	  Tout les Fields possèdent ces labels
## Affichage supplémentaire avec overStart et overEnd
- Tout les Fields possédent les slots overStart et overEnd permettant d'afficher des composants (souvent des boutons icons) par dessus l'input.
## Gestion des actions
Gérer par useInputActions
- Affiché par les slots overEnd. La configuration permet d'indiquer la couleur, le variant du bouton et l'icone a affiché ainsi que le tooltip.
- Différentes actions possibles :
	- copy (défaut false) (copie le contenu)
	- reset (défaut false) (revient à la modification au chargement de la vue)
	- back (défaut false) (revient à la dernière modification)
	- clear (défaut false) (efface le contenu)
	- toggleEdit (défaut false) (passe de readOnly à edit et vice versa)
	- togglePassword (uniquement pour les inputs type password) (permet d'afficher les mdp en clair)
	- toggleDisabled (désactive le champs)
	  utilisé par défaut dans les Inputs de type password si le navigateur ne prend pas cette fonctionnalité en charge). Pas d'actions de reset, copy ou back pour les inputs password.
- Props permettant de gérer les actions : objets où l'on peut passer les actions que l'on souhaite activer (ou désactiver pour togglePassword qui est le seul à pouvoir s'activer automatiquement) et passer des paramètres aux actions.
  ex : {back:{delay:500}, copy:{notificationMessage:"Copie dans votre presse-papier"}}
Tableau des actions possibles avec les paramètres en fonction des inputs : 
```js
// --- CONFIGURATION DES ACTIONS ---

const ACTIONS_CONFIGURATION = {
  reset: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir réinitialiser ce champ ?',
    },
    icon: 'fa-solid fa-arrow-rotate-left',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Réinitialiser',
    tooltip: 'Revenir à la valeur initiale',
    actionKey: 'reset',
  },
  back: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 500, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir annuler la dernière modification ?',
    },
    icon: 'fa-solid fa-rotate-left',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Annuler la dernière modification',
    tooltip: 'Annuler la dernière modification',
    actionKey: 'back',
  },
  clear: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: 'Êtes-vous sûr de vouloir vider ce champ ?',
    },
    icon: 'fa-solid fa-xmark',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Vider le champ',
    tooltip: 'Vider le champ',
    actionKey: 'clear',
  },
  copy: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating'],
    options: {
      delay: 1000, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: {
        message: 'Le contenu du champ a été copié dans le presse-papiers',
        type: 'success',
        icon: 'fa-solid fa-copy',
        duration: 2000,
      }, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-copy',
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Copier le contenu',
    tooltip: 'Copier le contenu',
    actionKey: 'copy',
  },
  password: {
    compatibility: ['password'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: false, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-eye', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Afficher le mot de passe', // sera dynamique

    tooltip: 'Afficher le mot de passe', // sera dynamique

    actionKey: 'password',
  },
  edit: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: true, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-pen', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "success",
    variant: "ghost",
    ariaLabel: 'Passer en édition', // sera dynamique
    tooltip: 'Passer en édition', // sera dynamique
    actionKey: 'edit',
  },
  lock: {
    compatibility: ['input', 'textarea', 'select', 'file', 'range', 'rating', 'checkbox', 'radio', 'toggle', 'filter'],
    options: {
      delay: 100, // délai avant de pouvoir refaire l'action
      autofocus: true, // autofocus sur le champ
      destroy: false, // détruire l'action après l'utilisation
      notify: false, // notifier l'utilisateur après l'utilisation
      confirm: false, // demander confirmation avant l'action
      confirmMessage: '',
    },
    icon: 'fa-solid fa-unlock', // sera dynamique
    size: 'auto', // dépend de l'input
    color: "neutral",
    variant: "ghost",
    ariaLabel: 'Activer le champ', // sera dynamique
    tooltip: 'Activer le champ', // sera dynamique
    actionKey: 'lock',
  },
};
```
## Gestion des styles
Gérer par useInputStyle
- Props :
	- Variant : glass, outline, ghost, dash, soft
	- Size : xs, sm, md, lg, xl
	- Color :  primary, secondary, success, error, accent, info, warning ou personnalisé grâce à tailwind comme color-green-400
	- animation : bool | string pour en choisir une précisement suivant ce qui sera disponible selon le type d'input
	- inputStyle : objet : permettant de définir en une prop toutes les valeurs concernant le style
		  ex : { variant : glass, size : md, color : primary, animation : "rotated-border" }
- Le composable permet de créer un style même si tout les props ne sont pas indiqué en utilisant des valeurs par défaut de l'input. Il ressort donc un style qui pourra être transmit au label, au validator et au helper.
- Les styles a proprement parlé son défini par un mixte de classe tailwind / DaisyUI et du scss custom placé dans chaque vue avec scoped pour limité la portée.