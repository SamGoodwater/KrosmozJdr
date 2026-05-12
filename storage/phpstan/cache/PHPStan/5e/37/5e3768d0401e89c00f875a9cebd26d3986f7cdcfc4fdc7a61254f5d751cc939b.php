<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/SpellEffectType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\SpellEffectType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9ab22bf3bc48e1de3655681b87d16c548c8c20071074d983bcdb98a11289a941-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\SpellEffectType',
        'filename' => '/var/www/KrosmozJdr/app/Models/SpellEffectType.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\SpellEffectType',
    'shortName' => 'SpellEffectType',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Type d\'effet de sort (référentiel).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property string $value_type
 * @property string|null $element
 * @property string|null $unit
 * @property bool $is_positive
 * @property int $sort_order
 * @property int|null $dofusdb_effect_id
 * @property Collection<int, SpellEffect> $spellEffects
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $spell_effects_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereCategory($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereDofusdbEffectId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereElement($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereIsPositive($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereSortOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereUnit($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffectType whereValueType($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 48,
    'endLine' => 197,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'VALUE_TYPE_FIXED' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'VALUE_TYPE_FIXED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fixed\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 137,
            'startFilePos' => 2920,
            'endTokenPos' => 137,
            'endFilePos' => 2926,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'VALUE_TYPE_DICE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'VALUE_TYPE_DICE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'dice\'',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 148,
            'startFilePos' => 2965,
            'endTokenPos' => 148,
            'endFilePos' => 2970,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'VALUE_TYPE_PERCENT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'VALUE_TYPE_PERCENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'percent\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 159,
            'startFilePos' => 3012,
            'endTokenPos' => 159,
            'endFilePos' => 3020,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'CATEGORY_DAMAGE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_DAMAGE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'damage\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 170,
            'startFilePos' => 3059,
            'endTokenPos' => 170,
            'endFilePos' => 3066,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'CATEGORY_HEAL' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_HEAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'heal\'',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 181,
            'startFilePos' => 3103,
            'endTokenPos' => 181,
            'endFilePos' => 3108,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'CATEGORY_SHIELD' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_SHIELD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'shield\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 192,
            'startFilePos' => 3147,
            'endTokenPos' => 192,
            'endFilePos' => 3154,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'CATEGORY_AP' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_AP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ap\'',
          'attributes' => 
          array (
            'startLine' => 83,
            'endLine' => 83,
            'startTokenPos' => 203,
            'startFilePos' => 3189,
            'endTokenPos' => 203,
            'endFilePos' => 3192,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 83,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'CATEGORY_PM' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_PM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pm\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 214,
            'startFilePos' => 3227,
            'endTokenPos' => 214,
            'endFilePos' => 3230,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'CATEGORY_RANGE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_RANGE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'range\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 225,
            'startFilePos' => 3268,
            'endTokenPos' => 225,
            'endFilePos' => 3274,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'CATEGORY_BUFF_STAT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_BUFF_STAT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'buff_stat\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 236,
            'startFilePos' => 3316,
            'endTokenPos' => 236,
            'endFilePos' => 3326,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'CATEGORY_DEBUFF_STAT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_DEBUFF_STAT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'debuff_stat\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 247,
            'startFilePos' => 3370,
            'endTokenPos' => 247,
            'endFilePos' => 3382,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'CATEGORY_BUFF_DAMAGE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_BUFF_DAMAGE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'buff_damage\'',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 258,
            'startFilePos' => 3426,
            'endTokenPos' => 258,
            'endFilePos' => 3438,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'CATEGORY_DEBUFF_DAMAGE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_DEBUFF_DAMAGE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'debuff_damage\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 269,
            'startFilePos' => 3484,
            'endTokenPos' => 269,
            'endFilePos' => 3498,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'CATEGORY_RESISTANCE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_RESISTANCE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'resistance\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 280,
            'startFilePos' => 3541,
            'endTokenPos' => 280,
            'endFilePos' => 3552,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'CATEGORY_STATE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_STATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'state\'',
          'attributes' => 
          array (
            'startLine' => 99,
            'endLine' => 99,
            'startTokenPos' => 291,
            'startFilePos' => 3590,
            'endTokenPos' => 291,
            'endFilePos' => 3596,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 99,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'CATEGORY_PLACEMENT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_PLACEMENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'placement\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 302,
            'startFilePos' => 3638,
            'endTokenPos' => 302,
            'endFilePos' => 3648,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'CATEGORY_TELEPORT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_TELEPORT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'teleport\'',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 313,
            'startFilePos' => 3689,
            'endTokenPos' => 313,
            'endFilePos' => 3698,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'CATEGORY_SUMMON' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_SUMMON',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'summon\'',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 324,
            'startFilePos' => 3737,
            'endTokenPos' => 324,
            'endFilePos' => 3744,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'CATEGORY_GLYPH_TRAP' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_GLYPH_TRAP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'glyph_trap\'',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 335,
            'startFilePos' => 3787,
            'endTokenPos' => 335,
            'endFilePos' => 3798,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'CATEGORY_ZONE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_ZONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'zone\'',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 346,
            'startFilePos' => 3835,
            'endTokenPos' => 346,
            'endFilePos' => 3840,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'CATEGORY_CRITICAL' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_CRITICAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'critical\'',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 111,
            'startTokenPos' => 357,
            'startFilePos' => 3881,
            'endTokenPos' => 357,
            'endFilePos' => 3890,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 111,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'CATEGORY_REFLECT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_REFLECT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'reflect\'',
          'attributes' => 
          array (
            'startLine' => 113,
            'endLine' => 113,
            'startTokenPos' => 368,
            'startFilePos' => 3930,
            'endTokenPos' => 368,
            'endFilePos' => 3938,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 113,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'CATEGORY_STEAL' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_STEAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'steal\'',
          'attributes' => 
          array (
            'startLine' => 115,
            'endLine' => 115,
            'startTokenPos' => 379,
            'startFilePos' => 3976,
            'endTokenPos' => 379,
            'endFilePos' => 3982,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 115,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'CATEGORY_DAMAGE_OVER_TIME' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_DAMAGE_OVER_TIME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'damage_over_time\'',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 117,
            'startTokenPos' => 390,
            'startFilePos' => 4031,
            'endTokenPos' => 390,
            'endFilePos' => 4048,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 64,
      ),
      'CATEGORY_HEAL_OVER_TIME' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_HEAL_OVER_TIME',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'heal_over_time\'',
          'attributes' => 
          array (
            'startLine' => 119,
            'endLine' => 119,
            'startTokenPos' => 401,
            'startFilePos' => 4095,
            'endTokenPos' => 401,
            'endFilePos' => 4110,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 119,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 60,
      ),
      'CATEGORY_LOCK' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_LOCK',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'lock\'',
          'attributes' => 
          array (
            'startLine' => 121,
            'endLine' => 121,
            'startTokenPos' => 412,
            'startFilePos' => 4147,
            'endTokenPos' => 412,
            'endFilePos' => 4152,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'CATEGORY_LINE_OF_SIGHT' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_LINE_OF_SIGHT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'line_of_sight\'',
          'attributes' => 
          array (
            'startLine' => 123,
            'endLine' => 123,
            'startTokenPos' => 423,
            'startFilePos' => 4198,
            'endTokenPos' => 423,
            'endFilePos' => 4212,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 123,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'CATEGORY_INVISIBILITY' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_INVISIBILITY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invisibility\'',
          'attributes' => 
          array (
            'startLine' => 125,
            'endLine' => 125,
            'startTokenPos' => 434,
            'startFilePos' => 4257,
            'endTokenPos' => 434,
            'endFilePos' => 4270,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 125,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'CATEGORY_PROSPECTING' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_PROSPECTING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'prospecting\'',
          'attributes' => 
          array (
            'startLine' => 127,
            'endLine' => 127,
            'startTokenPos' => 445,
            'startFilePos' => 4314,
            'endTokenPos' => 445,
            'endFilePos' => 4326,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 127,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'CATEGORY_OTHER' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'CATEGORY_OTHER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'other\'',
          'attributes' => 
          array (
            'startLine' => 129,
            'endLine' => 129,
            'startTokenPos' => 456,
            'startFilePos' => 4364,
            'endTokenPos' => 456,
            'endFilePos' => 4370,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 129,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'ELEMENT_NEUTRAL' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'ELEMENT_NEUTRAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'neutral\'',
          'attributes' => 
          array (
            'startLine' => 131,
            'endLine' => 131,
            'startTokenPos' => 467,
            'startFilePos' => 4409,
            'endTokenPos' => 467,
            'endFilePos' => 4417,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 131,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'ELEMENT_EARTH' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'ELEMENT_EARTH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'earth\'',
          'attributes' => 
          array (
            'startLine' => 133,
            'endLine' => 133,
            'startTokenPos' => 478,
            'startFilePos' => 4454,
            'endTokenPos' => 478,
            'endFilePos' => 4460,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 133,
        'endLine' => 133,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ELEMENT_FIRE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'ELEMENT_FIRE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fire\'',
          'attributes' => 
          array (
            'startLine' => 135,
            'endLine' => 135,
            'startTokenPos' => 489,
            'startFilePos' => 4496,
            'endTokenPos' => 489,
            'endFilePos' => 4501,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 135,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'ELEMENT_WATER' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'ELEMENT_WATER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'water\'',
          'attributes' => 
          array (
            'startLine' => 137,
            'endLine' => 137,
            'startTokenPos' => 500,
            'startFilePos' => 4538,
            'endTokenPos' => 500,
            'endFilePos' => 4544,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 137,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ELEMENT_AIR' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'ELEMENT_AIR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'air\'',
          'attributes' => 
          array (
            'startLine' => 139,
            'endLine' => 139,
            'startTokenPos' => 511,
            'startFilePos' => 4579,
            'endTokenPos' => 511,
            'endFilePos' => 4583,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 139,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'spell_effect_types\'',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 50,
            'startTokenPos' => 53,
            'startFilePos' => 2473,
            'endTokenPos' => 53,
            'endFilePos' => 2492,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'slug\', \'category\', \'description\', \'value_type\', \'element\', \'unit\', \'is_positive\', \'sort_order\', \'dofusdb_effect_id\']',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 63,
            'startTokenPos' => 62,
            'startFilePos' => 2522,
            'endTokenPos' => 94,
            'endFilePos' => 2734,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 63,
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
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'is_positive\' => \'boolean\', \'sort_order\' => \'integer\', \'dofusdb_effect_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 69,
            'startTokenPos' => 103,
            'startFilePos' => 2761,
            'endTokenPos' => 126,
            'endFilePos' => 2880,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 69,
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
      'categories' => 
      array (
        'name' => 'categories',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return list<string>
 */',
        'startLine' => 144,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'currentClassName' => 'App\\Models\\SpellEffectType',
        'aliasName' => NULL,
      ),
      'valueTypes' => 
      array (
        'name' => 'valueTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return list<string>
 */',
        'startLine' => 180,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'currentClassName' => 'App\\Models\\SpellEffectType',
        'aliasName' => NULL,
      ),
      'elements' => 
      array (
        'name' => 'elements',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return list<string>
 */',
        'startLine' => 188,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'currentClassName' => 'App\\Models\\SpellEffectType',
        'aliasName' => NULL,
      ),
      'spellEffects' => 
      array (
        'name' => 'spellEffects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 193,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffectType',
        'implementingClassName' => 'App\\Models\\SpellEffectType',
        'currentClassName' => 'App\\Models\\SpellEffectType',
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