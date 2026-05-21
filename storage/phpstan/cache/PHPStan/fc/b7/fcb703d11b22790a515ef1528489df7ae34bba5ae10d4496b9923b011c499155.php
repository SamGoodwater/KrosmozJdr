<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Notifications/DatabaseNotification.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Notifications\DatabaseNotification
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-db4e66ce5a01dd085ae646af07c1fbff81db443f1be3a47cbc732c7741d15b48-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Notifications\\DatabaseNotification',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Notifications/DatabaseNotification.php',
      ),
    ),
    'namespace' => 'Illuminate\\Notifications',
    'name' => 'Illuminate\\Notifications\\DatabaseNotification',
    'shortName' => 'DatabaseNotification',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 132,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\HasCollection',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'keyType' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'string\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 47,
            'startFilePos' => 412,
            'endTokenPos' => 47,
            'endFilePos' => 419,
          ),
        ),
        'docComment' => '/**
 * The "type" of the primary key ID.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'incrementing' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 58,
            'startFilePos' => 541,
            'endTokenPos' => 58,
            'endFilePos' => 545,
          ),
        ),
        'docComment' => '/**
 * Indicates if the IDs are auto-incrementing.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'notifications\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 69,
            'startFilePos' => 658,
            'endTokenPos' => 69,
            'endFilePos' => 672,
          ),
        ),
        'docComment' => '/**
 * The table associated with the model.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'guarded' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'guarded',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 80,
            'startFilePos' => 786,
            'endTokenPos' => 81,
            'endFilePos' => 787,
          ),
        ),
        'docComment' => '/**
 * The guarded attributes on the model.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'data\' => \'array\', \'read_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 50,
            'startTokenPos' => 92,
            'startFilePos' => 914,
            'endTokenPos' => 108,
            'endFilePos' => 980,
          ),
        ),
        'docComment' => '/**
 * The attributes that should be cast to native types.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'collectionClass' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'name' => 'collectionClass',
        'modifiers' => 18,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\\Illuminate\\Notifications\\DatabaseNotificationCollection::class',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 123,
            'startFilePos' => 1112,
            'endTokenPos' => 125,
            'endFilePos' => 1148,
          ),
        ),
        'docComment' => '/**
 * The type of collection that should be used for the model.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 85,
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
      'notifiable' => 
      array (
        'name' => 'notifiable',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the notifiable entity that the notification belongs to.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Relations\\MorphTo<\\Illuminate\\Database\\Eloquent\\Model, $this>
 */',
        'startLine' => 62,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'markAsRead' => 
      array (
        'name' => 'markAsRead',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the notification as read.
 *
 * @return void
 */',
        'startLine' => 72,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'markAsUnread' => 
      array (
        'name' => 'markAsUnread',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the notification as unread.
 *
 * @return void
 */',
        'startLine' => 84,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'read' => 
      array (
        'name' => 'read',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if a notification has been read.
 *
 * @return bool
 */',
        'startLine' => 96,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'unread' => 
      array (
        'name' => 'unread',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if a notification has not been read.
 *
 * @return bool
 */',
        'startLine' => 106,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'scopeRead' => 
      array (
        'name' => 'scopeRead',
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
            'startLine' => 117,
            'endLine' => 117,
            'startColumn' => 31,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope a query to only include read notifications.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 117,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'aliasName' => NULL,
      ),
      'scopeUnread' => 
      array (
        'name' => 'scopeUnread',
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
            'startLine' => 128,
            'endLine' => 128,
            'startColumn' => 33,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope a query to only include unread notifications.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<static>  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder<static>
 */',
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'implementingClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
        'currentClassName' => 'Illuminate\\Notifications\\DatabaseNotification',
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