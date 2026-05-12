<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1778577091,
	'meta' => array (
  'cacheVersion' => 'v12-linesToIgnore',
  'phpstanVersion' => '2.1.54',
  'fnsr' => false,
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80417,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, parseModelCastsMethod: false, enableMigrationCache: false, level: 6, treatPhpDocTypesAsCertain: false, paths: [/var/www/KrosmozJdr/app/Enums/Visibility.php, /var/www/KrosmozJdr/app/Http/Controllers/PageController.php, /var/www/KrosmozJdr/app/Http/Controllers/SectionController.php, /var/www/KrosmozJdr/app/Http/Requests/StorePageRequest.php, /var/www/KrosmozJdr/app/Http/Requests/UpdatePageRequest.php, /var/www/KrosmozJdr/app/Http/Requests/StoreSectionRequest.php, /var/www/KrosmozJdr/app/Http/Requests/UpdateSectionRequest.php, /var/www/KrosmozJdr/app/Http/Requests/StoreFileRequest.php, /var/www/KrosmozJdr/app/Http/Resources/PageResource.php, /var/www/KrosmozJdr/app/Http/Resources/SectionResource.php, /var/www/KrosmozJdr/app/Http/Resources/UserLightResource.php, /var/www/KrosmozJdr/app/Models/Page.php, /var/www/KrosmozJdr/app/Models/Section.php, /var/www/KrosmozJdr/app/Policies/PagePolicy.php, /var/www/KrosmozJdr/app/Policies/SectionPolicy.php, /var/www/KrosmozJdr/app/Services/SectionService.php], tmpDir: /var/www/KrosmozJdr/storage/phpstan}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ContractsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\FacadesMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ManagersMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\AuthsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper, arguments: {parser: @currentPhpVersionSimpleDirectParser, parseModelCastsMethod: %parseModelCastsMethod%}}, {class: Larastan\\Larastan\\Properties\\MigrationCache, arguments: {cacheDirectory: %tmpDir%, enabled: %enableMigrationCache%}}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%, treatPhpDocTypesAsCertain: %treatPhpDocTypesAsCertain%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentCollectionMapDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}}}',
  'analysedPaths' => 
  array (
    0 => '/var/www/KrosmozJdr/app/Console/Commands',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    '/var/www/KrosmozJdr/composer.lock' => '6733cd1447430d8cba1441c7e88ee7f7ada6336a3bec688aadebc5623c850ecf',
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
          'pretty_version' => 'v3.16.5',
          'version' => '3.16.5.0',
          'reference' => 'e85c0a8464da67e5b4a53a42796d46a43fc06c9a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-debugbar',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'barryvdh/laravel-dompdf' => 
        array (
          'pretty_version' => 'v3.1.2',
          'version' => '3.1.2.0',
          'reference' => 'ee3b72b19ccdf57d0243116ecb2b90261344dedc',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-dompdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'barryvdh/laravel-ide-helper' => 
        array (
          'pretty_version' => 'v3.7.0',
          'version' => '3.7.0.0',
          'reference' => 'ad7e37676f1ff985d55ef1b6b96a0c0a40f2609a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-ide-helper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'barryvdh/reflection-docblock' => 
        array (
          'pretty_version' => 'v2.4.1',
          'version' => '2.4.1.0',
          'reference' => '4f5ba70c30c81f2ce03a16a9965832cfcc31ed3b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.14.8',
          'version' => '0.14.8.0',
          'reference' => '63422359a44b7f06cae63c3b429b59e8efcc0629',
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
        'codezero/browser-locale' => 
        array (
          'pretty_version' => '3.4.0',
          'version' => '3.4.0.0',
          'reference' => 'dd6c50e5557b06b57960df6db7f52e85ef683e5e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../codezero/browser-locale',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/class-map-generator' => 
        array (
          'pretty_version' => '1.7.3',
          'version' => '1.7.3.0',
          'reference' => '86d8208fc3c649a3a999daf1a63c25201be2990f',
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
          'pretty_version' => '3.4.4',
          'version' => '3.4.4.0',
          'reference' => '198166618906cb2de69b95d7d47e5fa8aa1b2b95',
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
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
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
          'pretty_version' => 'v3.1.5',
          'version' => '3.1.5.0',
          'reference' => 'f11ead23a8a76d0ff9bbc6c7c8fd7e05ca328496',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/dompdf',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-font-lib' => 
        array (
          'pretty_version' => '1.0.2',
          'version' => '1.0.2.0',
          'reference' => 'a6e9a688a2a80016ac080b97be73d3e10c444c9a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/php-font-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dompdf/php-svg-lib' => 
        array (
          'pretty_version' => '1.0.2',
          'version' => '1.0.2.0',
          'reference' => '8259ffb930817e72b1ff1caef5d226501f3dfeb1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dompdf/php-svg-lib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragon-code/contracts' => 
        array (
          'pretty_version' => '2.25.0',
          'version' => '2.25.0.0',
          'reference' => '13d1254801026be5ba33cf1309a414953869175f',
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
          'pretty_version' => '6.17.1',
          'version' => '6.17.1.0',
          'reference' => '82a465953267989883d64b921e9725600a5073b5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../dragon-code/support',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
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
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'firebase/php-jwt' => 
        array (
          'pretty_version' => 'v7.0.5',
          'version' => '7.0.5.0',
          'reference' => '47ad26bab5e7c70ae8a6f08ed25ff83631121380',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../firebase/php-jwt',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'reference' => '38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => 'e01f4a821471308ba86aa202fed6698b6b695e3b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.10.0',
          'version' => '7.10.0.0',
          'reference' => 'b51ac707cfa420b7bfd4e4d5e510ba8008e822b4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '481557b130ef3790cf82b713667b43030dc9c957',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.9.0',
          'version' => '2.9.0.0',
          'reference' => '7d0ed42f28e42d61352a7a79de682e5e67fec884',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.5',
          'version' => '1.0.5.0',
          'reference' => '4f4bbd4e7172148801e76e3decc1e559bdee34e1',
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
          'pretty_version' => 'v0.7',
          'version' => '0.7.0.0',
          'reference' => '610392f38de49a44dab08dc1659960a29874c4b8',
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
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/reflection' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.58.0',
          ),
        ),
        'inertiajs/inertia-laravel' => 
        array (
          'pretty_version' => 'v2.0.24',
          'version' => '2.0.24.0',
          'reference' => 'ea345adad12f110edbbc4bef03b69c2374a535d4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'intervention/gif' => 
        array (
          'pretty_version' => '4.2.4',
          'version' => '4.2.4.0',
          'reference' => 'c3598a16ebe7690cd55640c44144a9df383ea73c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../intervention/gif',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'intervention/image' => 
        array (
          'pretty_version' => '3.11.8',
          'version' => '3.11.8.0',
          'reference' => 'cf04c8dd245697f701057c13d4bfe140d584e738',
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
          'pretty_version' => 'v3.9.6',
          'version' => '3.9.6.0',
          'reference' => '9ad17e83e96b63536cb6ac39c3d40d29ff9cf636',
          'type' => 'phpstan-extension',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel-lang/actions' => 
        array (
          'pretty_version' => '1.12.2',
          'version' => '1.12.2.0',
          'reference' => 'ef8847493e1c6a15e87667d64430e5312527b5e7',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/actions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/attributes' => 
        array (
          'pretty_version' => '2.15.6',
          'version' => '2.15.6.0',
          'reference' => '5e1806802af893b9ede26210be1ff90efd0697f1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/attributes',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/common' => 
        array (
          'pretty_version' => '6.8.0',
          'version' => '6.8.0.0',
          'reference' => 'f1e3383f94c4f157f0f41324792382bf2dd84d52',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/config' => 
        array (
          'pretty_version' => '1.17.0',
          'version' => '1.17.0.0',
          'reference' => '77ad089234aa74961ca30c7e6d13db9a62654c87',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/http-statuses' => 
        array (
          'pretty_version' => '3.12.1',
          'version' => '3.12.1.0',
          'reference' => '4f1cfeb3df179bdcd1c40bb57cc5c7f2a11278b4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/http-statuses',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/json-fallback' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '1b02c798da837c63e2d01f45b45bb49367d5b6a1',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/json-fallback',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/lang' => 
        array (
          'pretty_version' => '15.29.5',
          'version' => '15.29.5.0',
          'reference' => '9a1d194c9a72b8ee70a3515d8415e9645fe531fe',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/lang',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/locale-list' => 
        array (
          'pretty_version' => '1.7.0',
          'version' => '1.7.0.0',
          'reference' => '48e61c7f0a957420d4aaf5d35653889c25c4e2d4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/locale-list',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/locales' => 
        array (
          'pretty_version' => '2.11.0',
          'version' => '2.11.0.0',
          'reference' => '761aa3cfbc5bbe29eb958c9839e7dd3806193bac',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/locales',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/models' => 
        array (
          'pretty_version' => '1.7.0',
          'version' => '1.7.0.0',
          'reference' => 'a9aa0205ca019f4459ace26b0432b3b6d1886f66',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/models',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/moonshine' => 
        array (
          'pretty_version' => '1.9.0',
          'version' => '1.9.0.0',
          'reference' => '2340066d456e989f3c40ace85945bc182d64e710',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/moonshine',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-country-names' => 
        array (
          'pretty_version' => '1.8.0',
          'version' => '1.8.0.0',
          'reference' => '1d293138e34eb9e914bc4568cdebac2cb0a2eb0e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-country-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-currency-names' => 
        array (
          'pretty_version' => '1.9.0',
          'version' => '1.9.0.0',
          'reference' => '78beb3c74fc49970b2f948def631512d2a71f3d9',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-currency-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/native-locale-names' => 
        array (
          'pretty_version' => '2.8.0',
          'version' => '2.8.0.0',
          'reference' => 'c9908827c17a345ae9b0a248380bb223c04ed595',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/native-locale-names',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/publisher' => 
        array (
          'pretty_version' => '16.8.0',
          'version' => '16.8.0.0',
          'reference' => 'e5d3383f5385c2102f8a0d3dbe488ed86cd0250f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/publisher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/routes' => 
        array (
          'pretty_version' => '1.11.0',
          'version' => '1.11.0.0',
          'reference' => '29d7f83f2679f51187aa4e761d73bb4e0d133bf4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/routes',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel-lang/starter-kits' => 
        array (
          'pretty_version' => '1.13.2',
          'version' => '1.13.2.0',
          'reference' => '1b03951b21ec9cd49a276ce2be657020404c30e6',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/starter-kits',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/breeze' => 
        array (
          'pretty_version' => 'v2.4.1',
          'version' => '2.4.1.0',
          'reference' => '28cefeaf6af20177ddf5cc7b93e87e4ad79d533f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/breeze',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v12.58.0',
          'version' => '12.58.0.0',
          'reference' => '6172ae1f44ba5d89e111057ee4a4e7c27f5a610d',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.6',
          'version' => '1.2.6.0',
          'reference' => 'aa71a01c309e7f66bc2ec4fb1a59291b82eb4abf',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.29.1',
          'version' => '1.29.1.0',
          'reference' => '0770e9b7fafd50d4586881d456d6eb41c9247a80',
          'type' => 'project',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.17',
          'version' => '0.3.17.0',
          'reference' => '6a82ac19a28b916ae0885828795dbd4c59d9a818',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sail' => 
        array (
          'pretty_version' => 'v1.58.0',
          'version' => '1.58.0.0',
          'reference' => '2e5e968138ca52ed87d712449697a8364d73b466',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/sail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/sanctum' => 
        array (
          'pretty_version' => 'v4.3.2',
          'version' => '4.3.2.0',
          'reference' => '2a9bccc18e9907808e0018dd15fa643937886b1e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/sanctum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.13',
          'version' => '2.0.13.0',
          'reference' => 'b566ee0dd251f3c4078bed003a7ce015f5ea6dce',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/socialite' => 
        array (
          'pretty_version' => 'v5.27.0',
          'version' => '5.27.0.0',
          'reference' => '40e0757a75637c7b2dff05d3286b0d8fc25e5c0e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/socialite',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v2.11.1',
          'version' => '2.11.1.0',
          'reference' => 'c9f80cc835649b5c1842898fb043f8cc098dd741',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.8.2',
          'version' => '2.8.2.0',
          'reference' => '59fb075d2101740c337c7216e3f32b36c204218b',
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
          'pretty_version' => '3.33.0',
          'version' => '3.33.0.0',
          'reference' => '570b8871e0ce693764434b29154c54b434905350',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.31.0',
          'version' => '3.31.0.0',
          'reference' => '2f669db18a4c20c755c2bb7d3a7b0b2340488079',
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
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '08cf38e3924d4f56238125547b5720496fac8fd4',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '85d5c77c5d6d3af6c54db4a78246364908f3c928',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'maennchen/zipstream-php' => 
        array (
          'pretty_version' => '3.2.2',
          'version' => '3.2.2.0',
          'reference' => '77bebeb4c6c340bb3c11c843b2cffd8bbfde4d5e',
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
            0 => 'v2.2.6',
          ),
        ),
        'mews/purifier' => 
        array (
          'pretty_version' => '3.4.4',
          'version' => '3.4.4.0',
          'reference' => 'b2705cc6c832ce7229373418e191d71b6c037841',
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
          'pretty_version' => '3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'b321dd6749f0bf7189444158a3ce785cc16d69b0',
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
          'pretty_version' => '3.11.4',
          'version' => '3.11.4.0',
          'reference' => 'e890471a3494740f7d9326d72ce6a8c559ffee60',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.5',
          'version' => '1.3.5.0',
          'reference' => 'f0ab1a3cda782dbc5da270d28545236aa80c4002',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.3',
          'version' => '4.1.3.0',
          'reference' => 'bb3ea637e3d131d72acc033cfc2746ee893349fe',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.7.0',
          'version' => '5.7.0.0',
          'reference' => 'dca41cd15c2ac9d055ad70dbfd011130757d1f82',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.9.4',
          'version' => '8.9.4.0',
          'reference' => '716af8f95a470e9094cfca09ed897b023be191a5',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => '712a31b768f5daea284c2169a7d227031001b9a8',
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
          'pretty_version' => 'v2.2.6',
          'version' => '2.2.6.0',
          'reference' => 'abb9fa3c5c8dbe7efe03ddba56782917481de3e8',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../php-debugbar/php-debugbar',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.5',
          'version' => '1.9.5.0',
          'reference' => '75365b91986c2405cf5e1e012c5595cd487a98be',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpseclib/phpseclib' => 
        array (
          'pretty_version' => '3.0.52',
          'version' => '3.0.52.0',
          'reference' => '2adaefc83df2ec548558307690f376dd7d4f4fce',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpseclib/phpseclib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.1.54',
          'version' => '2.1.54.0',
          'reference' => '8be50c3992107dc837b17da4d140fbbdf9a5c5bd',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '11.0.12',
          'version' => '11.0.12.0',
          'reference' => '2c1ed04922802c15e1de5d7447b4856de949cf56',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '5.1.1',
          'version' => '5.1.1.0',
          'reference' => '2f3a64888c814fc235386b7387dd5b5ed92ad903',
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
          'pretty_version' => '11.5.55',
          'version' => '11.5.55.0',
          'reference' => 'adc7262fccc12de2b30f12a8aa0b33775d814f00',
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
          'pretty_version' => 'v0.12.22',
          'version' => '0.12.22.0',
          'reference' => '3be75d5b9244936dd4ac62ade2bfb004d13acf0f',
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
          'pretty_version' => '4.9.2',
          'version' => '4.9.2.0',
          'reference' => '8429c78ca35a09f27565311b98101e2826affde0',
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
            0 => '4.9.2',
          ),
        ),
        'sabberworm/php-css-parser' => 
        array (
          'pretty_version' => 'v9.3.0',
          'version' => '9.3.0.0',
          'reference' => '88dbd0f7f91abbfe4402d0a3071e9ff4d81ed949',
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
          'pretty_version' => '6.3.3',
          'version' => '6.3.3.0',
          'reference' => '2c95e1e86cb8dd41beb8d502057d1081ccc8eca9',
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
          'pretty_version' => '6.3.2',
          'version' => '6.3.2.0',
          'reference' => '70a298763b40b213ec087c51c739efcaa90bcd74',
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
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => 'f6458abbf32a6c8174f8f26261475dc133b3d9dc',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '5.1.3',
          'version' => '5.1.3.0',
          'reference' => 'f77d2d4e78738c98d9a68d2596fe5e8fa380f449',
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
          'pretty_version' => '4.9.2',
          'version' => '4.9.2.0',
          'reference' => '35372dc62787e61e91cfec73f45fd5d5ae0f8891',
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
          'pretty_version' => '3.9.4',
          'version' => '3.9.4.0',
          'reference' => '6a322b5e9268e3903d4fb6e1ff08b7dcc3aa9429',
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
          'pretty_version' => '11.22.1',
          'version' => '11.22.1.0',
          'reference' => '808424a4b7dc9811b5633c28a97c530919aea7b8',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-package-tools' => 
        array (
          'pretty_version' => '1.93.0',
          'version' => '1.93.0.0',
          'reference' => '0d097bce95b2bf6802fb1d83e1e753b0f5a948e7',
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
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => 'b55a638b189a6faa875e0ccdb00908fb87af95b3',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => 'd7d2b64a45a89d607865927b176fa51c33ddbb58',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v8.0.9',
          'version' => '8.0.9.0',
          'reference' => '3665cfade90565430909b906394c73c8739e57d0',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.7.0',
          'version' => '3.7.0.0',
          'reference' => '50f59d1f3ca46d41ac911f97a78626b6756af35b',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '8dd79d8af777ee6cba2fd4d98da6ffb839f3c0fa',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v8.0.9',
          'version' => '8.0.9.0',
          'reference' => '0c3c1a17604c4dbbec4b93fe162c538482096e1f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.7.0',
          'version' => '3.7.0.0',
          'reference' => 'ccba7060602b7fed0b03c85bf025257f76d9ef32',
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
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => 'e0be088d22278583a82da281886e8c3592fbf149',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '9381209597ec66c25be154cbf2289076e64d1eab',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.10',
          'version' => '7.4.10.0',
          'reference' => '23486f59234c6fd6e8f1bec97124f3829d686627',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => 'f6ea532250b476bfc1b56699b388a1bdbf168f62',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => '2d550c4758ba4c47519a6667c36553d535705b0c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '141046a8f9477948ff284fa65be2095baafb94f2',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '4864388bfbd3001ce88e234fab652acd91fdc57e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
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
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
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
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '6a21eb99c6973357967f6ce3708cd55a6bec6315',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => 'dfb55726c3a76ea3b6459fcfda1ec2d80a682411',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '3600c2cb22399e25bb226e4a135ce91eeb2a6149',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '88486db2c389b290bf87ff1de7ebc1e13e42bb06',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => 'fcfa4973a9917cef23f2e38774da74a2b7d115ee',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '26dfec253c4cf3e51b541b52ddf7e42cb0908e94',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '60f19cd3badc8de688421e21e4305eba50f8089a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => '287771d8bc86eacb30678dd10eda6c64a859951f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.7.0',
          'version' => '3.7.0.0',
          'reference' => 'd25d82433a80eba6aa0e6c24b61d7370d99e444a',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => 'ae9488f874d7603f9d2dfbf120203882b645d963',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v8.0.10',
          'version' => '8.0.10.0',
          'reference' => 'f63e9342e12646a57c91ef8a366a4f9d8e557b67',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.7.0',
          'version' => '3.7.0.0',
          'reference' => '0ab302977a952b42fd51475c4ebac81f8da0a95d',
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
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => '2676b524340abcfe4d6151ec698463cebafee439',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '9510c3966f749a1d1ff0059e1eabef6cc621e7fd',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/yaml' => 
        array (
          'pretty_version' => 'v8.0.10',
          'version' => '8.0.10.0',
          'reference' => 'aa9ee60c41d9b20a2468c41ff0a32e2a7405ac05',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../symfony/yaml',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'thecodingmachine/safe' => 
        array (
          'pretty_version' => 'v3.4.0',
          'version' => '3.4.0.0',
          'reference' => '705683a25bacf0d4860c7dea4d7947bfd09eea19',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../thecodingmachine/safe',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '1.3.1',
          'version' => '1.3.1.0',
          'reference' => 'b7489ce515e168639d17feec34b8847c326b0b3c',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tightenco/ziggy' => 
        array (
          'pretty_version' => 'v2.6.2',
          'version' => '2.6.2.0',
          'reference' => '8a0b645921623f77dceaf543d61ecd51a391d96e',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../tightenco/ziggy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'f0292ccf0ec75843d65027214426b6b163b48b41',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.3',
          'version' => '5.6.3.0',
          'reference' => '955e7815d677a3eaa7075231212f2110983adecc',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '8e1051fe39379367aecf014f41744ce7539a856f',
          'type' => 'library',
          'install_path' => '/var/www/KrosmozJdr/vendor/composer/../voku/portable-ascii',
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
    '/var/www/KrosmozJdr/vendor/larastan/larastan/bootstrap.php' => '5a3eacbf63b3e41659adfee92facededf8e020a932800f93c9a8b0e67f235805',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/Attribute85.php' => 'cb8b31e82c61ce197871c9e8a6f122256751f2ab606dd2be90846d4fa5f8933e',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionAttribute.php' => 'c0068e383717870a304781d462f7e2afe1c6f24e9133851852a2aca96b4fa26f',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionIntersectionType.php' => '65fe0a8bc6fe285d8ddc8798ab5b9299920af70db5ad74596bc08df823e7c5d9',
    'phar:///var/www/KrosmozJdr/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionUnionType.php' => '1e2fe940e4ba4e00d9ee6adb2af3ee1bf333e6f8afe61c61deb038886d293427',
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
  '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Called \'env\' outside of the config directory which returns null when the config is cached, use \'config\'.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php',
       'line' => 102,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 102,
       'nodeType' => 'PhpParser\\Node\\Expr\\FuncCall',
       'identifier' => 'larastan.noEnvCallsOutsideOfConfig',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Development\\GenerateTestCommand::handle() has no return type specified.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'line' => 18,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 18,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.return',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Development\\GenerateTestCommand::generateTestContent() has no return type specified.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 38,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.return',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Development\\GenerateTestCommand::generateTestContent() has parameter $name with no type specified.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 38,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.parameter',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Comparison operation ">" between 0 and 0 is always false.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'line' => 128,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 128,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Greater',
       'identifier' => 'greater.alwaysFalse',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Result of && is always false.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'line' => 128,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 128,
       'nodeType' => 'PHPStan\\Node\\BooleanAndNode',
       'identifier' => 'booleanAnd.alwaysFalse',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Offset 1 on array{0: non-falsy-string, 1: non-empty-string, 2: non-empty-string, 3?: non-empty-string} on left side of ?? always exists and is not nullable.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'line' => 380,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 380,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullCoalesce.offset',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Offset 2 on array{0: non-falsy-string, 1: non-empty-string, 2: non-empty-string, 3?: non-empty-string} on left side of ?? always exists and is not nullable.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'line' => 381,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 381,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullCoalesce.offset',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Strict comparison using === between non-empty-string and \'\' will always evaluate to false.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'line' => 462,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 462,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Identical',
       'identifier' => 'identical.alwaysFalse',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Strict comparison using !== between numeric-string and \'\' will always evaluate to true.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php',
       'line' => 89,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 89,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\NotIdentical',
       'identifier' => 'notIdentical.alwaysTrue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Project\\ProjectInitCommand::runScrappingMonsters() has parameter $baseArgs with no value type specified in iterable type array.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php',
       'line' => 474,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'See: https://phpstan.org/blog/solving-phpstan-no-value-type-specified-in-iterable-type',
       'nodeLine' => 474,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.iterableValue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Project\\ProjectUpdateCommand::getAutoUpdateIds() has parameter $config with no value type specified in iterable type array.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php',
       'line' => 202,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'See: https://phpstan.org/blog/solving-phpstan-no-value-type-specified-in-iterable-type',
       'nodeLine' => 202,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.iterableValue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Negated boolean expression is always true.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'line' => 260,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'If App\\Console\\Commands\\Project\\SetupCommand::tryAppConnection() is impure, add <fg=cyan>@phpstan-impure</> PHPDoc tag above its declaration. Learn more: <fg=cyan>https://phpstan.org/blog/remembering-and-forgetting-returned-values</>',
       'nodeLine' => 260,
       'nodeType' => 'PhpParser\\Node\\Expr\\BooleanNot',
       'identifier' => 'booleanNot.alwaysTrue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Unreachable statement - code above always terminates.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'line' => 265,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 265,
       'nodeType' => 'PHPStan\\Node\\UnreachableStatementNode',
       'identifier' => 'deadCode.unreachable',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Negated boolean expression is always true.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'line' => 285,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'If App\\Console\\Commands\\Project\\SetupCommand::tryAppConnection() is impure, add <fg=cyan>@phpstan-impure</> PHPDoc tag above its declaration. Learn more: <fg=cyan>https://phpstan.org/blog/remembering-and-forgetting-returned-values</>',
       'nodeLine' => 285,
       'nodeType' => 'PhpParser\\Node\\Expr\\BooleanNot',
       'identifier' => 'booleanNot.alwaysTrue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Unreachable statement - code above always terminates.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'line' => 290,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 290,
       'nodeType' => 'PHPStan\\Node\\UnreachableStatementNode',
       'identifier' => 'deadCode.unreachable',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Constant App\\Console\\Commands\\Scrapping\\ScrappingEffectsMapCommand::SUB_EFFECTS is unused.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php',
       'line' => 39,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'See: https://phpstan.org/developing-extensions/always-used-class-constants',
       'nodeLine' => 24,
       'nodeType' => 'PHPStan\\Node\\ClassConstantsNode',
       'identifier' => 'classConstant.unused',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Relation \'itemType\' is not found in App\\Models\\Entity\\Item model.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php',
       'line' => 72,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 72,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'larastan.relationExistence',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe property access "?->dofusdb_type_id" on left side of ?? is unnecessary. Use -> instead.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php',
       'line' => 82,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 82,
       'nodeType' => 'PhpParser\\Node\\Expr\\BinaryOp\\Coalesce',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 281,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 281,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to function is_array() with array<string, mixed> will always evaluate to true.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 380,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 380,
       'nodeType' => 'PhpParser\\Node\\Expr\\FuncCall',
       'identifier' => 'function.alreadyNarrowedType',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 409,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 409,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to function is_array() with array<mixed> will always evaluate to true.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 819,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 819,
       'nodeType' => 'PhpParser\\Node\\Expr\\FuncCall',
       'identifier' => 'function.alreadyNarrowedType',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 836,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 836,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 992,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 992,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 1009,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 1009,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Scrapping\\ScrappingRunCommand::buildImportOptions() has parameter $collectOptions with no value type specified in iterable type array.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 1023,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'See: https://phpstan.org/blog/solving-phpstan-no-value-type-specified-in-iterable-type',
       'nodeLine' => 1023,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.iterableValue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method Illuminate\\Console\\Command::option() invoked with 2 parameters, 0-1 required.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 1076,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 1076,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'arguments.count',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Relation \'creature\' is not found in App\\Models\\Entity\\Monster model.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 1240,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 1240,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'larastan.relationExistence',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Console\\Commands\\Scrapping\\ScrappingRunCommand::importOne() has parameter $options with no value type specified in iterable type array.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'line' => 1283,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'See: https://phpstan.org/blog/solving-phpstan-no-value-type-specified-in-iterable-type',
       'nodeLine' => 1283,
       'nodeType' => 'PHPStan\\Node\\InClassMethodNode',
       'identifier' => 'missingType.iterableValue',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #2 $rows of method App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand::exportItemTypesTable() expects Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ConsumableType|App\\Models\\Type\\ItemType|App\\Models\\Type\\ResourceType>, Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ResourceType> given.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'line' => 243,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'Template type TModel on class Illuminate\\Database\\Eloquent\\Collection is not covariant. Learn more: <fg=cyan>https://phpstan.org/blog/whats-up-with-template-covariant</>',
       'nodeLine' => 243,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #2 $rows of method App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand::exportItemTypesTable() expects Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ConsumableType|App\\Models\\Type\\ItemType|App\\Models\\Type\\ResourceType>, Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ConsumableType> given.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'line' => 244,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'Template type TModel on class Illuminate\\Database\\Eloquent\\Collection is not covariant. Learn more: <fg=cyan>https://phpstan.org/blog/whats-up-with-template-covariant</>',
       'nodeLine' => 244,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #2 $rows of method App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand::exportItemTypesTable() expects Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ConsumableType|App\\Models\\Type\\ItemType|App\\Models\\Type\\ResourceType>, Illuminate\\Database\\Eloquent\\Collection<int, App\\Models\\Type\\ItemType> given.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'line' => 245,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
       'traitFilePath' => NULL,
       'tip' => 'Template type TModel on class Illuminate\\Database\\Eloquent\\Collection is not covariant. Learn more: <fg=cyan>https://phpstan.org/blog/whats-up-with-template-covariant</>',
       'nodeLine' => 245,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Relation \'itemType\' is not found in App\\Models\\Entity\\Item model.',
       'file' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php',
       'line' => 47,
       'canBeIgnored' => true,
       'filePath' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 47,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'larastan.relationExistence',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ImportLegacyCapabilitiesCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'create',
        2 => 175,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        1 => 'footerMarkdown',
        2 => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'json_decode',
        1 => 274,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\File',
        1 => 'put',
        2 => 64,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\File',
        1 => 'put',
        2 => 34,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/LoadDevelopmentServersCommand.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/PrepareProjectCommand.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Pages\\PagesImportRulesTocCommand',
        1 => 'replaceSectionContent',
        2 => 'App\\Console\\Commands\\Pages\\PagesImportRulesTocCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Console\\Commands\\Pages\\PagesImportRulesTocCommand',
        ),
        1 => 'upsertLevel3Section',
        2 => 114,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Privacy/ProcessPrivacyDeletionRequestsCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\ExecuteUserErasureJob',
        1 => 'dispatch',
        2 => 38,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectClearCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 22,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataCommand.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDepsCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 27,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDevCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 25,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectEffectsCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 22,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectFixPermissionsCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 22,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'set_time_limit',
        1 => 92,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        ),
        1 => 'runStorageLink',
        2 => 133,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\DatabaseManager',
        1 => 'reconnect',
        2 => 401,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\DatabaseManager',
        1 => 'reconnect',
        2 => 467,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Database\\DatabaseManager',
        1 => 'reconnect',
        2 => 492,
      ),
      3 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 536,
      ),
      4 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 547,
      ),
      5 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 548,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      ),
      1 => 
      array (
        0 => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectOptimizeCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 25,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectPrepareCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 26,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectRefreshCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 19,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectResetCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 22,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectSuperAdminCommand.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'set_time_limit',
        1 => 95,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 108,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\DatabaseManager',
        1 => 'reconnect',
        2 => 163,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 170,
      ),
      3 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 237,
      ),
      4 => 
      array (
        0 => 'Illuminate\\Foundation\\Console\\Kernel',
        1 => 'call',
        2 => 238,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'passthru',
        1 => 147,
      ),
      1 => 
      array (
        0 => 'passthru',
        1 => 226,
      ),
      2 => 
      array (
        0 => 'shell_exec',
        1 => 434,
      ),
      3 => 
      array (
        0 => 'exec',
        1 => 450,
      ),
      4 => 
      array (
        0 => 'exec',
        1 => 484,
      ),
      5 => 
      array (
        0 => 'exec',
        1 => 498,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'App\\Console\\Commands\\Project\\SetupCommand',
        ),
        1 => 'runShell',
        2 => 233,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'App\\Console\\Commands\\Project\\SetupCommand',
        ),
        1 => 'runShell',
        2 => 236,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 333,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 341,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 342,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 348,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 376,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'PDO',
        ),
        1 => 'exec',
        2 => 384,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'App\\Console\\Commands\\Project\\SetupCommand',
        ),
        1 => 'runShell',
        2 => 470,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsAutreAuditCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        1 => 'formatTopNumericMap',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
      ),
      1 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        1 => 'formatTopStringMap',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
      ),
      2 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        1 => 'buildWarnings',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'arsort',
        1 => 111,
      ),
      1 => 
      array (
        0 => 'arsort',
        1 => 112,
      ),
      2 => 
      array (
        0 => 'arsort',
        1 => 113,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMapCommand',
        1 => 'resolveCharacteristicKey',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMapCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'ksort',
        1 => 239,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMissingCharacteristicsReportCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uasort',
        1 => 137,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityAuditCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        1 => 'buildWarnings',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uasort',
        1 => 218,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRacesSeedCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 33,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'create',
        2 => 73,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        1 => 'resolveTargetTable',
        2 => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'transaction',
        2 => 126,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'config',
        1 => 127,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\DatabaseManager',
        1 => 'reconnect',
        2 => 456,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'ZipArchive',
        ),
        1 => 'addFile',
        2 => 165,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'ZipArchive',
        ),
        1 => 'close',
        2 => 169,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 57,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\File',
        1 => 'makeDirectory',
        2 => 149,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesExtractCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'usort',
        1 => 78,
      ),
      1 => 
      array (
        0 => 'usort',
        1 => 79,
      ),
      2 => 
      array (
        0 => 'usort',
        1 => 80,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 35,
      ),
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Console\\Command',
        1 => '__construct',
        2 => 36,
      ),
    ),
  ),
); },
	'dependencies' => array (
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/GenerateCharacteristicColorCssCommand.php' => 
  array (
    'fileHash' => '8afb4e078f6100340ae34a62022bf22f0554c92ddbf6bc18791f7c8b322ba0d6',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ImportLegacyCapabilitiesCommand.php' => 
  array (
    'fileHash' => 'da66ae37de4d29e5ba5822bedaebda34da396f4ed0bd738cf011737f3ec43ab9',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ValidateCreatureFormulaPlaceholdersCommand.php' => 
  array (
    'fileHash' => 'afa4b169287aded4e4440b1d55127150c4334549fd0d815f2793bfd2a59e5fac',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php' => 
  array (
    'fileHash' => '2d75736f6a2fa255659513ccdf34f57d8edb961efb819599266cc31dd508bd81',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php' => 
  array (
    'fileHash' => '51db6fd67e28ef5cc2d6918f4c46f8e839597e8fcf6827c7ebe9117e56317ec2',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/LoadDevelopmentServersCommand.php' => 
  array (
    'fileHash' => '2befb9b718e33573be87860d4e5cbf5925e666cefc03c00e288a42c977ef0cb8',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Development/PrepareProjectCommand.php' => 
  array (
    'fileHash' => '21cfb06d382b00b7d050a408634515fb4ac69d08f4a6ae794a29afb5879ca66a',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Effects/EffectsRebuildSignaturesCommand.php' => 
  array (
    'fileHash' => '21e9dfb626a850a362469ba62edb2248899dde3cf9719e60fc13f4e287dee58d',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Media/CleanThumbnailsCommand.php' => 
  array (
    'fileHash' => '8139d369f886ce97ceca31974c2a4ed51bbac273985c90b467fa72950182a957',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php' => 
  array (
    'fileHash' => '8866ecf65788f6f2e7b6b6b0e7b0fbe35aa1a693c758fdfaf8424593bbda4a6c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/RulesInjectCharacteristicKrefsInMarkdownCommand.php' => 
  array (
    'fileHash' => 'c1d4edb8c89988abab205215d15c297f76535daee50989f799f5bf6df300c6ff',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Privacy/ProcessPrivacyDeletionRequestsCommand.php' => 
  array (
    'fileHash' => 'f037ac42f761be51d78ec4afaaec387405614ae07bd168ade562979e819c3efc',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php' => 
  array (
    'fileHash' => '23371e0936af8af2f350774e063d558657602858dade0e88a49867b7a3b53ca7',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectClearCommand.php' => 
  array (
    'fileHash' => 'aff3ab2c8e831dd26e68ff154f1491452b47862718760c6731d5247f314e2251',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataCommand.php' => 
  array (
    'fileHash' => 'fdd8b35dc1e2e6c082b2343797874909697a86c060e70488825103e4003145e1',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataImportRulesTocCommand.php' => 
  array (
    'fileHash' => 'bfb4556bbfe9317b63af787aa735b5c0f4fe5b8a7e09cf4d57811e570c8225c2',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDepsCommand.php' => 
  array (
    'fileHash' => '4b9c2ba78148678a1c3c67835cc1aea325885cd02f98e19889cbe51be1d1d543',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDevCommand.php' => 
  array (
    'fileHash' => '17df543fc6e31914d904b204e065e2e2075bc31905093249d29bff8946ef6c74',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectEffectsCommand.php' => 
  array (
    'fileHash' => 'f67ac08cf7a675f49246978d3848a653865aded11af90b6b9c179dd765e93fdd',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectFixPermissionsCommand.php' => 
  array (
    'fileHash' => 'a75d29feeb2af5d91c01e51e33e13c0b615e02b986e835781de7a4728a7a3416',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php' => 
  array (
    'fileHash' => 'd30043fd5198c94b0ed09f8b59192dcca6643123b9e300e48859c3d519dd7516',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectOptimizeCommand.php' => 
  array (
    'fileHash' => '9a6dac75d09474aaf57233eaa439428676796a85780909133ebe28b5dfcd48ae',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectPrepareCommand.php' => 
  array (
    'fileHash' => '8b6fe8fb95fc98555433f81bfcbc1acda7ad0662821edad4dddb56f658c0e327',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectRefreshCommand.php' => 
  array (
    'fileHash' => '2df43ed9e28452f8b27a65c90d9c0b0b30f21d1f6ec774fe31902ae32b81b1a4',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectResetCommand.php' => 
  array (
    'fileHash' => '5ca9214cee29586edc0c7cfed6112e35504c6b2594325f2ffbf78309ee721b06',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectReviewCommand.php' => 
  array (
    'fileHash' => 'fe40fbfdf561b793982a1e3c2ca47b60431f5c5d69e5f007a6180bafd2115f7e',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectSuperAdminCommand.php' => 
  array (
    'fileHash' => '268bf84c6e54fbefde0a6a35935aa50e393ce4d8910d96d133e5f63a58e08b2c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php' => 
  array (
    'fileHash' => 'db7ae008c5e74b9af75c0fb868ca8f1c279a08007858cca27aaa4fadcf9df7b0',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php' => 
  array (
    'fileHash' => 'f741b2bb1d13b83ca03ab3fb52c9f31bbca670e525a6dde5b72aeecc40bb4da6',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsAutreAuditCommand.php' => 
  array (
    'fileHash' => 'a0b6599c89803a5b6ac88f61a7f738a6690b42cd361321cd05318ba917de84fe',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsBackfillCharacteristicsCommand.php' => 
  array (
    'fileHash' => '8a20171e53414bd2eced2c83a70c17716b0adbeaa8bcdd893472ee2d19dec4fe',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php' => 
  array (
    'fileHash' => 'aac1aafbf801f96b954aa5a13931c8fea730c431a80e260cc280576a14641bc5',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMissingCharacteristicsReportCommand.php' => 
  array (
    'fileHash' => '957843a82af64cb2b3aa65a0f44afe99d378dd32fd9495026408d9c01b545175',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsPipelineCommand.php' => 
  array (
    'fileHash' => '71ec7928f8d8d8dc6f43a27602f291f3968cdb5f90730550b44447ac0de350fa',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityAuditCommand.php' => 
  array (
    'fileHash' => '78fb62fec5c7ca23868287e2571781a65882833a973064047ae1560ccc241d8b',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityGateCommand.php' => 
  array (
    'fileHash' => 'f479e8141b94851ec69affbece7de6a6d1dfaad6055b1aceadbc4c9211ddfc7e',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRacesSeedCommand.php' => 
  array (
    'fileHash' => '7cf858df6ce20fcf191b3ccde1d8fc8336d0523c5d1f6aa88e19d8c7a77b5a6b',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php' => 
  array (
    'fileHash' => 'e57e6907e16d1566776f91c040b2f30d6dbcc7cf8cac5e4d1b437d1b798244ba',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php' => 
  array (
    'fileHash' => '6255756d8e1d5bdb2a6b3a9165cf20a5c6ee94b5a87a11de818d435e7247cc4c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php' => 
  array (
    'fileHash' => 'e201c2422c93e444008c3bb0c01f664744b4e0b41fb8b1032fb2a7a2ae40a037',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSetupCommand.php' => 
  array (
    'fileHash' => '14469cd25886e926df9b15fab998956bbdbe5a9f4afb338bcd7f9c826e8d03d5',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesExtractCommand.php' => 
  array (
    'fileHash' => 'a11b949fab4ab3c61f758cdea58683fa1671ce8a24d58377675b87404b96255a',
    'dependentFiles' => 
    array (
      0 => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesSeedCommand.php',
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php' => 
  array (
    'fileHash' => '190c547d9145fb650e9b1ffa35407cd1a911ee26bfaf5fc51cea20157b4665f6',
    'dependentFiles' => 
    array (
    ),
  ),
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesSeedCommand.php' => 
  array (
    'fileHash' => '1f42c8b2a0e7d648c8b0355062781fe9d5ada2bfef32283e35e5cf9407440686',
    'dependentFiles' => 
    array (
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/GenerateCharacteristicColorCssCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Characteristics\\GenerateCharacteristicColorCssCommand',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'generator',
               'type' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ImportLegacyCapabilitiesCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Importe les capacités depuis un export JSON PHPMyAdmin de l\'ancienne base.
 *
 * Format supporté : export JSON du plugin "Export to JSON" pour PHPMyAdmin
 * (array racine avec objets type=header|database|table).
 *
 * Mapping ancien → nouveau :
 * - id, uniqid, timestamp_* : non conservés (nouveaux IDs auto)
 * - usable "1" → state "playable", usable "0" → state "draft"
 * - poéditables "0"/"1" → po_editable bool
 * - Valeurs par défaut pour read_level, write_level, created_by
 *
 * @example
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --dry-run
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --force-update
 */',
         'namespace' => 'App\\Console\\Commands\\Characteristics',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'capability' => 'App\\Models\\Entity\\Capability',
          'elementconstants' => 'App\\Support\\ElementConstants',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ValidateCreatureFormulaPlaceholdersCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Characteristics\\ValidateCreatureFormulaPlaceholdersCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Valide les placeholders `[id]` des définitions JSON créature (seed) contre une liste blanche.
 *
 * @example php artisan characteristics:validate-creature-formula-placeholders
 */',
         'namespace' => 'App\\Console\\Commands\\Characteristics',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'creatureformulaplaceholdervalidator' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'validator',
               'type' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Lance des vérifications locales (tests, analyse statique, audit, doc) et produit un rapport Markdown
 * + des prompts prêts à coller dans Cursor pour analyse / correctifs « agent ».
 *
 * @example php artisan dev:review tests
 * @example php artisan dev:review quality
 * @example php artisan dev:review all --fix-pint
 * @example php artisan dev:review security --no-cursor-prompts
 * @example php artisan dev:review all --cursor-agent
 *
 * @see docs/10-BestPractices/SECURITY_PRACTICES.md
 * @see docs/10-BestPractices/TESTING_PRACTICES.md
 */',
         'namespace' => 'App\\Console\\Commands\\Dev',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'command' => 'Illuminate\\Console\\Command',
          'file' => 'Illuminate\\Support\\Facades\\File',
          'process' => 'Illuminate\\Support\\Facades\\Process',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Development/GenerateTestCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Development\\GenerateTestCommand',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
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
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'generateTestContent',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'name',
               'type' => NULL,
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Development/LoadDevelopmentServersCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Development\\LoadDevelopmentServersCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Alias de confort vers `project:dev` (prepare + optimize + serveurs Laravel + Vite).
 *
 * Pour lancer aussi la file d’attente et le CSS en parallèle : `composer run dev` (voir composer.json).
 */',
         'namespace' => 'App\\Console\\Commands\\Development',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Development/PrepareProjectCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Development\\PrepareProjectCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @deprecated Utiliser `php artisan project:prepare` (ou `project:dev` qui l’enchaîne).
 * Conservé comme alias pour scripts / habitudes locales.
 */',
         'namespace' => 'App\\Console\\Commands\\Development',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Effects/EffectsRebuildSignaturesCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Recalcule config_signature pour tous les degrés d’effet.
 */',
         'namespace' => 'App\\Console\\Commands\\Effects',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'effectdegree' => 'App\\Models\\EffectDegree',
          'integrationservice' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'integrationService',
               'type' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Media/CleanThumbnailsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Media\\CleanThumbnailsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Nettoie les fichiers du répertoire « thumbnails » géré par {@see ImageService} (hors conversions Spatie).
 *
 * Planifié quotidiennement dans {@see \\App\\Console\\Kernel::schedule} : doit pouvoir s’exécuter en production.
 */',
         'namespace' => 'App\\Console\\Commands\\Media',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'imageservice' => 'App\\Services\\ImageService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'imageService',
               'type' => 'App\\Services\\ImageService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/PagesImportRulesTocCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Pages\\PagesImportRulesTocCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Importe la hiérarchie des règles depuis une table des matières Markdown.
 *
 * Mapping appliqué:
 * - Niveau 1 (##) => page parente
 * - Niveau 2 (###) => sous-page (enfant du niveau 1)
 * - Niveau 3 (liste - x.x.x) => section texte de la page niveau 2
 */',
         'namespace' => 'App\\Console\\Commands\\Pages',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'sectiontype' => 'App\\Enums\\SectionType',
          'page' => 'App\\Models\\Page',
          'section' => 'App\\Models\\Section',
          'user' => 'App\\Models\\User',
          'rulesimportslughelper' => 'App\\Support\\Cms\\RulesImportSlugHelper',
          'rulesmarkdowncharacteristickrefautowrap' => 'App\\Support\\Cms\\RulesMarkdownCharacteristicKrefAutowrap',
          'rulesmarkdowninternalruleslinktopagekref' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
          'rulestocparser' => 'App\\Support\\Cms\\RulesTocParser',
          'rulestocslugindex' => 'App\\Support\\Cms\\RulesTocSlugIndex',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'str' => 'Illuminate\\Support\\Str',
          'recursivedirectoryiterator' => 'RecursiveDirectoryIterator',
          'recursiveiteratoriterator' => 'RecursiveIteratorIterator',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Pages/RulesInjectCharacteristicKrefsInMarkdownCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Insère dans les Markdown des règles les shortcodes {@code [[kref:characteristic:…]]} selon le catalogue partagé.
 *
 * @example php artisan pages:rules-inject-characteristic-krefs --dry-run
 * @example php artisan pages:rules-inject-characteristic-krefs
 */',
         'namespace' => 'App\\Console\\Commands\\Pages',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'rulescharacteristickrefreplacementcatalog' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
          'command' => 'Illuminate\\Console\\Command',
          'recursivedirectoryiterator' => 'RecursiveDirectoryIterator',
          'recursiveiteratoriterator' => 'RecursiveIteratorIterator',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Privacy/ProcessPrivacyDeletionRequestsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Privacy\\ProcessPrivacyDeletionRequestsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Traite les demandes de suppression RGPD dont le délai de rétractation est passé.
 *
 * Planifié quotidiennement (ex: 02:00).
 */',
         'namespace' => 'App\\Console\\Commands\\Privacy',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'executeusererasurejob' => 'App\\Jobs\\ExecuteUserErasureJob',
          'datasubjectrequest' => 'App\\Models\\DataSubjectRequest',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Sauvegarde MySQL/MariaDB/SQLite + archive compressée de storage/app (hors backups), rotation ~1 mois.
 *
 * @example php artisan project:backup
 * @example php artisan project:backup --no-storage
 * @example php artisan project:backup --retention-days=14
 * @example php artisan project:backup --prune-only --dry-run
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'projectbackupservice' => 'App\\Services\\Project\\ProjectBackupService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectClearCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectClearCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Nettoyages caches / vues / queues ({@see ProjectRunService}). L’option --test retire uniquement les artefacts PHPUnit / coverage / storage testing.
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Point d’entrée unique pour les flux « données DofusDB » (sync / init catalogue / complétion).
 *
 * Délègue aux commandes existantes pour rester DRY.
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'normalizesprojectsyncentities' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataImportRulesTocCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Point d’entrée « données projet » pour l’import de la table des matières des règles.
 *
 * Délègue à `pages:import-rules-toc` pour garder une seule implémentation tout en exposant
 * le flux sous le namespace `project:data:*` (cohérent avec la doc domaine « données »).
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDepsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectDepsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Met à jour les dépendances du projet (Composer, pnpm), optionnellement la stack système,
 * puis enchaîne {@see ProjectOptimizeCommand} lorsque l’on utilise le mode « tout ».
 *
 * @example php artisan project:deps
 * @example php artisan project:deps --with-system
 * @example php artisan project:deps --composer --pnpm --optimize
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDevCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectDevCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Environnement de développement : project:prepare et project:optimize par défaut, puis serveurs PHP + Vite.
 *
 * @example php artisan project:dev
 * @example php artisan project:dev --no-prepare --no-optimize
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectEffectsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectEffectsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Quality gate et pipeline effets de sorts (via {@see ProjectRunService}).
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectFixPermissionsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectFixPermissionsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Corrige propriétaires / permissions du dépôt ({@see ProjectRunService}).
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Initialisation complète du projet : migrations, seeders, import règles, capacités (fichier local),
 * puis types et scrapping DofusDB (appels réseau en fin de pipeline).
 *
 * Phase seeders : `scrapping:setup` (types + caractéristiques + mappings), comptes/pages,
 * {@see SubEffectSeeder}, référentiels langues / conditions / traits, pages « Création »,
 * import legacy spécialisations (fichiers HTML optionnels), puis import TOC règles.
 * Capacités : commande `capabilities:import-legacy` sur `database/seeders/data/capability.json`.
 *
 * Les appels réseau vers DofusDB (`scrapping:types:seed`, `scrapping:races:seed`, `scrapping:run`)
 * sont exécutés en fin de pipeline : tu peux interrompre l’init après seeders / capacités
 * et garder une base utilisable pour les tests (`--skip-types`, `--skip-scrapping`).
 *
 * Transforme une base vide en un projet fonctionnel. Compatible exécution longue
 * (`set_time_limit(0)`, `DB::reconnect` entre phases). Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:init
 * @example php artisan project:init --fresh --noimage
 * @example php artisan project:init --skip-scrapping --entity=monster
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'normalizesprojectsyncentities' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
          'promptsprimarysuperadmin' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
          'notificationservice' => 'App\\Services\\NotificationService',
          'creationpagesseeder' => 'Database\\Seeders\\CreationPagesSeeder',
          'criticalpagesseeder' => 'Database\\Seeders\\CriticalPagesSeeder',
          'conditionseeder' => 'Database\\Seeders\\Entity\\ConditionSeeder',
          'creaturetraitseeder' => 'Database\\Seeders\\Entity\\CreatureTraitSeeder',
          'languageseeder' => 'Database\\Seeders\\Entity\\LanguageSeeder',
          'specializationseeder' => 'Database\\Seeders\\Entity\\SpecializationSeeder',
          'navmenuseeder' => 'Database\\Seeders\\NavMenuSeeder',
          'pageseeder' => 'Database\\Seeders\\PageSeeder',
          'sectionseeder' => 'Database\\Seeders\\SectionSeeder',
          'subeffectseeder' => 'Database\\Seeders\\SubEffectSeeder',
          'spelltypeseeder' => 'Database\\Seeders\\Type\\SpellTypeSeeder',
          'userseeder' => 'Database\\Seeders\\UserSeeder',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
          'config' => 'Illuminate\\Support\\Facades\\Config',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        1 => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectOptimizeCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Optimisation locale : optimize:clear, IDE Helper, dump-autoload, optimize.
 *
 * @example php artisan project:optimize
 * @example php artisan project:optimize --ide-only
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectPrepareCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Prépare l’environnement de dev : rebuild CSS, caches vues, documentation, migrations.
 *
 * @example php artisan project:prepare
 * @example php artisan project:prepare --clear
 * @example php artisan project:prepare --dev
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectRefreshCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Réinstallation lourde : dépendances, base vide, caches. À utiliser en local uniquement.
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectResetCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectResetCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Réinitialisations lourdes ({@see ProjectRunService}).
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'guardsproductionenvironment' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
          'projectrunservice' => 'App\\Services\\Project\\ProjectRunService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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
               'name' => 'projectRunService',
               'type' => 'App\\Services\\Project\\ProjectRunService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectReviewCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Alias « projet » de {@see \\App\\Console\\Commands\\Dev\\DevReviewCommand} : même rapport Markdown
 * (tests, qualité, sécurité, doc) pour le fournir à un agent Cursor.
 *
 * @example php artisan project:review
 * @example php artisan project:review tests --report-path=storage/app/dev-reports/last-review.md
 * @example php artisan review quality
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectSuperAdminCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectSuperAdminCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Crée le premier super_admin interactif (hors flux `project:init`).
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'promptsprimarysuperadmin' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Mise à jour des entités en base dont auto_update = true.
 *
 * Ne crée pas de nouvelles entités ; met à jour uniquement celles déjà présentes
 * et marquées pour mise à jour automatique.
 *
 * Compatible exécution longue (set_time_limit(0), DB::reconnect entre chunks).
 * Vide la queue avant l\'update. Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:data:sync
 * @example php artisan project:update
 * @example php artisan project:data:sync --entity=monster --dry-run
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'normalizesprojectsyncentities' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
          'breed' => 'App\\Models\\Entity\\Breed',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'item' => 'App\\Models\\Entity\\Item',
          'monster' => 'App\\Models\\Entity\\Monster',
          'panoply' => 'App\\Models\\Entity\\Panoply',
          'resource' => 'App\\Models\\Entity\\Resource',
          'spell' => 'App\\Models\\Entity\\Spell',
          'notificationservice' => 'App\\Services\\NotificationService',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
          'config' => 'Illuminate\\Support\\Facades\\Config',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'schema' => 'Illuminate\\Support\\Facades\\Schema',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Project\\SetupCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Setup du projet : paquets apt, mises à jour, base de données, nettoyage, réinstallation.
 *
 * Utilise .env pour la BDD (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).
 *
 * @example
 * php artisan setup --install
 * php artisan setup --update
 * php artisan setup --db
 * php artisan setup --db --no-seed
 * php artisan setup --clean
 * php artisan setup --refresh
 * php artisan setup --install --db
 */',
         'namespace' => 'App\\Console\\Commands\\Project',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'pdo' => 'PDO',
          'pdoexception' => 'PDOException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsAutreAuditCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Audit spécifique des sous-effets "autre" sur les sorts importés.
 *
 * Objectif:
 * - Mesurer la part de "autre" dans les sous-effets de sorts.
 * - Identifier les "autre" probablement convertibles (retrait/placement/soin/dégâts/etc.).
 * - Donner un top actionnable (effectId DofusDB quand disponible, textes normalisés).
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsBackfillCharacteristicsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsBackfillCharacteristicsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Backfill characteristic_key pour les mappings d\'effets DofusDB en source "characteristic".
 *
 * Stratégie:
 * - cible les lignes dofusdb_effect_mappings avec characteristic_source=characteristic et characteristic_key vide,
 * - lit characteristic depuis GET /effects/{id},
 * - résout characteristic_key via la BDD des caractéristiques (groupe spell),
 * - fallback sur la config JSON dofusdb_characteristic_to_krosmoz_spell.json,
 * - met à jour la BDD (ou affiche uniquement avec --dry-run).
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbeffectmapping' => 'App\\Models\\DofusdbEffectMapping',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'dofusdbeffectmappingservice' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
          'dofusdbclient' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'client',
               'type' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'characteristicGetter',
               'type' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mappingService',
               'type' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMapCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMapCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Récupère la liste des effets depuis l’API DofusDB et propose des mappings vers les sous-effets Krosmoz.
 *
 * Sortie : tableau PHP (effectId => [sub_effect_slug, characteristic_source, characteristic_key])
 * à coller dans DofusdbEffectMappingSeeder::MAPPINGS ou à écrire dans un fichier.
 *
 * Usage :
 *   php artisan scrapping:effects:map
 *   php artisan scrapping:effects:map --output=database/seeders/data/dofusdb_effect_mappings.php
 *   php artisan scrapping:effects:map --lang=fr --no-cache
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbclient' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'client',
               'type' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMissingCharacteristicsReportCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Rapport des mappings d\'effets incomplets (characteristic_source=characteristic, key manquante).
 *
 * Produit un regroupement par characteristic DofusDB (GET /effects/{id}.characteristic),
 * trié par fréquence, pour prioriser les prochains ajouts de conversion.
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbeffectmapping' => 'App\\Models\\DofusdbEffectMapping',
          'characteristicgetterservice' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
          'dofusdbclient' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'client',
               'type' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'characteristicGetter',
               'type' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsPipelineCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Pipeline prêt à l\'emploi: import batch de sorts puis quality gate des effets.
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityAuditCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Audit qualité des effets de sorts: couverture mapping + qualité value_converted.
 *
 * Objectif:
 * - Identifier les mappings incomplets (source=characteristic sans characteristic_key).
 * - Mesurer les sous-effets de sorts qui devraient avoir value_converted mais ne l\'ont pas.
 *
 * Commande orientée robustesse: sortie compacte, JSON optionnel, scan en chunks.
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbeffectmapping' => 'App\\Models\\DofusdbEffectMapping',
          'spelleffectconversionformularesolver' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formulaResolver',
               'type' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityGateCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Quality gate CI pour bloquer un import massif d\'effets de sorts si les seuils ne sont pas atteints.
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbeffectmapping' => 'App\\Models\\DofusdbEffectMapping',
          'spelleffectconversionformularesolver' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'formulaResolver',
               'type' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRacesSeedCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Récupère les races de monstres depuis l\'API DofusDB et les synchronise en base.
 *
 * Crée ou met à jour les entrées monster_races avec dofusdb_race_id et name.
 * Les races existantes ne sont pas réinitialisées (state, read_level, write_level préservés).
 *
 * @example php artisan scrapping:races:seed
 * @example php artisan scrapping:races:seed --lang=fr --skip-cache
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'monsterrace' => 'App\\Models\\Type\\MonsterRace',
          'user' => 'App\\Models\\User',
          'dofusdbmonsterracescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
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
               'name' => 'catalogService',
               'type' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Corrige les items mal routés (items -> resources/consumables) à partir du type DofusDB.
 *
 * Cette commande sert de rattrapage des données historiques :
 * - détecte les lignes de `items` dont le type cible attendu n\'est pas `items`
 * - migre vers `resources` ou `consumables`
 * - transfère les pivots principaux (drops + recettes)
 * - réaffecte media/effect_usages puis supprime la ligne source
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'consumable' => 'App\\Models\\Entity\\Consumable',
          'item' => 'App\\Models\\Entity\\Item',
          'resource' => 'App\\Models\\Entity\\Resource',
          'consumabletype' => 'App\\Models\\Type\\ConsumableType',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'command' => 'Illuminate\\Console\\Command',
          'db' => 'Illuminate\\Support\\Facades\\DB',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Commande unique pour le scrapping (récupération + import).
 *
 * Par défaut : récupère les données, télécharge les images et importe en base.
 * --simulate : récupère et convertit sans écrire en base.
 * --noimage : désactive le téléchargement des images (on stocke l\'URL DofusDB telle quelle).
 * --entity peut contenir plusieurs entités (virgules) pour enchaîner les imports.
 *
 * @example
 * php artisan scrapping:run --entity=monster --id=31
 * php artisan scrapping:run --entity=monster,item --levelMin=1 --levelMax=50 --simulate
 * php artisan scrapping:run --entity=resource --type-name=Ressource --limit=100
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'resource' => 'App\\Models\\Entity\\Resource',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'dofusdbmonsterracescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
          'collectservice' => 'App\\Services\\Scrapping\\Core\\Collect\\CollectService',
          'configloader' => 'App\\Services\\Scrapping\\Core\\Config\\ConfigLoader',
          'integrationservice' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
          'orchestrator' => 'App\\Services\\Scrapping\\Core\\Orchestrator\\Orchestrator',
          'scrappingpreviewbuilder' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
          'command' => 'Illuminate\\Console\\Command',
          'str' => 'Illuminate\\Support\\Str',
          'progressbar' => 'Symfony\\Component\\Console\\Helper\\ProgressBar',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'collectService',
               'type' => 'App\\Services\\Scrapping\\Core\\Collect\\CollectService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'orchestrator',
               'type' => 'App\\Services\\Scrapping\\Core\\Orchestrator\\Orchestrator',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'integrationService',
               'type' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Exporte les données de la BDD vers database/seeders/data/ pour que les seeders
 * utilisent ces fichiers comme source (au lieu de config/). Les caractéristiques
 * sont exportées en JSON (`characteristic-definitions/{groupe}/*.json`).
 *
 * À lancer après modification des caractéristiques / formules / types d\'effets via l\'interface.
 * Crée une sauvegarde ZIP des fichiers existants avant export, puis nettoie les backups > 7 ou > 7 jours.
 *
 * Disponible uniquement en environnement local et testing (désactivé en production pour limiter la surface d\'attaque).
 */',
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
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
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
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSetupCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Bootstrap du socle scrapping (migrations + seeders essentiels).
 *
 * Cette commande initialise les données indispensables au pipeline:
 * caractéristiques, mappings DofusDB, mappings scrapping par entité.
 *
 * @example php artisan scrapping:setup
 * @example php artisan scrapping:setup --fresh
 * @example php artisan scrapping:setup --skip-migrate
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'characteristicseeder' => 'Database\\Seeders\\CharacteristicSeeder',
          'creaturecharacteristicseeder' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
          'dofusdbcharacteristicidseeder' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
          'dofusdbeffectmappingseeder' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
          'objectcharacteristicseeder' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
          'scrappingentitymappingcharacteristicseeder' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
          'scrappingentitymappingseeder' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
          'spellcharacteristicseeder' => 'Database\\Seeders\\SpellCharacteristicSeeder',
          'spelleffecttypeseeder' => 'Database\\Seeders\\SpellEffectTypeSeeder',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesExtractCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesExtractCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Extrait les types d\'items DofusDB (catalogue + item-super-types.json)
 * vers database/seeders/data/ (resource_types.php, consumable_types.php, item_types.php).
 *
 * Phase 3 du plan types item BDD / seeders. À lancer une fois pour initialiser les fichiers,
 * puis utiliser scrapping:seeders:export --item-types après réglages en UI.
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
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
               'name' => 'catalogService',
               'type' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mappingService',
               'type' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Migre les item_type_id des équipements existants vers les superTypes.
 *
 * Avant : item_types stockait des typeIds (sous-types : Arc, Baguette, Marteau).
 * Après : item_types stocke des superTypeIds (types : Amulette, Arme, Bouclier).
 *
 * Cette commande met à jour les items existants pour pointer vers le bon ItemType (superTypeId).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PROPRIETES_ITEMS_RESOURCES_CONSUMABLES.md
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'item' => 'App\\Models\\Entity\\Item',
          'itemtype' => 'App\\Models\\Type\\ItemType',
          'dofusdbitemsupertypemappingservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
          'dofusdbitemtypescatalogservice' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
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
               'name' => 'catalogService',
               'type' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'mappingService',
               'type' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
  '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesSeedCommand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Remplit les tables resource_types, consumable_types, item_types depuis l’API DofusDB.
 *
 * Une seule commande : récupère tous les item-types via l’API (superTypeId → Ressource / Consommable / Équipement),
 * écrit les fichiers database/seeders/data/*.php puis exécute les 3 seeders pour synchroniser la BDD.
 * Aucun type n’est oublié : la classification repose sur l’API (https://api.dofusdb.fr/item-types).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */',
         'namespace' => 'App\\Console\\Commands\\Scrapping',
         'uses' => 
        array (
          'symfonycommand' => 'Symfony\\Component\\Console\\Command\\Command',
          'command' => 'Illuminate\\Console\\Command',
          'artisan' => 'Illuminate\\Support\\Facades\\Artisan',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
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
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'aliases',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
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
