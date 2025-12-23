<?php

/**
 * Registry des permissions par entité.
 *
 * @description
 * Mappe les `entityType` utilisés côté frontend (ex: 'items', 'resources', 'resource-types')
 * vers les classes Eloquent correspondantes. Ce fichier sert de source de vérité
 * pour exposer au front des permissions globales (create/updateAny/deleteAny/viewAny...).
 *
 * NB: Les permissions "par instance" restent exposées via les API Resources (champ `can`).
 */

use App\Models\Entity\Attribute;
use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Classe;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Entity\Spell;
use App\Models\Entity\Specialization;
use App\Models\Type\ResourceType;
use App\Models\User;
use App\Models\Page;

return [
    /**
     * Entités "core" (EntityTable entity-type)
     */
    'attributes' => Attribute::class,
    'campaigns' => Campaign::class,
    'capabilities' => Capability::class,
    'classes' => Classe::class,
    'consumables' => Consumable::class,
    'creatures' => Creature::class,
    'items' => Item::class,
    'monsters' => Monster::class,
    'npcs' => Npc::class,
    'panoplies' => Panoply::class,
    'resources' => Resource::class,
    'scenarios' => Scenario::class,
    'shops' => Shop::class,
    'spells' => Spell::class,
    'specializations' => Specialization::class,

    /**
     * Types
     */
    'resource-types' => ResourceType::class,

    /**
     * Admin / contenu
     */
    'users' => User::class,
    'pages' => Page::class,
];


