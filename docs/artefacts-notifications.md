# Système de notifications Krosmoz JDR

## Philosophie
Le système de notification est **centralisé** via un service unique (`NotificationService`). Il respecte les préférences utilisateur (canaux, activation) et la logique métier (créateur, admins, profil). Les notifications sont envoyées via des classes dédiées (ex : `EntityModifiedNotification`, `ProfileModifiedNotification`).

## Bonnes pratiques
- **Toujours passer par le service** pour envoyer une notification métier.
- **Ne jamais instancier directement** une notification dans un contrôleur ou observer.
- **Centraliser la logique** (calcul des changements, canaux, etc.) dans le service.
- **Préférer la simplicité dans les contrôleurs** : le service gère tout.

## Utilisation dans un contrôleur
```php
$old = clone $entity;
$entity->update($data);
NotificationService::notifyEntityModified($entity, Auth::user(), $old);
```
Pour le profil utilisateur :
```php
$old = clone $user;
$user->update($data);
NotificationService::notifyProfileModified($user, Auth::user(), $old);
```

## Utilisation avancée (changements personnalisés)
```php
$customChanges = [
    'champ' => ['old' => 'A', 'new' => 'B'],
];
NotificationService::notifyEntityModified($entity, Auth::user(), null, $customChanges);
```

## Exemple d'observer
```php
class PageObserver {
    public function updated(Page $page) {
        $old = $page->getOriginal();
        NotificationService::notifyEntityModified($page, Auth::user(), $old);
    }
}
```

## Préférences utilisateur
- Les canaux (`database`, `email`) et l'activation sont stockés sur le modèle `User`.
- Les méthodes `wantsNotification`, `notificationChannels`, `wantsProfileNotification` sont utilisées par le service.

## Étendre le système
- Pour une nouvelle entité, il suffit d'appeler le service dans le contrôleur ou l'observer.
- Pour une nouvelle notification, créer une classe dédiée et l'intégrer dans le service.

## Résumé
- **Centralisation** : toute la logique métier et technique est dans le service.
- **Lisibilité** : les contrôleurs restent simples.
- **Extensibilité** : facile d'ajouter de nouveaux types de notifications.

---

**Auteur :** Krosmoz JDR
**Dernière mise à jour :** 2024-06 
