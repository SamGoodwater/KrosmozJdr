<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Section.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Section
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-95c8594fe7f28d5d6745584c1481819aeb4ceff4c91df93186d19165306b0dc5-8.4.17-6.70.0.1',
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
 * @property-read Pivot|null $pivot Présent lorsque la section est chargée via une relation pivot (ex. page ↔ sections).
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
    'startLine' => 85,
    'endLine' => 416,
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
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 122,
            'startFilePos' => 4692,
            'endTokenPos' => 122,
            'endFilePos' => 4696,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
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
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 133,
            'startFilePos' => 4731,
            'endTokenPos' => 133,
            'endFilePos' => 4737,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
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
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 144,
            'startFilePos' => 4775,
            'endTokenPos' => 144,
            'endFilePos' => 4784,
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
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 155,
            'startFilePos' => 4822,
            'endTokenPos' => 155,
            'endFilePos' => 4831,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
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
            'startLine' => 99,
            'endLine' => 99,
            'startTokenPos' => 168,
            'startFilePos' => 4919,
            'endTokenPos' => 168,
            'endFilePos' => 4934,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 99,
        'endLine' => 99,
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
            'startLine' => 102,
            'endLine' => 102,
            'startTokenPos' => 181,
            'startFilePos' => 5083,
            'endTokenPos' => 181,
            'endFilePos' => 5104,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection files (placeholders: [name], [date], [id], [uniqid]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
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
            'startLine' => 109,
            'endLine' => 123,
            'startTokenPos' => 192,
            'startFilePos' => 5230,
            'endTokenPos' => 233,
            'endFilePos' => 5479,
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
        'startLine' => 109,
        'endLine' => 123,
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
            'startLine' => 130,
            'endLine' => 139,
            'startTokenPos' => 244,
            'startFilePos' => 5606,
            'endTokenPos' => 306,
            'endFilePos' => 5880,
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
        'startLine' => 130,
        'endLine' => 139,
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
        'startLine' => 144,
        'endLine' => 147,
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
        'startLine' => 152,
        'endLine' => 155,
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
        'startLine' => 160,
        'endLine' => 163,
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
                'startLine' => 171,
                'endLine' => 171,
                'startTokenPos' => 420,
                'startFilePos' => 6781,
                'endTokenPos' => 420,
                'endFilePos' => 6784,
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
            'startLine' => 171,
            'endLine' => 171,
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
        'startLine' => 171,
        'endLine' => 189,
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
            'startLine' => 199,
            'endLine' => 199,
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
        'startLine' => 199,
        'endLine' => 202,
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
            'startLine' => 208,
            'endLine' => 208,
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
        'startLine' => 208,
        'endLine' => 211,
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
            'startLine' => 217,
            'endLine' => 217,
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
                'startLine' => 217,
                'endLine' => 217,
                'startTokenPos' => 611,
                'startFilePos' => 8164,
                'endTokenPos' => 611,
                'endFilePos' => 8167,
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
            'startLine' => 217,
            'endLine' => 217,
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
        'startLine' => 217,
        'endLine' => 230,
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
            'startLine' => 236,
            'endLine' => 236,
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
        'startLine' => 236,
        'endLine' => 239,
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
            'startLine' => 245,
            'endLine' => 245,
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
                'startLine' => 245,
                'endLine' => 245,
                'startTokenPos' => 785,
                'startFilePos' => 9052,
                'endTokenPos' => 785,
                'endFilePos' => 9055,
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
            'startLine' => 245,
            'endLine' => 245,
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
        'startLine' => 245,
        'endLine' => 250,
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
        'startLine' => 259,
        'endLine' => 262,
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
                'startLine' => 267,
                'endLine' => 267,
                'startTokenPos' => 866,
                'startFilePos' => 9599,
                'endTokenPos' => 866,
                'endFilePos' => 9602,
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
            'startLine' => 267,
            'endLine' => 267,
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
        'startLine' => 267,
        'endLine' => 287,
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
                'startLine' => 292,
                'endLine' => 292,
                'startTokenPos' => 1023,
                'startFilePos' => 10264,
                'endTokenPos' => 1023,
                'endFilePos' => 10267,
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
            'startLine' => 292,
            'endLine' => 292,
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
        'startLine' => 292,
        'endLine' => 310,
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
        'startLine' => 315,
        'endLine' => 318,
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
        'startLine' => 323,
        'endLine' => 326,
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
                'startLine' => 343,
                'endLine' => 343,
                'startTokenPos' => 1198,
                'startFilePos' => 11936,
                'endTokenPos' => 1198,
                'endFilePos' => 11939,
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
            'startLine' => 343,
            'endLine' => 343,
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
        'startLine' => 343,
        'endLine' => 415,
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