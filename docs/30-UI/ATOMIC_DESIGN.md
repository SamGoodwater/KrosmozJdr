# Documentation UI KrosmozJDR

Bienvenue dans la documentation des composants UI, validation et notifications du projet.

## Philosophie
- **Atomic Design** : séparation claire entre Atoms, Molecules, Organisms, Composables
- **Factorisation** : toute la logique métier et toutes les props (HTML, layout, validation, actions, etc.) sont centralisées dans des composables et dans `inputHelper.js` (API DRY)
- **API déclarative** : toutes les fonctionnalités sont activables via des props ou des hooks
- **Extensibilité** : facile d'ajouter de nouvelles actions, règles de validation, ou types de notifications

## Sommaire

- [Inputs](./INPUTS.md) : Structure, API, slots, actions contextuelles, bonnes pratiques
- [Validation](./VALIDATION.md) : useValidation, Validator, helpers, gestion des erreurs serveur
- [Notifications](./NOTIFICATIONS.md) : useNotificationStore, NotificationContainer, API, exemples
- [Intégration Validation + Notifications](./INTEGRATION_VALIDATION_NOTIFICATIONS.md) : Cas d’usage croisés, migration, FAQ, patterns avancés

## Chemin de lecture conseillé
1. **Commencez par [Inputs](./INPUTS.md)** pour comprendre la structure factorisée (toutes les props sont héritées via `getInputProps`)
2. **Poursuivez avec [Validation](./VALIDATION.md)** pour la gestion des erreurs et du feedback utilisateur
3. **Consultez [Notifications](./NOTIFICATIONS.md)** pour l'intégration des toasts et feedback globaux
4. **Terminez par [Intégration Validation + Notifications](./INTEGRATION_VALIDATION_NOTIFICATIONS.md)** pour les cas d'usage avancés et la migration

---

**Pour toute question, commencez par le fichier correspondant à votre besoin, ou consultez ce README pour naviguer.**

<!-- Exemple factorisé -->
```vue
<script setup>
import { getInputProps } from '@/Utils/atomic-design/inputHelper';
const props = defineProps({ ...getInputProps('input', 'field') });
``` 