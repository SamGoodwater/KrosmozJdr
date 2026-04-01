<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1774957014,
	'meta' => array (
  'cacheVersion' => 'v12-linesToIgnore',
  'phpstanVersion' => '2.1.33',
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80417,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, level: 6, treatPhpDocTypesAsCertain: false, paths: [/var/www/KrosmozJdr/app/Enums/Visibility.php, /var/www/KrosmozJdr/app/Http/Controllers/PageController.php, /var/www/KrosmozJdr/app/Http/Controllers/SectionController.php, /var/www/KrosmozJdr/app/Http/Requests/StorePageRequest.php, /var/www/KrosmozJdr/app/Http/Requests/UpdatePageRequest.php, /var/www/KrosmozJdr/app/Http/Requests/StoreSectionRequest.php, /var/www/KrosmozJdr/app/Http/Requests/UpdateSectionRequest.php, /var/www/KrosmozJdr/app/Http/Requests/StoreFileRequest.php, /var/www/KrosmozJdr/app/Http/Resources/PageResource.php, /var/www/KrosmozJdr/app/Http/Resources/SectionResource.php, /var/www/KrosmozJdr/app/Http/Resources/UserLightResource.php, /var/www/KrosmozJdr/app/Models/Page.php, /var/www/KrosmozJdr/app/Models/Section.php, /var/www/KrosmozJdr/app/Policies/PagePolicy.php, /var/www/KrosmozJdr/app/Policies/SectionPolicy.php, /var/www/KrosmozJdr/app/Services/SectionService.php], tmpDir: /var/www/KrosmozJdr/storage/phpstan}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\Extension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}}}',
  'analysedPaths' => 
  array (
    0 => '/var/www/KrosmozJdr/app/Services/Creature',
    1 => '/var/www/KrosmozJdr/app/Services/Characteristic/Formula/FormulaResolutionService.php',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    '/var/www/KrosmozJdr/composer.lock' => 'f73ab9da4830bb1afbb2c568af8ff430b7ecafd9',
  ),
  'composerInstalled' => 
  array (
    '/var/www/KrosmozJdr/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'archtechx/enums' => 
        array (
          'pretty_version' => 'v1.1.2',
          'version' => '1.1.2.0',
          'reference' => '81375b71c176f680880a95e7448d84258cfb5c72',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../archtechx/enums',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'barryvdh/laravel-debugbar' => 
        array (
          'pretty_version' => 'v3.16.0',
          'version' => '3.16.0.0',
          'reference' => 'f265cf5e38577d42311f1a90d619bcd3740bea23',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-debugbar',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'barryvdh/laravel-dompdf' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'reference' => '8e71b99fc53bb8eb77f316c3c452dd74ab7cb25d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-dompdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'barryvdh/laravel-ide-helper' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '8d00250cba25728373e92c1d8dcebcbf64623d29',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-ide-helper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'barryvdh/reflection-docblock' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'd103774cbe7e94ddee7e4870f97f727b43fe7201',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.13.1',
          'version' => '0.13.1.0',
          'reference' => 'fc7ed316430118cc7836bf45faff18d5dfc8de04',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'calcinai/php-imagick' => 
        array (
          'pretty_version' => 'v0.1.2',
          'version' => '0.1.2.0',
          'reference' => '001530b19560b9862ffe78c3ae29ad5dc2549e6d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../calcinai/php-imagick',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/class-map-generator' => 
        array (
          'pretty_version' => '1.6.1',
          'version' => '1.6.1.0',
          'reference' => '134b705ddb0025d397d8318a75825fe3c9d1da34',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/./class-map-generator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/pcre' => 
        array (
          'pretty_version' => '3.3.2',
          'version' => '3.3.2.0',
          'reference' => 'b2bed4734f0cc156ee1fe9c0da2550420d99a21e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/./pcre',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/semver' => 
        array (
          'pretty_version' => '3.4.3',
          'version' => '3.4.3.0',
          'reference' => '4313d26ada5e0c4edfbd1dc481a92ff7bff91f12',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/./semver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.0.10',
          'version' => '2.0.10.0',
          'reference' => '5817d0659c5b50c9b950feb9af7b9668e2c436bc',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/dompdf' => 
        array (
          'pretty_version' => 'v3.1.4',
          'version' => '3.1.4.0',
          'reference' => 'db712c90c5b9868df3600e64e68da62e78a34623',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/dompdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-font-lib' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'reference' => '6137b7d4232b7f16c882c75e4ca3991dbcf6fe2d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/php-font-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-svg-lib' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'eb045e518185298eb6ff8d80d0d0c6b17aecd9af',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/php-svg-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragon-code/contracts' => 
        array (
          'pretty_version' => '2.24.0',
          'version' => '2.24.0.0',
          'reference' => 'c21ea4fc0a399bd803a2805a7f2c989749083896',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dragon-code/contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragon-code/pretty-array' => 
        array (
          'pretty_version' => '4.2.0',
          'version' => '4.2.0.0',
          'reference' => 'b94034d92172a5d14a578822d68b2a8f8b5388e0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dragon-code/pretty-array',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragon-code/support' => 
        array (
          'pretty_version' => '6.16.0',
          'version' => '6.16.0.0',
          'reference' => 'ab9b657a307e75f6ba5b2b39e1e45207dc1a065a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dragon-code/support',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.4.0',
          'version' => '3.4.0.0',
          'reference' => '8c784d071debd117328803d86b2097615b457500',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ezyang/htmlpurifier' => 
        array (
          'pretty_version' => 'v4.19.0',
          'version' => '4.19.0.0',
          'reference' => 'b287d2a16aceffbf6e0295559b39662612b77fcf',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../ezyang/htmlpurifier',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.3',
          'version' => '2.18.3.0',
          'reference' => '59a123a3d459c5a23055802237cb317f609867e5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'firebase/php-jwt' => 
        array (
          'pretty_version' => 'v7.0.3',
          'version' => '7.0.3.0',
          'reference' => '28aa0694bcfdfa5e2959c394d5a1ee7a5083629e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../firebase/php-jwt',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.3.0',
          'version' => '1.3.0.0',
          'reference' => '3d158f36e7875e2f040f37bc0573956240a5a38b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.3',
          'version' => '1.1.3.0',
          'reference' => '3ba905c11371512af9d9bdd27d99b782216b6945',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.9.3',
          'version' => '7.9.3.0',
          'reference' => '7b2f29fe81dc4da0ca0ea7d42107a0845946ea77',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '7c69f28996b0a6920945dd20b3857e499d9ca96c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.7.1',
          'version' => '2.7.1.0',
          'reference' => 'c2270caaabe631b3b44c85f99e5a04bbb8060d16',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.4',
          'version' => '1.0.4.0',
          'reference' => '30e286560c137526eccd4ce21b2de477ab0676d2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.6',
          'version' => '0.6.0.0',
          'reference' => '947083e2dca211a6f12fb1beb67a01e387de9b62',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.22.0',
          ),
        ),
        'inertiajs/inertia-laravel' => 
        array (
          'pretty_version' => 'v2.0.4',
          'version' => '2.0.4.0',
          'reference' => 'bab0c0c992aa36e63d800903288d490d6b774d97',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'intervention/gif' => 
        array (
          'pretty_version' => '4.2.2',
          'version' => '4.2.2.0',
          'reference' => '5999eac6a39aa760fb803bc809e8909ee67b451a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../intervention/gif',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'intervention/image' => 
        array (
          'pretty_version' => '3.11.4',
          'version' => '3.11.4.0',
          'reference' => '8c49eb21a6d2572532d1bc425964264f3e496846',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../intervention/image',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.8.0',
          'version' => '3.8.0.0',
          'reference' => 'd13ef96d652d1b2a8f34f1760ba6bf5b9c98112e',
          'type' => 'phpstan-extension',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel-lang/actions' => 
        array (
          'pretty_version' => '1.10.2',
          'version' => '1.10.2.0',
          'reference' => '2fd80501e849b84a795f3325d61a44b543fe8d41',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/actions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/attributes' => 
        array (
          'pretty_version' => '2.13.5',
          'version' => '2.13.5.0',
          'reference' => '0f7a0c195cbd4eb000b14dc3fd233dacd23a10f7',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/attributes',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/common' => 
        array (
          'pretty_version' => '6.7.1',
          'version' => '6.7.1.0',
          'reference' => '8deaf311213d696cbd3cc41ac23705af39a187ff',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/config' => 
        array (
          'pretty_version' => '1.14.0',
          'version' => '1.14.0.0',
          'reference' => '0f6a41a1d5f4bde6ff59fbfd9e349ac64b737c69',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/http-statuses' => 
        array (
          'pretty_version' => '3.10.4',
          'version' => '3.10.4.0',
          'reference' => '97fdb638f95bf9ca8131cc887750d70980aa7be3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/http-statuses',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/json-fallback' => 
        array (
          'pretty_version' => '2.2.1',
          'version' => '2.2.1.0',
          'reference' => '0172de25e6cc3c5b26a7c8b778ff4e37f4d913f4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/json-fallback',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/lang' => 
        array (
          'pretty_version' => '15.22.6',
          'version' => '15.22.6.0',
          'reference' => 'ae837229304b25eb19c0fbfc0aa9b772b8110a97',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/lang',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/locale-list' => 
        array (
          'pretty_version' => '1.5.1',
          'version' => '1.5.1.0',
          'reference' => '060475db218a97a54612163112b44782024d79c1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/locale-list',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/locales' => 
        array (
          'pretty_version' => '2.10.0',
          'version' => '2.10.0.0',
          'reference' => '8c1d2383ced70a919b3c2f430589be6c81663087',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/locales',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/models' => 
        array (
          'pretty_version' => '1.3.1',
          'version' => '1.3.1.0',
          'reference' => 'a54368960a763a1f9a3bfc8056f54752431aa12b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/models',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/moonshine' => 
        array (
          'pretty_version' => '1.3.8',
          'version' => '1.3.8.0',
          'reference' => 'a4bf8eeef0e44ed9fb596379e9ec8627c401e6cb',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/moonshine',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-country-names' => 
        array (
          'pretty_version' => '1.5.1',
          'version' => '1.5.1.0',
          'reference' => '70eba5c3b7a4035da085123a81c076e52367b30c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-country-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-currency-names' => 
        array (
          'pretty_version' => '1.6.1',
          'version' => '1.6.1.0',
          'reference' => 'b376c69778be7184f7292cb92424ac7d7415d55d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-currency-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-locale-names' => 
        array (
          'pretty_version' => '2.5.1',
          'version' => '2.5.1.0',
          'reference' => '38278ef43fb3460c9fb7a056ac2e8043e4faa963',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-locale-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/publisher' => 
        array (
          'pretty_version' => '16.7.0',
          'version' => '16.7.0.0',
          'reference' => 'af47a0807f753818d72808250a1166e98172047c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/publisher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/routes' => 
        array (
          'pretty_version' => '1.9.1',
          'version' => '1.9.1.0',
          'reference' => 'ed87d81fff3b79048abcb533e0f39a52bc030ffb',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/routes',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/starter-kits' => 
        array (
          'pretty_version' => '1.3.10',
          'version' => '1.3.10.0',
          'reference' => '90260cfad7436ef11802eeb7adf18485905d8cf0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/starter-kits',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/breeze' => 
        array (
          'pretty_version' => 'v2.3.8',
          'version' => '2.3.8.0',
          'reference' => '1a29c5792818bd4cddf70b5f743a227e02fbcfcd',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/breeze',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v12.22.0',
          'version' => '12.22.0.0',
          'reference' => '6ab00c913ef6ec6fad0bd506f7452c0bb9e792c3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.3',
          'version' => '1.2.3.0',
          'reference' => '8cc3d575c1f0e57eeb923f366a37528c50d2385a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.24.0',
          'version' => '1.24.0.0',
          'reference' => '0345f3b05f136801af8c339f9d16ef29e6b4df8a',
          'type' => 'project',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.6',
          'version' => '0.3.6.0',
          'reference' => '86a8b692e8661d0fb308cec64f3d176821323077',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sail' => 
        array (
          'pretty_version' => 'v1.44.0',
          'version' => '1.44.0.0',
          'reference' => 'a09097bd2a8a38e23ac472fa6a6cf5b0d1c1d3fe',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/sail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/sanctum' => 
        array (
          'pretty_version' => 'v4.2.0',
          'version' => '4.2.0.0',
          'reference' => 'fd6df4f79f48a72992e8d29a9c0ee25422a0d677',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/sanctum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.4',
          'version' => '2.0.4.0',
          'reference' => 'b352cf0534aa1ae6b4d825d1e762e35d43f8a841',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/socialite' => 
        array (
          'pretty_version' => 'v5.25.0',
          'version' => '5.25.0.0',
          'reference' => '231f572e1a37c9ca1fb8085e9fb8608285beafb3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/socialite',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v2.10.1',
          'version' => '2.10.1.0',
          'reference' => '22177cc71807d38f2810c6204d8f7183d88a57d3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.7.1',
          'version' => '2.7.1.0',
          'reference' => '10732241927d3971d28e7ea7b5712721fa2296ca',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.30.0',
          'version' => '3.30.0.0',
          'reference' => '2203e3151755d874bb2943649dae1eb8533ac93e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.30.0',
          'version' => '3.30.0.0',
          'reference' => '6691915f77c7fb69adfb87dcd550052dc184ee10',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.16.0',
          'version' => '1.16.0.0',
          'reference' => '2d6702ff215bf922936ccc1ad31007edc76451b9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/oauth1-client' => 
        array (
          'pretty_version' => 'v1.11.0',
          'version' => '1.11.0.0',
          'reference' => 'f9c94b088837eb1aae1ad7c4f23eb65cc6993055',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/oauth1-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.5.1',
          'version' => '7.5.1.0',
          'reference' => '81fb5145d2644324614cc532b28efd0215bda430',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.5.0',
          'version' => '7.5.0.0',
          'reference' => '08cfc6c4f3d811584fb09c37e2849e6a7f9b0742',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'maennchen/zipstream-php' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '682f1098a8fddbaf43edac2306a691c7ad508ec5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../maennchen/zipstream-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'masterminds/html5' => 
        array (
          'pretty_version' => '2.10.0',
          'version' => '2.10.0.0',
          'reference' => 'fcf91eb64359852f00d921887b219479b4f21251',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../masterminds/html5',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'maximebf/debugbar' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => 'v2.2.4',
          ),
        ),
        'mews/purifier' => 
        array (
          'pretty_version' => '3.4.3',
          'version' => '3.4.3.0',
          'reference' => 'acc71bc512dcf9b87144546d0e3055fc76d244ff',
          'type' => 'package',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../mews/purifier',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.9.0',
          'version' => '3.9.0.0',
          'reference' => '10d85740180ecba7896c87e06a166e0c95a0e3b6',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.10.2',
          'version' => '3.10.2.0',
          'reference' => '76b5c07b8a9d2025ed1610e14cef1f3fd6ad2c24',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.2',
          'version' => '1.3.2.0',
          'reference' => 'da801d52f0354f70a638673c4a0f04e16529431d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.0.8',
          'version' => '4.0.8.0',
          'reference' => 'c930ca4e3cf4f17dcfb03037703679d2396d2ede',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.6.0',
          'version' => '5.6.0.0',
          'reference' => '221b0d0fdf1369c71047ad1d18bb5880017bbc56',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.8.2',
          'version' => '8.8.2.0',
          'reference' => '60207965f9b7b7a4ce15a0f75d57f9dadb105bdb',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.3.1',
          'version' => '2.3.1.0',
          'reference' => 'dfa08f390e509967a15c22493dc0bac5733d9123',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'paragonie/constant_time_encoding' => 
        array (
          'pretty_version' => 'v3.1.3',
          'version' => '3.1.3.0',
          'reference' => 'd5b01a39b3415c2cd581d3bd3a3575c1ebbd8e77',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../paragonie/constant_time_encoding',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'paragonie/random_compat' => 
        array (
          'pretty_version' => 'v9.99.100',
          'version' => '9.99.100.0',
          'reference' => '996434e5492cb4c3edcb9168db6fbb1359ef965a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../paragonie/random_compat',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'php-debugbar/php-debugbar' => 
        array (
          'pretty_version' => 'v2.2.4',
          'version' => '2.2.4.0',
          'reference' => '3146d04671f51f69ffec2a4207ac3bdcf13a9f35',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../php-debugbar/php-debugbar',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.3',
          'version' => '1.9.3.0',
          'reference' => 'e3fac8b24f56113f7cb96af14958c0dd16330f54',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpseclib/phpseclib' => 
        array (
          'pretty_version' => '3.0.49',
          'version' => '3.0.49.0',
          'reference' => '6233a1e12584754e6b5daa69fe1289b47775c1b9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpseclib/phpseclib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.1.33',
          'version' => '2.1.33.0',
          'reference' => '9e800e6bee7d5bd02784d4c6069b48032d16224f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '11.0.10',
          'version' => '11.0.10.0',
          'reference' => '1a800a7446add2d79cc6b3c01c45381810367d76',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '5.1.0',
          'version' => '5.1.0.0',
          'reference' => '118cfaaa8bc5aef3287bf315b6060b1174754af6',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '5.0.1',
          'version' => '5.0.1.0',
          'reference' => 'c1ca3814734c07492b3d4c5f794f4b0995333da2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '3e0404dc6b300e6bf56415467ebcb3fe4f33e964',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '3b415def83fbcb41f991d9ebf16ae4ad8b7837b3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '11.5.28',
          'version' => '11.5.28.0',
          'reference' => '93f30aa3889e785ac63493d4976df0ae9fdecb60',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1|2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
            1 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.10',
          'version' => '0.12.10.0',
          'reference' => '6e80abe6f2257121f1eb9a4c55bf29d921025b22',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.0',
          'version' => '4.9.0.0',
          'reference' => '4e0e23cc785f0724a0e838279a9eb03f28b092a0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.0',
          ),
        ),
        'sabberworm/php-css-parser' => 
        array (
          'pretty_version' => 'v8.9.0',
          'version' => '8.9.0.0',
          'reference' => 'd8e916507b88e389e26d4ab03c904a082aa66bb9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sabberworm/php-css-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => '15c5dd40dc4f38794d383bb95465193f5e0ae180',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '54391c61e4af8078e5b276ab082b6d3c54c9ad64',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/code-unit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/code-unit-reverse-lookup' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '183a9b2632194febd219bb9246eee421dad8d45e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/code-unit-reverse-lookup',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '6.3.1',
          'version' => '6.3.1.0',
          'reference' => '24b8fbc2c8e201bb1308e7b05148d6ab393b6959',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'ee41d384ab1906c68852636b6de493846e13e5a0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '6.0.2',
          'version' => '6.0.2.0',
          'reference' => 'b4ccd857127db5d41a5b676f24b51371d76d8544',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '7.2.1',
          'version' => '7.2.1.0',
          'reference' => 'a5c75038693ad2e8d4b6c15ba2403532647830c4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '6.3.0',
          'version' => '6.3.0.0',
          'reference' => '3473f61172093b2da7de1fb5782e1f24cc036dc3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '7.0.2',
          'version' => '7.0.2.0',
          'reference' => '3be331570a721f9a4b5917f4209773de17f747d7',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => 'd36ad0d782e5756913e42ad87cb2890f4ffe467a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => 'f5b498e631a74204185071eb41f33f38d64608aa',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => '6e1a43b411b2ad34146dee7524cb13a068bb35f9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '6.0.2',
          'version' => '6.0.2.0',
          'reference' => '694d156164372abbd149a4b85ccda2e4670c0e16',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '5.1.2',
          'version' => '5.1.2.0',
          'reference' => 'a8a7e30534b0eb0c77cd9d07e82de1a114389f5e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '5.0.2',
          'version' => '5.0.2.0',
          'reference' => 'c687e3387b99f5b03b6caa64c74b63e2936ff874',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'socialiteproviders/discord' => 
        array (
          'pretty_version' => '4.2.0',
          'version' => '4.2.0.0',
          'reference' => 'c71c379acfdca5ba4aa65a3db5ae5222852a919c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../socialiteproviders/discord',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'socialiteproviders/manager' => 
        array (
          'pretty_version' => 'v4.8.1',
          'version' => '4.8.1.0',
          'reference' => '8180ec14bef230ec2351cff993d5d2d7ca470ef4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../socialiteproviders/manager',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'socialiteproviders/steam' => 
        array (
          'pretty_version' => '4.3.1',
          'version' => '4.3.1.0',
          'reference' => 'b92e57db0dc498a2328d98db1ede379528ace211',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../socialiteproviders/steam',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/image' => 
        array (
          'pretty_version' => '3.9.1',
          'version' => '3.9.1.0',
          'reference' => '9a8e02839897b236f37708d24bcb12381ba050ff',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/image',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/image-optimizer' => 
        array (
          'pretty_version' => '1.8.1',
          'version' => '1.8.1.0',
          'reference' => '2ad9ac7c19501739183359ae64ea6c15869c23d9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/image-optimizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-medialibrary' => 
        array (
          'pretty_version' => '11.17.10',
          'version' => '11.17.10.0',
          'reference' => '10bbf232d27a69fd2f991e88d7e3d72e84d715cb',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-package-tools' => 
        array (
          'pretty_version' => '1.92.7',
          'version' => '1.92.7.0',
          'reference' => 'f09a799850b1ed765103a4f0b4355006360c49a5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-package-tools',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'spatie/temporary-directory' => 
        array (
          'pretty_version' => '2.3.1',
          'version' => '2.3.1.0',
          'reference' => '662e481d6ec07ef29fd05010433428851a42cd07',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/temporary-directory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v7.3.0',
          'version' => '7.3.0.0',
          'reference' => 'b81435fbd6648ea425d1ee96a2d8e68f4ceacd24',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '5f360ebc65c55265a74d23d7fe27f957870158a1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v7.3.0',
          'version' => '7.3.0.0',
          'reference' => '601a5ce9aaad7bf10797e3663faefce9e26c24e2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '63afe740e99a13ba87ec199bb07bbdee937a5b62',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '0b31a944fcd8759ae294da4d2808cbc53aebd0c3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v7.3.0',
          'version' => '7.3.0.0',
          'reference' => '497f73ac996a598c92409b44ac43b6690c4f666d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '59eb412e93815df44f05f342958efa9f46b1e586',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '2a6614966ba1074fa93dae0bc804227422df4dfe',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '6877c122b3a6cc3695849622720054f6e6fa5fa6',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '6ecc895559ec0097e221ed2fd5eb44d5fede083c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => 'd43e84d9522345f96ad6283d5dfccc8c1cfc299b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => 'e0a0f859148daf1edf6c60b398eb40bfc96697d1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => 'a3cc8b044a6ea513310cbd48ef7333b384945638',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => 'b9123926e3b7bc2f98c02ad54f6a4b02b91a8abe',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '9614ac4d8061dc257ecc64cba1b140873dce8ad3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '6d857f4d76bd4b343eac26d6b539585d2bc56493',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '0cc9dd0f17f61d8131e7df6b84bd344899fe2608',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php81' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '4a4cfc2d253c21a5ad0e53071df248ed48c6ce5c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php81',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '2fb86d65e2d424369ad2905e83b236a8805ba491',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.32.0',
          'version' => '1.32.0.0',
          'reference' => '21533be36c24be3f4b1669c4725c7d1d2bab4ae2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.3.0',
          'version' => '7.3.0.0',
          'reference' => '40c295f2deb408d5e9d2d32b8ba1dd61e36f05af',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '7614b8ca5fa89b9cd233e21b627bfc5774f586e4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'f021b05a130d35510bd6b25fe9053c2a8a15d5d4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '42f505aff654e62ac7ac2ce21033818297ca89ca',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '81b48f4daa96272efcce9c7a6c4b58e629df3c90',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'df210c7a2573f1913b2d17cc95f90f53a73d8f7d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.3.1',
          'version' => '7.3.1.0',
          'reference' => 'a69f69f3159b852651a6bf45a9fdd149520525bb',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => '53205bea27450dc5c65377518b3275e126d45e75',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/yaml' => 
        array (
          'pretty_version' => 'v7.3.2',
          'version' => '7.3.2.0',
          'reference' => 'b8d7d868da9eb0919e99c8830431ea087d6aae30',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/yaml',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '1.2.3',
          'version' => '1.2.3.0',
          'reference' => '737eda637ed5e28c3413cb1ebe8bb52cbf1ca7a2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tightenco/ziggy' => 
        array (
          'pretty_version' => 'v2.5.3',
          'version' => '2.5.3.0',
          'reference' => '0b3b521d2c55fbdb04b6721532f7f5f49d32f52b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../tightenco/ziggy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.3.0',
          'version' => '2.3.0.0',
          'reference' => '0d72ac1c00084279c1816675284073c5a337c20d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.2',
          'version' => '5.6.2.0',
          'reference' => '24ac4c74f91ee2c193fa1aaa5c249cb0822809af',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.0.3',
          'version' => '2.0.3.0',
          'reference' => 'b1d923f88091c6bf09699efcd7c8a1b1bfd7351d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '1.11.0',
          'version' => '1.11.0.0',
          'reference' => '11cb2199493b2f8a3b53e7f19068fc6aac760991',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    '/var/www/KrosmozJdr/vendor/larastan/larastan/bootstrap.php' => '28392079817075879815f110287690e80398fe5e',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/Attribute85.php' => '123dcd45f03f2463904087a66bfe2bc139760df0',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionAttribute.php' => '0b4b78277eb6545955d2ce5e09bff28f1f8052c8',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionIntersectionType.php' => 'a3e6299b87ee5d407dae7651758edfa11a74cb11',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionUnionType.php' => '1b349aa997a834faeafe05fa21bc31cae22bf2e2',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'FFI',
    2 => 'PDO',
    3 => 'Phar',
    4 => 'Reflection',
    5 => 'SPL',
    6 => 'SimpleXML',
    7 => 'Zend OPcache',
    8 => 'bcmath',
    9 => 'calendar',
    10 => 'ctype',
    11 => 'curl',
    12 => 'date',
    13 => 'dom',
    14 => 'exif',
    15 => 'fileinfo',
    16 => 'filter',
    17 => 'ftp',
    18 => 'gd',
    19 => 'gettext',
    20 => 'hash',
    21 => 'iconv',
    22 => 'igbinary',
    23 => 'imagick',
    24 => 'intl',
    25 => 'json',
    26 => 'ldap',
    27 => 'libxml',
    28 => 'mbstring',
    29 => 'mysqli',
    30 => 'mysqlnd',
    31 => 'openssl',
    32 => 'pcntl',
    33 => 'pcre',
    34 => 'pdo_mysql',
    35 => 'posix',
    36 => 'random',
    37 => 'readline',
    38 => 'redis',
    39 => 'session',
    40 => 'shmop',
    41 => 'soap',
    42 => 'sockets',
    43 => 'sodium',
    44 => 'standard',
    45 => 'sysvmsg',
    46 => 'sysvsem',
    47 => 'sysvshm',
    48 => 'tokenizer',
    49 => 'xml',
    50 => 'xmlreader',
    51 => 'xmlwriter',
    52 => 'xsl',
    53 => 'zip',
    54 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '6',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  '/var/www/KrosmozJdr/app/Services/Characteristic/Formula/FormulaResolutionService.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureObjectBonusToCreatureVariables.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Services\\Creature\\Runtime\\CreatureObjectBonusToCreatureVariables',
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Services\\Creature\\Runtime\\CreatureRuntimeStatsService',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Services\\Creature\\Runtime\\CreatureObjectBonusToCreatureVariables',
        ),
        1 => 'mergeInto',
        2 => 56,
      ),
    ),
  ),
); },
	'dependencies' => array (
  '/var/www/KrosmozJdr/app/Services/Characteristic/Formula/FormulaResolutionService.php' => 
  array (
    'fileHash' => 'ab180185116e85819fbf8cd469d55076b9882f63',
    'dependentFiles' => 
    array (
      0 => '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php',
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureItemBonusAggregator.php' => 
  array (
    'fileHash' => '63cd94bfb97df300e3338da66cfb0c8406a7b08f',
    'dependentFiles' => 
    array (
      0 => '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php',
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureObjectBonusToCreatureVariables.php' => 
  array (
    'fileHash' => 'afca722f744ee936809b7d7a303c3ff72aed3fac',
    'dependentFiles' => 
    array (
      0 => '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php',
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php' => 
  array (
    'fileHash' => '62c72598a9eefdc8e4daf07cdfdcc8c49b150b4c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureVariableMapBuilder.php' => 
  array (
    'fileHash' => 'b8be06ec08a0bc50565277afe678c512df0d12f8',
    'dependentFiles' => 
    array (
      0 => '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php',
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  '/var/www/KrosmozJdr/app/Services/Characteristic/Formula/FormulaResolutionService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Service central de résolution des formules de caractéristiques.
 *
 * Responsabilités :
 * - Valider le format des formules (syntaxe, pas de constructions dangereuses)
 * - Sécurité : évaluation sans eval(), uniquement nombres, opérateurs, variables [id] et fonctions autorisées (floor, ceil, round, sqrt, abs, cos, sin, tan, asin, acos, atan, pow, min, max)
 * - Évaluer une formule (ou table JSON) avec des variables données
 * - Produire toutes les valeurs possibles pour une plage d\'une variable (ex. level 1 à 20)
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 * @see FormulaConfigDecoder
 * @see SafeExpressionEvaluator
 */',
         'namespace' => 'App\\Services\\Characteristic\\Formula',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expressionEvaluator',
               'type' => 'App\\Services\\Characteristic\\Formula\\SafeExpressionEvaluator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'validateFormula',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Valide le format d\'une formule (ou table) et retourne la liste d\'erreurs.
     *
     * Pour une formule : vérifie que seuls [id], nombres, + - * / ( ), floor, ceil sont présents,
     * puis valide l\'expression après substitution des variables par 0.
     * Pour une table JSON : valide la structure et chaque entrée (valeur fixe ou sous-formule).
     *
     * @return list<string> Liste d\'erreurs (vide si valide)
     */',
             'namespace' => 'App\\Services\\Characteristic\\Formula',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formula',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'evaluate',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Évalue une formule (simple ou table JSON) avec les variables fournies.
     *
     * @param  string|null  $formula  Formule (ex. [vitality]*10+[level]*2) ou JSON table
     * @param  array<string, int|float>  $variables  Map id => valeur (ex. level => 5, vitality => 12)
     * @return float|null Résultat ou null si formule invalide / vide
     */',
             'namespace' => 'App\\Services\\Characteristic\\Formula',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?float',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formula',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'variables',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'evaluateForVariableRange',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Pour une formule, retourne un tableau de toutes les valeurs possibles
     * quand une variable parcourt une plage [min, max] (inclus).
     *
     * Les autres variables restent fixées via $baseVariables. Utile par ex. pour
     * afficher les valeurs level 1→20 d\'une formule qui contient [level].
     *
     * @param  string|null  $formula  Formule ou table JSON
     * @param  string  $variableName  Nom de la variable à faire varier (ex. "level")
     * @param  int  $min  Valeur minimale (incluse)
     * @param  int  $max  Valeur maximale (incluse)
     * @param  array<string, int|float>  $baseVariables  Variables fixes (ex. vitality => 10)
     * @return array<int, float> Map valeur_variable => résultat (ex. [1 => 12.0, 2 => 14.0, ...])
     */',
             'namespace' => 'App\\Services\\Characteristic\\Formula',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formula',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'variableName',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'min',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'max',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'baseVariables',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'extractVariablePlaceholders',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Liste les identifiants [id] présents dans une formule (expression simple ou sous-formules d’une table JSON).
     * Utile pour la résolution ordonnée ou l’affichage (décomposition, tooltips).
     *
     * @return list<string> Identifiants uniques dans l’ordre d’apparition
     */',
             'namespace' => 'App\\Services\\Characteristic\\Formula',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formula',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'substitutePlaceholdersForDisplay',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Remplace les [id] par des nombres pour affichage (tooltips). Les variables manquantes deviennent 0.
     * Ne gère pas les tables JSON (retourne null).
     *
     * @param  array<string, int|float>  $variables
     */',
             'namespace' => 'App\\Services\\Characteristic\\Formula',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formula',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'variables',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureItemBonusAggregator.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Creature\\Runtime\\CreatureItemBonusAggregator',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Agrège les bonus JSON des objets liés à une créature (quantités prises en compte).
 *
 * @example
 *   $agg = new CreatureItemBonusAggregator();
 *   $totals = $agg->aggregateTotals($creature->items);
 *   $lines = $agg->aggregatePerItemLines($creature->items);
 */',
         'namespace' => 'App\\Services\\Creature\\Runtime',
         'uses' => 
        array (
          'item' => 'App\\Models\\Entity\\Item',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'aggregateTotals',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Somme par clé courte (ex. strength, athletics) — même convention que ItemEffectsToBonusConverter.
     *
     * @param  \\Illuminate\\Support\\Collection<int, Item>|\\Illuminate\\Database\\Eloquent\\Collection<int, Item>  $items
     * @return array<string, int>
     */',
             'namespace' => 'App\\Services\\Creature\\Runtime',
             'uses' => 
            array (
              'item' => 'App\\Models\\Entity\\Item',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'items',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'aggregatePerItemLines',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Détail par objet (pour décomposition UI).
     *
     * @param  \\Illuminate\\Support\\Collection<int, Item>|\\Illuminate\\Database\\Eloquent\\Collection<int, Item>  $items
     * @return list<array{item_id: int, name: string, quantity: int, bonuses: array<string, int>}>
     */',
             'namespace' => 'App\\Services\\Creature\\Runtime',
             'uses' => 
            array (
              'item' => 'App\\Models\\Entity\\Item',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'items',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureObjectBonusToCreatureVariables.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Creature\\Runtime\\CreatureObjectBonusToCreatureVariables',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Applique les bonus agrégés d’objets (clés courtes type Dofus→Krosmoz) sur la carte de variables runtime.
 *
 * Règles :
 * - Clé `{stat}_object` → `{stat}_creature` si la créature a une carac avec db_column (ex. strength → strength_creature).
 * - Compétences D&D : clé anglaise courte (ex. athletics) → colonne bonus français (athletisme_bonus) — voir mapping explicite.
 */',
         'namespace' => 'App\\Services\\Creature\\Runtime',
         'uses' => 
        array (
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'getter',
               'type' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mergeInto',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Fusionne les totaux d’objets dans $variables (modifié en place). Retourne les mêmes totaux pour la payload.
     *
     * @param  array<string, int|float>  $variables
     * @param  array<string, int>  $itemTotals
     * @return array<string, int>
     */',
             'namespace' => 'App\\Services\\Creature\\Runtime',
             'uses' => 
            array (
              'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'variables',
               'type' => 'array',
               'byRef' => true,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'entity',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'itemTotals',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureRuntimeStatsService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Creature\\Runtime\\CreatureRuntimeStatsService',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Calcule les caractéristiques dérivées d’une créature (formules BDD + bonus objets) et expose une décomposition pour l’API / tooltips.
 */',
         'namespace' => 'App\\Services\\Creature\\Runtime',
         'uses' => 
        array (
          'characteristic' => 'App\\Models\\Characteristic',
          'characteristiccreature' => 'App\\Models\\CharacteristicCreature',
          'creature' => 'App\\Models\\Entity\\Creature',
          'formularesolutionservice' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
          'formulavariableresolver' => 'App\\Services\\Characteristic\\Formula\\FormulaVariableResolver',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'getter',
               'type' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formulas',
               'type' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'variableMapBuilder',
               'type' => 'App\\Services\\Creature\\Runtime\\CreatureVariableMapBuilder',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'itemBonusAggregator',
               'type' => 'App\\Services\\Creature\\Runtime\\CreatureItemBonusAggregator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'objectBonusMerger',
               'type' => 'App\\Services\\Creature\\Runtime\\CreatureObjectBonusToCreatureVariables',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'resolve',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array{
     *   entity: string,
     *   variables: array<string, float|int>,
     *   computed: array<string, array{
     *     key: string,
     *     value: float,
     *     formula: string|null,
     *     formula_display: string|null,
     *     substituted: string|null,
     *     placeholders: list<array{id: string, value: float}>
     *   }>,
     *   unresolved_computed_keys: list<string>,
     *   items: array{
     *     aggregated: array<string, int>,
     *     lines: list<array{item_id: int, name: string, quantity: int, bonuses: array<string, int>}>
     *   }
     * }
     */',
             'namespace' => 'App\\Services\\Creature\\Runtime',
             'uses' => 
            array (
              'characteristic' => 'App\\Models\\Characteristic',
              'characteristiccreature' => 'App\\Models\\CharacteristicCreature',
              'creature' => 'App\\Models\\Entity\\Creature',
              'formularesolutionservice' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
              'formulavariableresolver' => 'App\\Services\\Characteristic\\Formula\\FormulaVariableResolver',
              'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'creature',
               'type' => 'App\\Models\\Entity\\Creature',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'entity',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/var/www/KrosmozJdr/app/Services/Creature/Runtime/CreatureVariableMapBuilder.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\Creature\\Runtime\\CreatureVariableMapBuilder',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Construit la carte de variables [id] => valeur à partir des colonnes créature (+ métadonnées characteristic_creature).
 */',
         'namespace' => 'App\\Services\\Creature\\Runtime',
         'uses' => 
        array (
          'characteristic' => 'App\\Models\\Characteristic',
          'characteristiccreature' => 'App\\Models\\CharacteristicCreature',
          'creature' => 'App\\Models\\Entity\\Creature',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'buildBaseMap',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Carte initiale : lignes characteristic_creature (db_column) + colonnes scalaires non couvertes.
     *
     * @return array<string, int|float>
     */',
             'namespace' => 'App\\Services\\Creature\\Runtime',
             'uses' => 
            array (
              'characteristic' => 'App\\Models\\Characteristic',
              'characteristiccreature' => 'App\\Models\\CharacteristicCreature',
              'creature' => 'App\\Models\\Entity\\Creature',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'creature',
               'type' => 'App\\Models\\Entity\\Creature',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'entity',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
