<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Condition.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Condition
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ca7fd2f8516a0023894158d4327132bd0368241538545cc0d2c1f3a92875c1ed-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Condition',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Condition.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Condition',
    'shortName' => 'Condition',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Référentiel canonique des états/conditions de jeu.
 *
 * @property int $id
 * @property int|null $dofusdb_id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $icon
 * @property string|null $image
 * @property bool $dissipable
 * @property array<array-key, mixed>|null $raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read Collection<int, Spell> $spells
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 41,
    'endLine' => 63,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Concerns\\HasEntityImageMedia',
      1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 94,
            'startFilePos' => 1513,
            'endTokenPos' => 94,
            'endFilePos' => 1517,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 105,
            'startFilePos' => 1551,
            'endTokenPos' => 105,
            'endFilePos' => 1557,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 48,
            'startTokenPos' => 116,
            'startFilePos' => 1594,
            'endTokenPos' => 116,
            'endFilePos' => 1603,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 49,
            'startTokenPos' => 127,
            'startFilePos' => 1640,
            'endTokenPos' => 127,
            'endFilePos' => 1649,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/conditions\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 138,
            'startFilePos' => 1683,
            'endTokenPos' => 138,
            'endFilePos' => 1708,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 149,
            'startFilePos' => 1756,
            'endTokenPos' => 149,
            'endFilePos' => 1774,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_id\', \'name\', \'description\', \'state\', \'read_level\', \'write_level\', \'icon\', \'image\', \'prevents_spell_cast\', \'prevents_fight\', \'cant_be_moved\', \'cant_be_pushed\', \'cant_deal_damage\', \'invulnerable\', \'cant_switch_position\', \'incurable\', \'invulnerable_melee\', \'invulnerable_range\', \'cant_tackle\', \'cant_be_tackled\', \'display_turn_remaining\', \'is_main_state\', \'dissipable\', \'raw\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 160,
            'startFilePos' => 1833,
            'endTokenPos' => 210,
            'endFilePos' => 2205,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 400,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_id\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'prevents_spell_cast\' => \'boolean\', \'prevents_fight\' => \'boolean\', \'cant_be_moved\' => \'boolean\', \'cant_be_pushed\' => \'boolean\', \'cant_deal_damage\' => \'boolean\', \'invulnerable\' => \'boolean\', \'cant_switch_position\' => \'boolean\', \'incurable\' => \'boolean\', \'invulnerable_melee\' => \'boolean\', \'invulnerable_range\' => \'boolean\', \'cant_tackle\' => \'boolean\', \'cant_be_tackled\' => \'boolean\', \'display_turn_remaining\' => \'boolean\', \'is_main_state\' => \'boolean\', \'dissipable\' => \'boolean\', \'raw\' => \'array\']',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 221,
            'startFilePos' => 2270,
            'endTokenPos' => 297,
            'endFilePos' => 2792,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 547,
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
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 87,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
        'aliasName' => NULL,
      ),
      'creatures' => 
      array (
        'name' => 'creatures',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 103,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
        'aliasName' => NULL,
      ),
      'spells' => 
      array (
        'name' => 'spells',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 62,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 202,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
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