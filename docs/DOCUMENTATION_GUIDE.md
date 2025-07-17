# Guide de documentation — KrosmozJDR

## 1. Documentation projet (`/docs/`)

- **Synthétique** : chaque fichier va à l’essentiel, pas de pavés, pas de redites.
- **Adaptée LLM** : listes, exemples courts, titres explicites, liens croisés.
- **Exhaustive** : chaque concept important a son fichier dédié (éviter les gros fichiers fourre-tout).
- **Navigation** : chaque dossier a un `README.md` synthétique pour guider l’exploration.
- **Exemples** : toujours illustrer par 1 ou 2 exemples concrets, pas plus.
- **Découpage** : préférer plusieurs petits fichiers à un seul long (ex : INPUTS.md, VALIDATION.md, NOTIFICATIONS.md séparés).
- **Pas d’historique** : la documentation décrit l’état actuel, pas l’évolution du projet.

#### Exemple de structure :
```
/docs/
  00-Project/README.md
  40-UI/INPUTS.md
  40-UI/VALIDATION.md
  40-UI/NOTIFICATIONS.md
  ...
```

---

## 2. Documentation du code (dans le code source)

- **Docbloc synthétique** : chaque composant/fonction a un docbloc court, clair, sans historique de refactoring.
- **Exemples** : inclure un exemple d’utilisation minimal si pertinent.
- **Pas de commentaires inutiles** : ne pas commenter l’évidence ou l’évolution (“// refactoring du composant X”).
- **Lien vers la doc projet** : si un composant suit un pattern documenté, référencer le fichier de doc concerné.

#### Exemple de docbloc :
```js
/**
 * InputField — Champ de saisie complet (molecule)
 * @props {String|Object} label — Label du champ (positions multiples)
 * @props {Object|String|Boolean} validation — Validation factorisée
 * @example
 * <InputField label="Email" v-model="email" :validation="{ state: 'error', message: 'Email invalide' }" />
 */
```

---

## 3. Où documenter quoi ?

- **Règles de code, conventions, style** : `docs/10-BestPractices/BEST_PRACTICES.md`
- **Comment écrire la doc** : `docs/DOCUMENTATION_GUIDE.md` (ce fichier)
- **API, patterns, UI, entités, etc.** : un fichier dédié par concept dans le bon dossier
- **Checklists, TODO, idées** : à garder dans un dossier backup ou personnel, pas dans la doc principale

---

## 4. Conseils pour LLM et équipe

- **Un concept = un fichier** (éviter les gros chapitres)
- **README synthétique** à chaque niveau
- **Exemples courts et factorisés**
- **Pas de duplication**
- **Liens croisés** pour faciliter la navigation automatique

---