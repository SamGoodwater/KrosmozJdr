# 📁 Système d'Upload de Fichiers

## 📋 Vue d'ensemble

Le système d'upload de fichiers de KrosmozJDR gère l'upload, la prévisualisation, la validation et la suppression de fichiers. Côté **frontend** il utilise les composants `FileCore` (Atom) et `FileField` (Molecule), ainsi que les composables `useFileUpload` et `useDragAndDrop`. Côté **backend**, les fichiers attachés aux modèles (sections, utilisateur avatar, caractéristiques icône, ressources image) sont **tous gérés par [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction)** : le composant File envoie le fichier vers les routes API (ex. `sections.files.store`, `user.updateAvatar`, `admin.characteristics.upload-icon`, `api.entities.resources.upload-image`), et chaque contrôleur attache le média via `addMediaFromRequest()` / `toMediaCollection()`. Conversions WebP et miniatures : voir [Spatie Media Library — Medias](../../50-Fonctionnalités/Medias/SPATIE_MEDIA_LIBRARY.md).

---

## 🎯 Composants

### **FileCore.vue** - Atom de base

Composant atomique pour l'input file natif, stylé avec DaisyUI.

```vue
<FileCore 
  v-model="file"
  accept="image/*"
  variant="glass"
  color="primary"
/>
```

**Props principales :**
- `accept` : Types MIME acceptés (ex: "image/*", ".pdf,.doc")
- `multiple` : Sélection multiple de fichiers
- `capture` : Capture média ("user", "environment")
- `variant`, `color`, `size` : Styles DaisyUI

### **FileField.vue** - Molecule complète

Composant complet avec preview, validation, drag & drop et gestion des fichiers existants.

```vue
<FileField 
  v-model="newFile"
  label="Avatar"
  :currentPath="user.avatar"
  defaultPath="/storage/images/avatar/default_avatar_head.webp"
  :canDelete="true"
  accept="image/*"
  :maxSize="5242880"
  @delete="handleDelete"
  @error="handleError"
/>
```

**Props principales :**
- `v-model` : Fichier sélectionné (File object)
- `currentPath` : Chemin du fichier existant à afficher (URL string)
- `defaultPath` : Chemin du fichier par défaut (non supprimable)
- `canDelete` : Si on peut supprimer le fichier (défaut: true)
- `maxSize` : Taille maximale en octets
- `accept` : Types MIME acceptés

**Événements :**
- `@delete` : Émis quand l'utilisateur supprime le fichier
- `@error` : Émis en cas d'erreur (validation, etc.)
- `@update:currentFile` : Émis quand un nouveau fichier remplace l'ancien

**Slots :**
- `#default` : Slot personnalisé pour l'affichage du fichier
  - Props disponibles : `{ file, type, url, name, size, source, canDelete }`

---

## 🔧 Composables

### **useFileUpload**

Composable centralisé pour la gestion complète des uploads.

```javascript
import useFileUpload from '@/Composables/form/useFileUpload'

const {
  fileToDisplay,      // Fichier actuellement affiché
  previewUrls,        // URLs de preview
  hasFileToDisplay,   // Boolean : y a-t-il un fichier à afficher ?
  hasPreview,         // Boolean : y a-t-il une preview de nouveau fichier ?
  canDeleteFile,      // Boolean : peut-on supprimer le fichier ?
  getFileType,        // Fonction : détermine le type de fichier
  validateFile,       // Fonction : valide un fichier
  reset,              // Fonction : réinitialise le composable
  deleteFile,         // Fonction : supprime le fichier
  extractFiles        // Fonction : extrait les fichiers d'un input
} = useFileUpload({
  modelValue: computed(() => props.modelValue),
  currentPath: computed(() => props.currentPath),
  defaultPath: computed(() => props.defaultPath),
  canDelete: props.canDelete,
  maxSize: props.maxSize,
  onError: (error) => emit('error', error),
  onUpdateCurrentFile: (file) => emit('update:currentFile', file)
})
```

