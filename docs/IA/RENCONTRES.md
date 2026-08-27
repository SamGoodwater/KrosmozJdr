# Rencontres et PNJ (à la demande)

Monstres, sorts de créature et PNJ ne se génèrent **pas** en masse au départ. On les crée quand un scénario en a besoin : moins cher, relecture d’une fiche à la fois.

## Paquet monstre + sorts

Un monstre JDR n’est pas une créature Dofus + 8 sorts importés. C’est **une fiche + 1 à 3 actions**.

À générer **dans le même appel** :

- stats selon gabarit (niveau, rôle : brute, tireur, soutien…) ;
- 2–3 sorts-créature (attaque, particularité, éventuellement un passif) ;
- loot simple si besoin.

Les sorts doivent coller aux caracs (Terre ↔ Force, peu de sorts, budget PA). Deux appels séparés recréent l’aberration « sorts Terre / Force 0 ».

### Données

- Monstre = coquille `Monster` + stats sur `Creature`.
- Liaison : `creature_spell` (`Creature::spells()`).
- Les sorts créés ici sont des **sorts de créature**, pas des sorts de classe (Iop, Cra…).

Flux prévu :

1. Brief MJ (« chef Bouftou niveau 10 ») → Laravel : gabarit 5.1.2 + quelques étalons `playable`.
2. Un JSON `{ monster, spells: [ … ] }`.
3. Créer les `Spell` et le `Monster` en `auto`, lier le pivot.
4. Relire **le paquet**, pas quatre fiches orphelines.

Plus tard : réutiliser un sort `playable` déjà collé (« même crachat que le Bouftou ») au lieu d’en créer un. En v1, créer les 2–3 sorts dans le même JSON suffit.

Gabarits : `private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md` (PV, dégâts, CA par palier, archétypes, boss).

UX : un geste **« Générer cette rencontre »**, pas « générer un monstre » puis « générer ses sorts ».

## Sorts de classe (hors créature)

Réécriture JDR d’un sort Dofus `raw` :

- garder l’identité (classe, élément, fantasy) ;
- 1 effet principal + 0–2 secondaires jouables à table ;
- PA / portée / dés dans les grilles existantes ;
- pas de nouveaux types d’effets hors catalogue.

À faire **au fil de l’eau** (PNJ ou perso), pas un batch de tout le grimoire Dofus. Le mapping d’effets scrap (`SpellEffectsConversionService`, `dofusdb_effect_mappings`) reste la conversion brute ; l’IA propose un `auto` par-dessus.

## PNJ

Le modèle existe déjà (`Npc` + `Creature` + `breed_id` + `specialization_id` + panoplies) mais le gameplay PNJ (stuff porté, sorts connus comme kit) n’est pas abouti. La génération peut **forcer** à préciser ce schéma.

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

Laravel complète gabarit + listes. Le modèle choisit dans les listes et rédige nom / story / comportement.
