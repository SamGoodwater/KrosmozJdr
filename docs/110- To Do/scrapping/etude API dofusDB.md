# Effects
Système présents seulement dans DofusDB.

| propDofusDB    | description | format |
| -------------- | ----------- | ------ |
| caterogie      |             | int    |
| characteristic |             | int    |
| effectId       |             | int    |
| elementId      |             | int    |
| from           |             | int    |
| to             |             | int    |

# Ressources
Url : 
- liste : 
- unitaire : 

# Équipements

# Consommables
Url : 
- liste :
	-   https://api.dofusdb.fr/items?typeId[$ne]=203&$sort=-id&typeId[$in][]=76&level[$gte]=0&level[$lte]=200&$skip=0&lang=fr
- unitaire : https://api.dofusdb.fr/items/IDDOFUSDB?lang=fr
Entité correspondante : consommable

Les consommables sont récupérables via : type.superType.id ou type.superTypeId = 6 ou type.superType.name.fr = "Consommable"

| prop DofusDB   | prop Krosmoz | fc de conversion | Commentaire                             |
| -------------- | ------------ | ---------------- | --------------------------------------- |
| description.fr | description  | -                |                                         |
| effects.[]     |              |                  | liste multiple d'object Effects DofusDB |
| img            | image        | img              |                                         |
| level          | level        | level            |                                         |
| name.fr        | name         |                  |                                         |
| price          | price        | price            |                                         |
|                | type         |                  | issu de consumable_type.id              |
|                |              |                  |                                         |
### Type des consumables

Table : consumable_type

| props DofusDB | prop Krosmoz | dc de conversion | commentaire |
| ------------- | ------------ | ---------------- | ----------- |
| type.name.fr  | name         |                  |             |
| -             | id           |                  |             |
|               |              |                  |             |
# Monstres

# Sorts

# Classes

# Fonctions de Conversions

## Image
Verification du poids et du format sinon conversion webp et réduire la taille.
## Level
Level Krosmoz = Level Dofus / 10

## Price