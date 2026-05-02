<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Section.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Section
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c6a62c95f7d2557d9da437d4126ee8d6cfc109cc36567773629a592f72f365c8-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Section',
        'filename' => '/var/www/KrosmozJdr/app/Models/Section.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Section',
    'shortName' => 'Section',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Modèle Eloquent Section
 *
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l\'ordre, le type, les paramètres dynamiques, la visibilité, l\'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d\'accès.
 *
 * Relations : page, users, createdBy ; médias via Media Library (collection files)
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $slug
 * @property int $order
 * @property SectionType $template
 * @property array<array-key, mixed>|null $settings
 * @property array<array-key, mixed>|null $data
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Page $page
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \\Database\\Factories\\SectionFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section wherePageId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereTitle($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereTemplate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereSettings($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereData($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Section withoutTrashed()
 * @property SectionType|null $type
 * @property array<array-key, mixed>|null $params
 * @method static Builder<static>|Section displayable(?\\App\\Models\\User $user = null)
 * @method static Builder<static>|Section ordered()
 * @method static Builder<static>|Section playable()
 * @method static Builder<static>|Section published()
 * @method static Builder<static>|Section readableFor(?\\App\\Models\\User $user = null)
 * @method static Builder<static>|Section whereParams($value)
 * @method static Builder<static>|Section whereReadLevel($value)
 * @method static Builder<static>|Section whereType($value)
 * @method static Builder<static>|Section whereWriteLevel($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 83,
    'endLine' => 414,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
      2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
      3 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 117,
            'startFilePos' => 4517,
            'endTokenPos' => 117,
            'endFilePos' => 4521,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 128,
            'startFilePos' => 4556,
            'endTokenPos' => 128,
            'endFilePos' => 4562,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 139,
            'startFilePos' => 4600,
            'endTokenPos' => 139,
            'endFilePos' => 4609,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 150,
            'startFilePos' => 4647,
            'endTokenPos' => 150,
            'endFilePos' => 4656,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sections/files\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 163,
            'startFilePos' => 4744,
            'endTokenPos' => 163,
            'endFilePos' => 4759,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
      'MEDIA_FILE_PATTERN_FILES' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'MEDIA_FILE_PATTERN_FILES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'[id]-[date]-[uniqid]\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 176,
            'startFilePos' => 4908,
            'endTokenPos' => 176,
            'endFilePos' => 4929,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection files (placeholders: [name], [date], [id], [uniqid]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 67,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'page_id\', \'title\', \'slug\', \'order\', \'template\', \'type\', \'settings\', \'data\', \'params\', \'state\', \'read_level\', \'write_level\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 121,
            'startTokenPos' => 187,
            'startFilePos' => 5055,
            'endTokenPos' => 228,
            'endFilePos' => 5304,
          ),
        ),
        'docComment' => '/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'order\' => \'integer\', \'template\' => \\App\\Enums\\SectionType::class, \'type\' => \\App\\Enums\\SectionType::class, \'settings\' => \'array\', \'data\' => \'array\', \'params\' => \'array\', \'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 128,
            'endLine' => 137,
            'startTokenPos' => 239,
            'startFilePos' => 5431,
            'endTokenPos' => 301,
            'endFilePos' => 5705,
          ),
        ),
        'docComment' => '/**
 * The attributes that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 128,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'createdBy' => 
      array (
        'name' => 'createdBy',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the user that created the section.
 */',
        'startLine' => 142,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'page' => 
      array (
        'name' => 'page',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the page that owns the section.
 */',
        'startLine' => 150,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'users' => 
      array (
        'name' => 'users',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les utilisateurs associés à cette section.
 */',
        'startLine' => 158,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'registerMediaConversions' => 
      array (
        'name' => 'registerMediaConversions',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 169,
                'endLine' => 169,
                'startTokenPos' => 415,
                'startFilePos' => 6606,
                'endTokenPos' => 415,
                'endFilePos' => 6609,
              ),
            ),
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
                      'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
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
            'startLine' => 169,
            'endLine' => 169,
            'startColumn' => 46,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enregistre les conversions média (WebP + miniature) pour la collection "files".
 * Les images sont converties en WebP pour réduire la taille et servies en miniature.
 *
 * @see https://spatie.be/docs/laravel-medialibrary/v11/converting-images/defining-conversions
 */',
        'startLine' => 169,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'scopePlayable' => 
      array (
        'name' => 'scopePlayable',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 35,
            'endColumn' => 48,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param Builder<Section> $query @return Builder<Section> */',
        'startLine' => 197,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'scopePublished' => 
      array (
        'name' => 'scopePublished',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 36,
            'endColumn' => 49,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param Builder<Section> $query @return Builder<Section> */',
        'startLine' => 206,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'scopeReadableFor' => 
      array (
        'name' => 'scopeReadableFor',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 215,
            'endLine' => 215,
            'startColumn' => 38,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'user' => 
          array (
            'name' => 'user',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 215,
                'endLine' => 215,
                'startTokenPos' => 606,
                'startFilePos' => 7989,
                'endTokenPos' => 606,
                'endFilePos' => 7992,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
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
            'startLine' => 215,
            'endLine' => 215,
            'startColumn' => 54,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param Builder<Section> $query @return Builder<Section> */',
        'startLine' => 215,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'scopeOrdered' => 
      array (
        'name' => 'scopeOrdered',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 34,
            'endColumn' => 47,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param Builder<Section> $query @return Builder<Section> */',
        'startLine' => 234,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'scopeDisplayable' => 
      array (
        'name' => 'scopeDisplayable',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 38,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'user' => 
          array (
            'name' => 'user',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 243,
                'endLine' => 243,
                'startTokenPos' => 780,
                'startFilePos' => 8877,
                'endTokenPos' => 780,
                'endFilePos' => 8880,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 54,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param Builder<Section> $query @return Builder<Section> */',
        'startLine' => 243,
        'endLine' => 248,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'isPlayable' => 
      array (
        'name' => 'isPlayable',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si la section est publiée.
 */',
        'startLine' => 257,
        'endLine' => 260,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'isReadableFor' => 
      array (
        'name' => 'isReadableFor',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 265,
                'endLine' => 265,
                'startTokenPos' => 861,
                'startFilePos' => 9424,
                'endTokenPos' => 861,
                'endFilePos' => 9427,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
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
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 35,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si la section est lisible pour un utilisateur.
 */',
        'startLine' => 265,
        'endLine' => 285,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'canBeViewedBy' => 
      array (
        'name' => 'canBeViewedBy',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 290,
                'endLine' => 290,
                'startTokenPos' => 1018,
                'startFilePos' => 10089,
                'endTokenPos' => 1018,
                'endFilePos' => 10092,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
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
            'startLine' => 290,
            'endLine' => 290,
            'startColumn' => 35,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si la section peut être vue par un utilisateur (état + visibilité).
 */',
        'startLine' => 290,
        'endLine' => 308,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'publish' => 
      array (
        'name' => 'publish',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Publie la section.
 */',
        'startLine' => 313,
        'endLine' => 316,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'archive' => 
      array (
        'name' => 'archive',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Archive la section.
 */',
        'startLine' => 321,
        'endLine' => 324,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
        'aliasName' => NULL,
      ),
      'canBeEditedBy' => 
      array (
        'name' => 'canBeEditedBy',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 341,
                'endLine' => 341,
                'startTokenPos' => 1193,
                'startFilePos' => 11761,
                'endTokenPos' => 1193,
                'endFilePos' => 11764,
              ),
            ),
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
                      'name' => 'App\\Models\\User',
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
            'startLine' => 341,
            'endLine' => 341,
            'startColumn' => 35,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si la section peut être modifiée par un utilisateur selon write_level,
 * et en respectant aussi les droits d\'écriture sur la page parente.
 *
 * **Logique de vérification :**
 * - Les super_admin peuvent toujours modifier
 * - L\'auteur de la section peut modifier sa section (même sans niveau de permission)
 *   → Il ne peut modifier que sa propre section, pas les autres sections de la page
 * - Les utilisateurs associés à la section peuvent modifier (mais doivent avoir les droits sur la page)
 * - Sinon : l\'utilisateur doit avoir un niveau \\(\\ge\\) `write_level` de la section
 *   ET pouvoir modifier la page parente (selon ses propres règles d\'écriture)
 *
 * @param  User|null  $user  Utilisateur (null pour invité)
 * @return bool True si l\'utilisateur peut modifier la section
 */',
        'startLine' => 341,
        'endLine' => 413,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Section',
        'implementingClassName' => 'App\\Models\\Section',
        'currentClassName' => 'App\\Models\\Section',
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