**Fonctionnalités :**
- Détection automatique du type de fichier (image, vidéo, audio, document)
- Validation de la taille et du type MIME
- Création de previews avec `URL.createObjectURL`
- Gestion de la priorité d'affichage : nouveau fichier > currentPath > defaultPath
- Détection automatique des fichiers par défaut (non supprimables)
- Nettoyage automatique des URLs blob

### **useDragAndDrop**

Composable pour la gestion du drag & drop de fichiers.

```javascript
import useDragAndDrop from '@/Composables/form/useDragAndDrop'

const { isDragging, dragHandlers } = useDragAndDrop({
  onFilesDropped: (file) => {
    emit('update:modelValue', file)
  },
  accept: props.accept
})
```

**Fonctionnalités :**
- Gestion des événements drag & drop
- Validation des types MIME acceptés
- Compteur de drag pour gérer les zones imbriquées
- État réactif `isDragging` pour l'affichage d'un overlay

---

## 🖼️ Composants de Preview

### **FilePreview.vue**

Composant atomique pour afficher un aperçu de fichier selon son type.

```vue
<FilePreview
  :file="fileObject"
  :url="previewUrl"
  type="image"
  :name="fileName"
  :size="fileSize"
  :canDelete="true"
  @delete="handleDelete"
/>
```

**Types supportés :**
- `image` : Utilise le composant `Image.vue`
- `video` : Utilise le composant `Video.vue`
- `audio` : Utilise le composant `Audio.vue`
- `file` : Utilise le composant `Document.vue`

### **Image.vue, Video.vue, Audio.vue, Document.vue**

Composants atomiques dédiés pour chaque type de fichier, avec bouton de suppression intégré.

---

## 📝 Exemples d'utilisation

### **Upload simple**

```vue
<template>
  <FileField 
    v-model="file"
    label="Document"
    accept=".pdf,.doc,.docx"
    :maxSize="5242880"
    helper="Taille maximale : 5MB"
  />
</template>

<script setup>
import { ref } from 'vue'
import FileField from '@/Pages/Molecules/data-input/FileField.vue'

const file = ref(null)
</script>
```

### **Upload avec fichier existant et par défaut**

```vue
<template>
  <FileField 
    v-model="avatarFile"
    label="Avatar"
    :currentPath="user?.avatar"
    defaultPath="/storage/images/avatar/default_avatar_head.webp"
    :canDelete="true"
    accept="image/*"
    :maxSize="5242880"
    @delete="deleteAvatar"
    @error="handleError"
  >
    <template #default="{ url, source, canDelete }">
      <div class="relative inline-block group">
        <Avatar
          :src="url || user?.avatar || '/storage/images/avatar/default_avatar_head.webp'"
          :label="user?.name"
          size="3xl"
          rounded="full"
        />
        <button
          v-if="canDelete"
          type="button"
          @click="deleteAvatar"
          class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity btn btn-sm btn-error btn-circle"
        >
          <i class="fa-solid fa-trash-can"></i>
        </button>
      </div>
    </template>
  </FileField>
</template>

<script setup>
import { ref } from 'vue'
import FileField from '@/Pages/Molecules/data-input/FileField.vue'
import Avatar from '@/Pages/Atoms/data-display/Avatar.vue'

const avatarFile = ref(null)

const deleteAvatar = () => {
  // Logique de suppression
}
</script>
```

### **Upload avec validation personnalisée**

```vue
<template>
  <FileField 
    v-model="coverImage"
    label="Image de couverture"
    accept="image/*"
    :maxSize="2 * 1024 * 1024"
    :validation="coverValidation"
    helper="Formats acceptés: JPG, PNG (max 2MB)"
  />
</template>

<script setup>
import { ref, computed } from 'vue'
import FileField from '@/Pages/Molecules/data-input/FileField.vue'

const coverImage = ref(null)

const coverValidation = computed(() => ({
  rules: [
    {
      validator: (value) => {
        if (!value) return true
        const file = value
        const validTypes = ['image/jpeg', 'image/png', 'image/webp']
        return validTypes.includes(file.type)
      },
      message: 'Seuls les formats JPG, PNG et WEBP sont acceptés'
    }
  ],
  trigger: 'change'
}))
</script>
```

