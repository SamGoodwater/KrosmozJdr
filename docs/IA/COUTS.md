# Coûts et choix de modèles

Ordres de grandeur, tarifs publics **août 2026**. À revérifier sur les pages fournisseurs avant un run réel.

## Modèles visés

| Modèle | Entrée / MTok | Cache hit | Sortie / MTok | Usage prévu |
| --- | --- | --- | --- | --- |
| **Claude Sonnet 5** | 2 $ | 0,20 $ | 10 $ | Défaut production |
| Claude Haiku 4.5 | 1 $ | 0,10 $ | 5 $ | Tri / extraction simple, pas le design |
| Claude Opus 5 | 5 $ | 0,50 $ | 25 $ | Échecs du validateur, premiers étalons |
| GPT-4.1 | 2 $ | 0,50 $ | 8 $ | Alternative, A/B sur 20 fiches or |
| GPT-5 | 1,25 $ | 0,13 $ | 10 $ | Alternative, entrée un peu moins chère |
| GPT-4.1 mini | 0,40 $ | 0,10 $ | 1,60 $ | Trop juste pour du design JDR |

Sonnet 5 : le $2 / $10 annoncé comme introductif jusqu’au 31 août 2026 a été **pérennisé** (plus de hausse au 1er septembre).

Batch API (run de nuit, pas de temps réel) : environ **−50 %**.

## Hypothèse d’un appel « Laravel assemble »

Préfixe stable (superviseur + schéma + 8 étalons + extrait de normes) : ~16 k tokens, **caché** après le premier appel.

Par fiche : ~5–8 k tokens dynamiques (source + voisins ou catalogue préfiltré) ; sortie ~1,2–4 k selon le type ; ~25 % de seconde passe.

### Après échauffement du cache (Sonnet 5)

| Paquet | Ordre de grandeur |
| --- | --- |
| Objet (si passe LLM) | ~0,03 $ |
| Sort de classe | ~0,05 $ |
| Monstre seul | ~0,07 $ |
| **Rencontre** (monstre + 2–3 sorts) | ~0,08–0,12 $ |
| PNJ, listes préchargées | ~0,10 $ |
| PNJ **agent** (plusieurs allers-retours API) | ~0,25–0,50 $ |

### Scénarios projet

| Scénario | Sonnet, contexte assemblé | LLM qui « browse » l’API |
| --- | --- | --- |
| Grille objets **par algo** | ~0 $ LLM | — |
| 50 objets flavour LLM | ~2 $ | inutile |
| 20 rencontres de scénario | ~2 $ | ~8–15 $ |
| 30 PNJ + 20 monstres + 10 sorts / mois | souvent **&lt; 10 $** | 3–8× plus |
| Catalogue Dofus entier en LLM | 150–300 $ (et **hors stratégie**) | 700–1 500 $ |

L’essentiel du budget n’est pas la prod à la demande : c’est **l’itération des prompts** (regénérer 50 fiches dix fois). Prévoir 50–150 $ de tests au cadrage.

## Batch vs cache

Le préfixe (règles, exemples, schéma) est déjà amorti par le **prompt cache** : enchaîner « 1 rencontre, 1 rencontre » reste cheap.

Empiler plusieurs monstres dans **une** réponse ne fait presque pas d’économie et dégrade la qualité. Pour un usage « 1 monstre de scénario », le batch multi-entités ne se pose pas.

Agent avec 4–8 appels d’API dans la conversation : à réserver à un cas exploratoire, pas au remplissage de catalogue.
