# Catalogue objets et pré-filtre

Les objets sont le **socle** : sans un petit référentiel `playable`, PNJ et rencontres n’ont rien de cohérent à porter. Ce n’est **pas** un import massif de tout Dofus.

Commande : `php artisan ia:equipment-grid` (rapport). `--write` crée les **trous** en `draft` (`official_id` `ia-grid:slot:voie:niveau`), jamais `playable`. Config : `resources/ia/equipment-grid.json`. Les fiches Dofus ne sont pas réécrites.

## Pourquoi pas tout Dofus

En JDR, un type d’équipement n’a que **3 ou 4 caractéristiques possibles**. Après conversion, des dizaines d’items Dofus **collapsent** vers la même signature (type + niveau + bonus).

Le code sait déjà :

- filtrer les bonus incompatibles avec le type (`allowed_item_type_ids`, `ItemEffectsToBonusConverter`) ;
- snaper aux normes (`NormsResolver`) ;
- hasher une signature (`DuplicateEquipmentSignatureChecker`).

Un quota « une dizaine d’objets par niveau » est le bon **ordre de grandeur** s’il décrit une **couverture**, pas 10 items tirés au hasard.

## Grille cible

Remplir les cases **niveau × slot × voie** (Terre / Feu / Eau / Air, parfois Neutre), pas un tas Dofus.

| Idée | Volume |
| --- | --- |
| ~8–15 objets par niveau (slots utiles × voies) | **200–400** `playable` pour tout le jeu |
| 10 par niveau × 30 | ~300, cohérent |
| 10 par *type* et par niveau | encore trop |

Référence d’équilibrage : `private/game/rules/5-Ressources-et-equilibrage/5.2-principes-d-equilibrage/5.2.4-equipements-et-panoplies.md`.

## Pipeline objets (surtout algorithmique)

1. Convertir Dofus (pipeline scrap actuel).
2. Classer dans la grille (niveau clampé 1–20 × slot × voie). La voie se lit sur `effect` (JSON converti) ou, à défaut, sur le tableau d’effets Dofus brut encore stocké dans `bonus` (Force / Int / Chance / Agi, dégâts élémentaires).
3. Pour chaque case, **garder un représentant** sans le modifier (identité Dofus inchangée). En cas de doublon : source Dofus, puis état `playable` > `draft` > `auto` > `raw`.
4. Trou : `ia:equipment-grid --write` crée un objet **sans** `dofusdb_id`, état `draft`, bonus de palier (règle 5.2.4 : 1 / 2 / 3 / 4 selon la bande de niveaux). Accessoires et armures : carac de voie. Armes : dégâts élémentaires. Relancer `--write` est idempotent.

Le filtre « 3–4 caracs autorisées du type » (`item_type_dofus_ids`, aujourd’hui souvent **cape seule** pour Force) s’applique au scrap, pas à la classification de grille : un anneau Dofus avec de la Force reste un candidat Terre, même si la conversion JDR a droppé le bonus. Les trous générés portent volontairement la carac de voie (à relire).

L’IA **ne réécrit pas** le nom, la description ni les caracs d’un item Dofus (gel admin / JSON). Elle n’intervient que pour un **unique de scénario** (sans source) ou un cas que l’algo ne tranche pas. Un snap algo peut aller en revue humaine légère ; `auto` n’est obligatoire que s’il y a eu une passe LLM. Ne pas publier `playable` tout seul.

## Pré-filtre pour le LLM (pas d’API agent)

Le modèle **ne reçoit pas** tout le catalogue. Laravel envoie une liste courte, par exemple pour un Iop Terre niveau 8 : `playable`, niveau 6–8, types portables, bonus dans { Force, Vitalité, dégâts Terre, éventuellement PA }.

Forme compacte (1–3 k tokens, 20–40 lignes) :

```json
[
  {
    "id": 412,
    "name": "Anneau du Bouftou",
    "type": "anneau",
    "level": 7,
    "bonuses": { "strength": 2, "vitality": 1 }
  }
]
```

Consigne : *tu ne crées pas d’objet ; tu renvoies des `id` ; un item par slot ; cohérence Terre / Force.*

Le validateur revérifie ids, niveau, slots, voie. Id inventé → retry.

Même idée pour les **sorts de classe** d’un PNJ : liste `playable` préfiltrée (classe, élément, niveau ≤ N).

## API catalogue (quand on la fera)

Besoin réel : recherche **côté Laravel** (et plus tard UI MJ), pas un agent qui lit une doc d’API.

- Uniquement `playable`.
- Payload compact (pas l’arbre d’effets ni les colonnes TanStack).
- Filtres objets : type, niveau min/max, caractéristique de bonus, élément / voie, rareté.
- Filtres sorts : classe, élément, niveau, rôle (dégâts, soin, contrôle…).
- Auth service / admin, pas les APIs de table actuelles.

Les routes caractéristiques existent déjà (`/api/characteristics`, normes, table de référence). Il faudra une **vue compacte** pour le prompt, pas un dump du référentiel.

## Étalons manuels

Une vingtaine de fiches `playable` **par type** au moment où l’IA touche ce type. Pour les objets, l’algo peut produire le volume ; les étalons servent surtout de few-shot **si** une passe LLM existe (flavour, uniques). Ne pas réécrire tout Dofus à la main avant de commencer.
