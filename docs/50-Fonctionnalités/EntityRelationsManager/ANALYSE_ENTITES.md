# Analyse des entités pour EntityRelationsManager

## Entités prioritaires (haute valeur)

### 1. **Creature** ⭐⭐⭐
**Relations many-to-many :**
- `items` (avec pivot `quantity`) - **PRIORITAIRE**
- `resources` (avec pivot `quantity`) - **PRIORITAIRE**
- `consumables` (avec pivot `quantity`) - **PRIORITAIRE**
- `spells` - **PRIORITAIRE**
- `attributes` - Utile
- `capabilities` - Utile

**Pourquoi :** Les créatures ont plusieurs relations avec quantités (items, resources, consumables) qui sont fréquemment modifiées. Le composant actuel devra être étendu pour gérer les pivots (quantités).

**Complexité :** Moyenne (nécessite support des pivots)

---

### 2. **Scenario** ⭐⭐⭐
**Relations many-to-many :**
- `items` - **PRIORITAIRE**
- `consumables` - **PRIORITAIRE**
- `resources` - **PRIORITAIRE**
- `spells` - **PRIORITAIRE**
- `panoplies` - **PRIORITAIRE**
- `shops` - Utile
- `npcs` - Utile
- `monsters` - Utile

**Pourquoi :** Les scénarios sont au cœur du système et ont de nombreuses relations avec des entités importantes. La gestion de ces relations est cruciale pour la création de scénarios.

**Complexité :** Faible (pas de pivot)

---

### 3. **Shop** ⭐⭐⭐
**Relations many-to-many :**
- `items` (avec pivot `quantity`, `price`, `comment`) - **PRIORITAIRE**
- `consumables` (avec pivot `quantity`, `price`, `comment`) - **PRIORITAIRE**
- `resources` (avec pivot `quantity`, `price`, `comment`) - **PRIORITAIRE**
- `panoplies` - Utile

**Pourquoi :** Les boutiques ont des relations avec des pivots complexes (quantité, prix, commentaire). C'est un cas d'usage important pour la gestion des ventes.

**Complexité :** Élevée (nécessite support des pivots multiples)

---

### 4. **Spell** ⭐⭐
**Relations many-to-many :**
- `classes` - **PRIORITAIRE** (relation importante pour le gameplay)
- `creatures` - Utile
- `scenarios` - Utile
- `campaigns` - Utile
- `spellTypes` - **PRIORITAIRE** (classification des sorts)
- `monsters` (invocations) - Utile

**Pourquoi :** Les sorts ont des relations importantes avec les classes et les types de sorts. La gestion des classes associées à un sort est fréquente.

**Complexité :** Faible (pas de pivot)

---

### 5. **Campaign** ⭐⭐
**Relations many-to-many :**
- `items` - Utile
- `consumables` - Utile
- `resources` - Utile
- `spells` - Utile
- `panoplies` - Utile
- `shops` - Utile
- `npcs` - Utile
- `monsters` - Utile
- `scenarios` - **PRIORITAIRE**
- `users` - **PRIORITAIRE** (joueurs de la campagne)
- `pages` - Utile
- `files` (avec pivot `order`) - Utile

**Pourquoi :** Les campagnes ont beaucoup de relations, mais certaines sont moins fréquemment modifiées. Les relations avec les scénarios et les utilisateurs sont les plus importantes.

**Complexité :** Moyenne (quelques pivots)

---

### 6. **Item** ⭐
**Relations many-to-many :**
- `resources` (avec pivot `quantity`) - **PRIORITAIRE** (recette de craft)
- `panoplies` - Utile (déjà géré côté panoply)
- `scenarios` - Utile
- `campaigns` - Utile
- `shops` (avec pivot `quantity`, `price`, `comment`) - Utile

**Pourquoi :** La relation la plus importante est `resources` avec quantité (recette de craft). Les autres relations sont moins fréquemment modifiées.

**Complexité :** Moyenne (pivot quantity pour resources)

---

## Entités secondaires (moins prioritaires)

### 7. **Npc**
**Relations :**
- `panoplies` - Utile
- `scenarios` - Utile
- `campaigns` - Utile

**Pourquoi :** Moins de relations, moins fréquemment modifiées.

---

### 8. **Monster**
**Relations :**
- `scenarios` - Utile
- `campaigns` - Utile
- `spells` (invocations) - Utile

**Pourquoi :** Relations simples, moins fréquemment modifiées.

---

## Recommandations d'implémentation

### Phase 1 : Relations simples (sans pivot) ⚡ RAPIDE
1. ✅ **Panoply → Items** (déjà fait)
2. **Spell → Classes** (haute valeur, simple, page Edit existe)
3. **Spell → SpellTypes** (haute valeur, simple, page Edit existe)
4. **Scenario → Items, Consumables, Resources, Spells, Panoplies** (haute valeur, simple)

**Avantages Phase 1 :**
- Pas de modification du composant nécessaire
- Implémentation rapide
- Valeur immédiate pour les utilisateurs

### Phase 2 : Relations avec pivot simple (quantity)
1. **Creature → Items, Resources, Consumables** (avec quantité)
2. **Item → Resources** (recette de craft avec quantité)

### Phase 3 : Relations avec pivots multiples
1. **Shop → Items, Consumables, Resources** (avec quantity, price, comment)
2. **Item → Shops** (avec quantity, price, comment)

### Phase 4 : Relations complexes
1. **Campaign → Scenarios, Users** (gestion de campagne)
2. **Scenario → Files** (avec ordre)

---

## Notes techniques

### Support des pivots
Le composant actuel `EntityRelationsManager` ne gère pas encore les pivots. Pour les relations avec quantités/prix, il faudra :
- Ajouter des champs de saisie pour les valeurs du pivot
- Modifier la sauvegarde pour inclure les données du pivot
- Adapter l'affichage pour montrer les valeurs du pivot

### Relations bidirectionnelles
Certaines relations sont bidirectionnelles (ex: Item ↔ Panoply). Il faut décider où placer l'interface de gestion :
- **Recommandation :** Gérer depuis l'entité "principale" (Panoply gère ses items, pas l'inverse)

---

## Priorisation finale

**Top 5 à implémenter en priorité :**
1. ✅ Panoply → Items (fait)
2. Spell → Classes
3. Spell → SpellTypes
4. Scenario → Items, Consumables, Resources, Spells, Panoplies
5. Creature → Items, Resources, Consumables, Spells (avec quantités)

