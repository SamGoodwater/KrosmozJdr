<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Concerns/HasMediaCustomNaming.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Concerns\HasMediaCustomNaming
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-4d2d30c927978dfb2f19ff2fd24a406d3c814153d432d2738f419461d5ba131c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'filename' => '/var/www/KrosmozJdr/app/Models/Concerns/HasMediaCustomNaming.php',
      ),
    ),
    'namespace' => 'App\\Models\\Concerns',
    'name' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
    'shortName' => 'HasMediaCustomNaming',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Trait optionnel pour les modèles HasMedia : nommage des fichiers via constantes.
 *
 * Constantes possibles sur le modèle :
 * - MEDIA_PATH : répertoire de stockage (ex: images/entity/breeds), lu par ModelAwarePathGenerator.
 * - MEDIA_FILE_PATTERN_{COLLECTION} : motif pour une collection (ex: MEDIA_FILE_PATTERN_IMAGES, MEDIA_FILE_PATTERN_ICONS).
 * - MEDIA_FILE_PATTERN : motif par défaut pour toutes les collections.
 *
 * Placeholders dans le motif : [name], [date], [id], [uniqid], [slug], [key].
 * Le nom final est passé dans Str::slug() pour éviter tout caractère inadapté au système de fichiers.
 * Exemple : \'breed-icon-[name]-[date]\' → breed-icon-eniripsa-2025-02-07
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 83,
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
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'getMediaFileNameForCollection' => 
      array (
        'name' => 'getMediaFileNameForCollection',
        'parameters' => 
        array (
          'collection' => 
          array (
            'name' => 'collection',
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 51,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'extension' => 
          array (
            'name' => 'extension',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 27,
                'endLine' => 27,
                'startTokenPos' => 39,
                'startFilePos' => 1230,
                'endTokenPos' => 39,
                'endFilePos' => 1231,
              ),
            ),
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 71,
            'endColumn' => 92,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'string',
                  'isIdentifier' => true,
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le nom de fichier pour une collection selon le motif défini, ou null pour garder le nom par défaut.
 *
 * @param  string  $collection  Nom de la collection (images, icons, files…)
 * @param  string  $extension  Extension à ajouter (ex: png). Laissé vide si le nom doit rester sans extension.
 */',
        'startLine' => 27,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Concerns',
        'declaringClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'implementingClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'currentClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'aliasName' => NULL,
      ),
      'getMediaFilePatternForCollection' => 
      array (
        'name' => 'getMediaFilePatternForCollection',
        'parameters' => 
        array (
          'collection' => 
          array (
            'name' => 'collection',
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
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 57,
            'endColumn' => 74,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'string',
                  'isIdentifier' => true,
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le motif de nommage pour la collection (constante MEDIA_FILE_PATTERN_* ou MEDIA_FILE_PATTERN).
 */',
        'startLine' => 71,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models\\Concerns',
        'declaringClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'implementingClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
        'currentClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
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