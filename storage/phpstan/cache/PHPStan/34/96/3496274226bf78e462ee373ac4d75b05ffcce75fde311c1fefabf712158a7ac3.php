<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Support/Cms/RulesMarkdownInternalRulesLinkToPageKref.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Support\Cms\RulesMarkdownInternalRulesLinkToPageKref
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-37c73e2af8b6ea9f6b5f51894f702a674bfa729b478e346da9fba2bfb2733921-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'filename' => '/var/www/KrosmozJdr/app/Support/Cms/RulesMarkdownInternalRulesLinkToPageKref.php',
      ),
    ),
    'namespace' => 'App\\Support\\Cms',
    'name' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
    'shortName' => 'RulesMarkdownInternalRulesLinkToPageKref',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Remplace les liens Markdown relatifs vers des fichiers de règles {@code N[.N]+-titre.md}
 * par des shortcodes {@code [[kref:pageSection:pageSlug@sectionSlug|libellé]]} lorsque le
 * numéro de section est connu dans la TOC ; sinon {@code [[kref:page:slug|libellé]]}.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 78,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'LINK_PATTERN' => 
      array (
        'declaringClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'implementingClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'name' => 'LINK_PATTERN',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'/\\[([^\\]]*)\\]\\(([^)]+)\\)/u\'',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 25,
            'startFilePos' => 405,
            'endTokenPos' => 25,
            'endFilePos' => 432,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 62,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'apply' => 
      array (
        'name' => 'apply',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'currentMdAbsolutePath' => 
          array (
            'name' => 'currentMdAbsolutePath',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 37,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'rulesRootAbsolutePath' => 
          array (
            'name' => 'rulesRootAbsolutePath',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 9,
            'endColumn' => 37,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Support\\Cms\\RulesTocSlugIndex',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 14,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'implementingClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'currentClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'aliasName' => NULL,
      ),
      'parentLevel2NumberFromSectionNumber' => 
      array (
        'name' => 'parentLevel2NumberFromSectionNumber',
        'parameters' => 
        array (
          'number' => 
          array (
            'name' => 'number',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 64,
            'endColumn' => 77,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * {@code 3.2.4} → page parente {@code 3.2} ; {@code 2.5} → {@code 2.5}.
 */',
        'startLine' => 69,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'implementingClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'currentClassName' => 'App\\Support\\Cms\\RulesMarkdownInternalRulesLinkToPageKref',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));