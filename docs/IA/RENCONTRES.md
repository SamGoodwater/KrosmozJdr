# Rencontres et PNJ (à la demande)

Monstres, sorts de créature et PNJ ne se génèrent **pas** en masse au départ. On les crée quand un scénario en a besoin : moins cher, relecture d’une fiche à la fois.

## Paquet monstre + sorts

Un monstre JDR n’est pas une créature Dofus + 8 sorts importés. C’est **une fiche + 1 à 3 actions**.

À générer **dans le même appel** (les **stats** du monstre restent figées par défaut, voir les réglages IA) :

- 2–3 sorts-créature (attaque, particularité, éventuellement un passif) ;
- loot simple si besoin.

Les sorts doivent coller aux caracs déjà présentes (Terre ↔ Force, peu de sorts, budget PA). Deux appels séparés recréent l’aberration « sorts Terre / Force 0 ». Pour laisser l’IA retoucher une carac, l’ajouter dans les exceptions de l’admin IA (`writable_characteristics`).

**Socle few-shot déjà en base** : 19 invocations de classe `playable` (`jdr:summon:…`, Tofu, Poupée, Harponneuse…) **et** 28 monstres de bestiaire `playable` (`jdr:bestiary:…`, Incarnam + Astrub / Tainela / plage / cimetière : Tofu Chimérique, Piou Vert, Boufton, Gelée Bleuet, Chafer…). Invocations = 1 créature fragile (8/12/16 PV, 1 PM) + **1** sort-créature. Bestiaire = PA/PM Dofus, 1 à 2 sorts, hostilité, traits. C’est le modèle à imiter ; le LLM ne les génère pas et ne les passe pas en `playable`.

### Données

- Monstre = coquille `Monster` + stats sur `Creature`.
- Liaison : `creature_spell` (`Creature::spells()`).
- Les sorts créés ici sont des **sorts de créature**, pas des sorts de classe (Iop, Cra…).

Flux :

1. Brief MJ (« chef Bouftou niveau 10 ») → Laravel : fiche source + gabarit 5.1.2 (stats **hors** LLM par défaut) + `example_ids`.
2. Un JSON `{ monster, spells: [ … ] }` : le monstre ne porte que les clés `writable` ; les sorts-créature sont le delta.
3. Créer les `Spell` et poser le `Monster` / `Creature` en `auto` (`auto_update=false`), lier le pivot.
4. Relire **le paquet**, pas quatre fiches orphelines.

Commande : `php artisan ia:convert encounter --id=12` (alias `ia:convert-encounter`) ou `--official-id=jdr:bestiary:…`. UI : icône **Sources** → volet Conversion IA (admin).

Plus tard : réutiliser un sort `playable` déjà collé (« même crachat que le Bouftou ») au lieu d’en créer un. En v1, créer les 2–3 sorts dans le même JSON suffit.

Gabarits : `private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md` (PV, dégâts, CA par palier, archétypes, boss).

UX : un geste **« Sources »** (même icône que DofusDB) ouvre le modal deux volets. Le volet IA lance la conversion de **cette rencontre** (pas un sort isolé).

## Sorts de classe (hors créature)

Réécriture JDR d’un sort Dofus `raw` :

- **garder** nom, classe, élément, image, fantasy ;
- 1 effet principal + 0–2 secondaires jouables à table (c’est le delta IA) ;
- PA / portée / dés dans les grilles existantes ;
- pas de nouveaux types d’effets hors catalogue.

À faire **au fil de l’eau** (PNJ ou perso), pas un batch de tout le grimoire Dofus. Le mapping d’effets scrap (`SpellEffectsConversionService`, `dofusdb_effect_mappings`) reste la conversion brute ; l’IA propose un `auto` par-dessus (`ia:convert spell`, POST `/api/entities/spells/{id}/ia-convert`).

## PNJ

Le modèle est en place : coquille `Npc` + corps `Creature` + `breed_id` / `specialization_id`, langues, panoplies, boutique. Le **kit de jeu** (sorts connus, stuff porté 1/slot sauf 2 anneaux, sync d’état coquille → créature) est du **code applicatif** (`NpcController`, `NpcEquipmentSlotValidator`) — pas de génération LLM.

Contrairement aux objets / sorts / monstres Dofus, **il n’y a rien à figer** : l’IA crée nom, histoire, rôle, stats et kit. Option : partir d’une **page de site** (encyclopédie, wiki, DofusDB) — Laravel en extrait nom / portrait / lore, le modèle complète la fiche JDR. Pas de scrap de masse. `Npc.official_id` sert au seeder (`jdr:npc:incarnam:…`), pas à DofusDB.

La génération IA s’appuie sur ce schéma déjà persisté. Pipeline : `NpcSpecialization` + pré-filtre `NpcKitCatalog` injecté dans le prompt (`extraContext`). POST `/api/entities/npcs/{id}/ia-convert`, `ia:convert npc`. Détail des champs : [CHAMPS.md](./CHAMPS.md).

**Socle few-shot** : 5 PNJ `playable` d’Incarnam (`jdr:npc:incarnam:ganymede` … `fouduglen`) — classe + spe jouable + kit. Pré-filtre : `NpcKitCatalog` / `php artisan ia:npc-kit-catalog` (objets, sorts, gabarit 5.1.2). Ids few-shot résolus au runtime, pas dans `generation.json`.

Paquet :

```json
{
  "npc": { "concept": "…", "breed_id": 1, "level": 8 },
  "stats": { },
  "item_ids": [412, 880],
  "spell_ids": [55, 56, 90]
}
```

- **Objets** : toujours des `id` du pré-filtre `playable`, presque jamais d’invention.
- **Sorts de classe** : piocher dans le `playable` si la liste est assez riche ; sinon réécrire 1–2 sorts dans le même paquet (comme pour un monstre).
- Validateur : Force haute si voie Terre, stuff Force, sorts de la classe, niveau d’équipement, un item par slot.

Sans grille d’objets `playable`, ne pas lancer la génération de PNJ.

## Ce que le MJ fournit

Un brief court suffit : rôle, niveau, ton, lieu optionnel.

Exemples : « garde Iop d’Astrub, niveau 8, brutal, pas un boss » ; « chef Bouftou niveau 10 pour la fin de scène ».

Laravel complète gabarit + listes (+ extrait de page si fourni). Le modèle **crée** nom / story / comportement et choisit dans les listes. Sur un monstre Dofus, le nom et la race restent ceux de la source.