---

## 🎨 Design et Style

### **Drag & Drop Overlay**

Le système inclut un overlay glassmorphism automatique lors du drag & drop :

- Fond semi-transparent avec `backdrop-filter: blur(12px)`
- Bordure en pointillés avec couleur primary
- Animation fluide avec transitions `cubic-bezier`
- Icône animée avec `animate-bounce`

### **Bouton de suppression**

- Icône : `fa-trash-can` (FontAwesome)
- Style : `btn btn-sm btn-error btn-circle`
- Affichage : Au survol (`opacity-0 group-hover:opacity-100`)
- Ombres : `shadow-lg hover:shadow-xl`

---

## ⚙️ Fonctionnalités avancées

### **Priorité d'affichage**

Le système gère automatiquement la priorité d'affichage :

1. **Nouveau fichier** (preview) : Affiché en priorité si un fichier vient d'être sélectionné
2. **currentPath** : Affiché si un fichier existant est disponible
3. **defaultPath** : Affiché en dernier recours si aucun autre fichier n'est disponible

### **Détection des fichiers par défaut**

Le système détecte automatiquement les fichiers par défaut (non supprimables) en comparant `currentPath` et `defaultPath` après normalisation des chemins.

### **Nettoyage automatique**

Les URLs blob créées avec `URL.createObjectURL` sont automatiquement nettoyées :
- Lors du démontage du composant (`onUnmounted`)
- Lors de la sélection d'un nouveau fichier
- Lors de la réinitialisation du composable

---

## 🔍 Détection du type de fichier

Le système détecte automatiquement le type de fichier :

- **Images** : `.jpg`, `.jpeg`, `.png`, `.gif`, `.webp`, `.svg`, `.bmp`
- **Vidéos** : `.mp4`, `.webm`, `.ogg`, `.avi`, `.mov`, `.wmv`
- **Audio** : `.mp3`, `.wav`, `.ogg`, `.aac`, `.flac`, `.m4a`
- **Documents** : Tous les autres types

La détection se fait via :
- Le type MIME pour les `File` objects
- L'extension pour les URLs string

---

## 🚀 Bonnes pratiques

1. **Toujours nettoyer les previews** : Le système le fait automatiquement, mais assurez-vous de réinitialiser `modelValue` après un upload réussi
2. **Utiliser `currentPath` et `defaultPath`** : Pour une meilleure UX, fournissez toujours un `defaultPath` pour les avatars/images
3. **Valider côté serveur** : La validation côté client est pratique, mais toujours valider côté serveur
4. **Gérer les erreurs** : Utilisez l'événement `@error` pour afficher des messages d'erreur à l'utilisateur
5. **Optimiser les images** : Utilisez `ImageService` côté serveur pour générer des thumbnails

---

## 🧪 Tests

Les flux d’upload reliés à Media Library sont couverts par des tests Feature et Unit :

| Contexte | Fichier de test | Tests |
|----------|-----------------|-------|
| Section (fichiers) | `SectionControllerTest` | `test_section_file_upload_via_media_library`, `test_section_file_delete` |
| Utilisateur (avatar) | `UserControllerTest` | `test_user_can_upload_avatar`, `test_user_can_delete_avatar` |
| Caractéristique (icône) | `CharacteristicControllerTest` | `test_admin_can_upload_characteristic_icon` |
| Scrapping (attach image) | `IntegrationServiceTest` | `test_attach_image_from_url_*` (URL vide, téléchargement désactivé, hôte non autorisé) |
| Avatar path | `UserTest` | `test_avatar_path_returns_default_if_none` |

L’upload d’image des ressources (API `api.entities.resources.upload-image`) peut être couvert par un test Feature dédié si besoin.

---

## 📚 Références

- [Composants Input System](../INPUT%20SYSTEM/README.md)
- [Architecture Input System](../INPUT%20SYSTEM/ARCHITECTURE.md)
- [Design Guide](../DESIGN_GUIDE.md)

