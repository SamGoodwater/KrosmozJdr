<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/app/Services/Scrapping/Core/Integration/IntegrationService.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      'ade05b2e9693be9bd6320fb712493014' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '2c1e4fe75cc4cd9d1b8b0f84b7552a61' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '16d38da29ac441043b3e8ca66f3f13f1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrate',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a822a8dd310741faef6bf86eaca5b7cf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'wouldReplaceExisting',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3cfcf37ec4d39549f9d2693933564efd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateMonster',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4b3788dc3954bad706fd4f8fd9abfd56' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'mapCreatureAttributes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '86060f5cc867cc785537b78d262f013f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'sizeStringToInt',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ca9bff599c320623d65ee2f163d762c1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'filterExcludedFromUpdate',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '50aef8f72a65a514a3edce321d07571e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'filterByWhitelist',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ec84633a770de7f33ccb77af5f165187' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'attachImageFromUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0fb1d8fff3c8f987a96b2d8554c3f466' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'resolveMonsterRaceId',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'abbaa4a75b6feaf6cdede4d8e3513543' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateSpell',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4c1c93ce316b059d7389804d9b944887' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'buildSpellPoMinMax',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '6ba215f3091359268ad3f8ee743afdeb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateSpellEffectsForSpell',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '29e5296ba263ebd89a47ba29ace9a6d8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'makeUniqueEffectDegreeSlug',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4ae6e2d26ef0ef5c5b208842e96a0628' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'simulateSpellEffects',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '33e383badcdafa84919e788fe571c4ea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'collectSubEffectIdsFromSpellPayload',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'd2fc9e67cfe19f361bc4abc890778543' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'normalizeSubEffectsRowsForSignature',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3369191bae90816575b918dda744e707' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'computeEffectConfigSignature',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '79e2c965c7abefa9f522a8427bde3f31' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'rebuildConfigSignatureForEffectDegree',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '15a2617502a11417c8d42093ffe3b787' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'effectSubEffectDedupKey',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '6530d448de2dbe0c235400a8eaae51ca' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'effectSubEffectDedupWhere',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7c44c4699419c7f875abdc84dabeba48' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateConditionFromParams',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'cf2fdb75ca3fa1b6ff3720824af4d373' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'inferSpellElementMaskFromEffectsPayload',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'f1491f1a9fb5891c9b18a0d2bac24c53' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'inferSpellTypeIdsFromEffectsPayload',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '6568cea8a325a14e2600095c14645ab3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'normalizeTextKey',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'b6f41baeabd4f0ae0ef053ab130a958e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateBreed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c543f996f2599e8954087174f2f5c335' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integrateItem',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c573791bf4f13a77e95c39d032af58c9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'syncResourceRecipe',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'f227b143ae0e1a6072daf2e5bd171dc6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'getItemTargetTableFromRaw',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '48b9ec3cf8be8051c43deb6f9a3df3a7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'getItemTargetTable',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4486c8f796e1032f734d3f2157b45c55' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'resolveItemTargetTable',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '45992f9e5075c1197db553cd798254a2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'integratePanoply',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0052f9e54aa08fc877cd4f0468ba8fea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'getExistingAttributesForComparison',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '157ba1c5af7c9b059e641487ecad702d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'resolveItemEntityType',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a539af740ee4cbab20e1ec598fecaec8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'getSystemUserId',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4eb2938ec285f3496fa046544de5397b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
         'uses' => 
        array (
          'effect' => 'App\\Models\\Effect',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'effectsubeffect' => 'App\\Models\\EffectSubEffect',
          'effectusage' => 'App\\Models\\EffectUsage',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'creature' => 'App\\Models\\Entity\\Creature',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'condition' => 'App\\Models\\Entity\\Condition',
          'subeffect' => 'App\\Models\\SubEffect',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
          'elementbitmask' => 'App\\Support\\ElementBitmask',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
         'functionName' => 'localizedToString',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
           'uses' => 
          array (
            'effect' => 'App\\Models\\Effect',
            'effectdegree' => 'App\\Models\\EffectDegree',
            'effectsubeffect' => 'App\\Models\\EffectSubEffect',
            'effectusage' => 'App\\Models\\EffectUsage',
            'breed' => 'App\\Models\\Entity\\Breed',
            'consumable' => 'App\\Models\\Entity\\Consumable',
            'creature' => 'App\\Models\\Entity\\Creature',
            'item' => 'App\\Models\\Entity\\Item',
            'monster' => 'App\\Models\\Entity\\Monster',
            'panoply' => 'App\\Models\\Entity\\Panoply',
            'resource' => 'App\\Models\\Entity\\Resource',
            'spell' => 'App\\Models\\Entity\\Spell',
            'condition' => 'App\\Models\\Entity\\Condition',
            'subeffect' => 'App\\Models\\SubEffect',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'dofusdbelementid' => 'App\\Support\\DofusDbElementId',
            'elementbitmask' => 'App\\Support\\ElementBitmask',
            'auth' => 'Illuminate\\Support\\Facades\\Auth',
            'db' => 'Illuminate\\Support\\Facades\\DB',
            'log' => 'Illuminate\\Support\\Facades\\Log',
          ),
           'className' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      '/var/www/KrosmozJdr/app/Services/Scrapping/Core/Integration/IntegrationService.php' => '964d6901048a3eaa07098aed14b60334d39d0b131c2e6d711d4a9ddb1ded4a55',
    ),
  ),
));