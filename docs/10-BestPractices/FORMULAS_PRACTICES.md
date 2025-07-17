# Formules dynamiques — Bonnes pratiques

- Toujours utiliser la syntaxe définie dans [CONTENT_OVERVIEW.md – section 5](../30-Content/CONTENT_OVERVIEW.md#5-syntaxe-des-formules-krosmoz-jdr).
- Le parser de formules est développé sur-mesure pour la syntaxe métier (accolades, crochets, opérateurs, fonctions, conditions, min/max, etc.).
- Valider la syntaxe et la cohérence des formules lors de l’enregistrement ou de la modification.
- Documenter l’usage des formules dans le code (docblocks, exemples).
- Ajouter des tests unitaires pour le parsing et l’évaluation des formules.
- Les formules permettent de rendre les propriétés dynamiques et évolutives. 