<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '5ff1c731f5e8b04b2175a06b9e494c80' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '73af8993cb0c55d36cc6eacabe841979' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Concerns',
         'uses' => 
        array (
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
         'typeAliasClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
          1 => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
          2 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'b88617c39ef10ba3b0c527ea29ddc82f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Concerns',
         'uses' => 
        array (
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'guardDevelopmentOnly',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Concerns',
           'uses' => 
          array (
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
           'typeAliasClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
          1 => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
          2 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '8e7dff6dd409a45f8c3bd72093b5aa87' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Concerns',
         'uses' => 
        array (
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'guardNotProduction',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Concerns',
           'uses' => 
          array (
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
           'typeAliasClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
          1 => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
          2 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '9d8c25e19e9a936bfd386793282fc4f4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '58b41362e9f34fc892d04b13357f7470' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'handle',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '020c43acc2239fe3d3c9c57bf5428fa5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'getFilesToExportForCurrentRun',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      'a615d0f91905f3006b446bd2791e58f3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'createBackupZip',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      'e6109855763d6e42171e1778604ea8e2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'cleanupOldBackups',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '2dffc4e92f04fa64cf8a59d645c5fd3c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportCharacteristics',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '9f8a039425f1cb194a8a0b878a688029' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportConversionFormulasInGroups',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '67935b203897f5febd287f2a234e3f45' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportSpellEffectTypes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '8f6b57d5461455265af854312e8f9798' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportItemTypes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      'ce2bdd94329d8c9a2e0b4cfddcdce7ed' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportItemTypesTable',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '53ecc53465acd1ddeb4f9daa0ec79fb9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'exportScrappingMappings',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '534d99de91fea261b34fb349397f50e2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
          'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
          'spelleffecttype' => 'App\\Models\\SpellEffectType',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
          'command' => 'Illuminate\\Console\\Command',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
          'ziparchive' => 'ZipArchive',
        ),
         'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
         'functionName' => 'varExportShort',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Console\\Commands\\Scrapping',
           'uses' => 
          array (
            'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
            'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
            'scrappingentitymapping' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
            'scrappingentitymappingtarget' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
            'spelleffecttype' => 'App\\Models\\SpellEffectType',
            'consumabletype' => 'App\\Models\\Type\\ConsumableType',
            'itemtype' => 'App\\Models\\Type\\ItemType',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'characteristicdefinitionsexportfromdatabaseservice' => 'App\\Services\\Characteristics\\CharacteristicDefinitionsExportFromDatabaseService',
            'command' => 'Illuminate\\Console\\Command',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'file' => 'Illuminate\\Support\\Facades\\File',
            'schema' => 'Illuminate\\Support\\Facades\\Schema',
            'ziparchive' => 'ZipArchive',
          ),
           'className' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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
      '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php' => 'e201c2422c93e444008c3bb0c01f664744b4e0b41fb8b1032fb2a7a2ae40a037',
      '/var/www/KrosmozJdr/app/Console/Concerns/GuardsProductionEnvironment.php' => '87ef059d5e30de0d572a63a6449fc018a5c2fccfc0746c36a1d9bda76d26bff3',
    ),
  ),
));