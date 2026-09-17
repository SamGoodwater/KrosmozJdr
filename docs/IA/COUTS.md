# Coûts et choix de modèles

Ordres de grandeur, tarifs publics **septembre 2026**. À revérifier sur les pages fournisseurs avant un run réel.

Le modèle se choisit dans **IA métier** (`generation.model`). Défaut : **Haiku 4.5**. Allowlist : Haiku / Sonnet / Opus (`AnthropicModelCatalog`).

## Modèles visés

| Modèle | Entrée / MTok | Cache hit | Sortie / MTok | Usage prévu |
| --- | --- | --- | --- | --- |
| **Claude Haiku 4.5** | 1 $ | 0,10 $ | 5 $ | **Défaut production** (moins cher, JSON outillé) |
| Claude Sonnet 5 | 2 $ | 0,20 $ | 10 $ | Design JDR plus soigné |
| Claude Opus 5 | 5 $ | 0,50 $ | 25 $ | Échecs du validateur, premiers étalons |
| GPT-4.1 | 2 $ | 0,50 $ | 8 $ | Alternative, A/B sur 20 fiches or (non branché) |
| GPT-5 | 1,25 $ | 0,13 $ | 10 $ | Alternative, entrée un peu moins chère (non branché) |
| GPT-4.1 mini | 0,40 $ | 0,10 $ | 1,60 $ | Trop juste pour du design JDR |

Sonnet 5 : le $2 / $10 annoncé comme introductif jusqu’au 31 août 2026 a été **pérennisé**.

Batch API (run de nuit, pas de temps réel) : environ **−50 %**. Non utilisé pour l’UI.

## Cache prompt

Marqueur `cache_control: ephemeral` (TTL 5 min) sur le **dernier bloc stable** : outil `submit_json` + préfixe user (tâche, gel, étalons). La fiche source, le brief et les retries restent hors cache.

Le cache automatique top-level n’est **pas** utilisé : il poserait le point de rupture sur le message qui change à chaque fiche, donc aucun hit.

Seuil fournisseur : **1 024** tokens (Sonnet 5), **4 096** (Haiku 4.5), **512** (Opus 5). En dessous, Anthropic ignore le marqueur (pas d’erreur). Toggle admin `generation.prompt_cache`, défaut **on**.

Écriture cache : 1,25× le prix d’entrée (5 min). Lecture : 0,1×.

## Hypothèse d’un appel « Laravel assemble »

Préfixe stable (superviseur + schéma + 8 étalons + extrait de normes) : ~16 k tokens, **caché** après le premier appel.

Par fiche : ~5–8 k tokens dynamiques (source + voisins ou catalogue préfiltré) ; sortie ~1,2–4 k selon le type ; ~25 % de seconde passe.

### Après échauffement du cache (Haiku 4.5, ~½ Sonnet)

| Paquet | Ordre de grandeur |
| --- | --- |
| Objet (si passe LLM) | ~0,02 $ |
| Sort de classe | ~0,03 $ |
| Monstre seul | ~0,04 $ |
| **Rencontre** (monstre + 2–3 sorts) | ~0,04–0,06 $ |
| PNJ, listes préchargées | ~0,05 $ |
| PNJ **agent** (plusieurs allers-retours API) | ~0,12–0,25 $ |

Les estimés de la page admin sont recalculés selon le modèle choisi.

### Scénarios projet

| Scénario | Haiku, contexte assemblé | LLM qui « browse » l’API |
| --- | --- | --- |
| Grille objets **par algo** | ~0 $ LLM | — |
| 50 objets flavour LLM | ~1 $ | inutile |
| 20 rencontres de scénario | ~1 $ | ~8–15 $ |
| 30 PNJ + 20 monstres + 10 sorts / mois | souvent **&lt; 5 $** | 3–8× plus |
| Catalogue Dofus entier en LLM | 80–150 $ (et **hors stratégie**) | 700–1 500 $ |

L’essentiel du budget n’est pas la prod à la demande : c’est **l’itération des prompts** (regénérer 50 fiches dix fois). Prévoir 50–150 $ de tests au cadrage.

## Batch vs cache

Le préfixe (règles, exemples, schéma) est déjà amorti par le **prompt cache** : enchaîner « 1 rencontre, 1 rencontre » reste cheap.

Empiler plusieurs monstres dans **une** réponse ne fait presque pas d’économie et dégrade la qualité. Pour un usage « 1 monstre de scénario », le batch multi-entités ne se pose pas.

Agent avec 4–8 appels d’API dans la conversation : à réserver à un cas exploratoire, pas au remplissage de catalogue.
