<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/app/Models/Entity/Spell.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '743d14533f31626ac90d40dd1d025365' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
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
      '567dbeb0efe8bac899e3ec4d4730de47' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
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
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '0132f6a5eee3c12b4ed533877c32cc0c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
          'TMedia' => 
          array (
            0 => '@template',
            1 => 
            \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
               'name' => 'TMedia',
               'bound' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'default' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'lowerBound' => NULL,
               'description' => '',
               'attributes' => 
              array (
                'startLine' => 2,
                'endLine' => 2,
              ),
            )),
          ),
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '651e96ccaebfa5ae0c8b1f91d071b208' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'bootInteractsWithMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '69e4ffe8c7d10306acfa3bfc76a31dc6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'media',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ff8ab2ed9e0243605f3d153956966f26' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'aa365c9d0412b1b4c8778b0083e3b146' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromRequest',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f42de4af80f81442fe5542984796bf3a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromDisk',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '7dec01f530be1f1688244da479cab82f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addFromMediaLibraryRequest',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2f5bab65b531a8a88f808a45d53f06d2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'syncFromMediaLibraryRequest',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a316d02bee6bdf285613d40c4fdc52b3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMultipleMediaFromRequest',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f9c8d5d8352a53034025ddb2f1bc6279' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addAllMediaFromRequest',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6b9aad4efa95a6f383273aefd3f697cd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '90831eba18c5e1bf9d6ad62ca2c5492a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromString',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '1fe287bb4731a55ab6f16d3096b3057e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromBase64',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '1ae332fa8730b4ad972a04db3205399b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaFromStream',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ced6e372eece6cf88e37dc4529b871ef' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'copyMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '496c4d91cfde6cf3c18c21dd81d353b4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'hasMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '43ae82f1ca0b6b3a016ca9e8e749cf56' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '89e6a4e0a6ea39511dadee2d8871407f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaRepository',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '83eed6893fcf8801f7f80432ac2e9899' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaModel',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'cb05cf1e390435329887835c52051118' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFirstMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '79f9be1a0ecaa9f8446bc186b4bd2b8f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getLastMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '19b5093d06235e0785fbd3ceb1d2267e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaItem',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '08c55a0c3ae2924adc380457e093437b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaItemUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c670956ad06c2f3d6a908f2cae84f80c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFirstMediaUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd458caa7ffa79990cae58a70eb5be45a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getLastMediaUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '84dc35e34b082406d4ef822921480efd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaItemTemporaryUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'cb392a2ea4e872c2022ed6b0799aec3c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFirstTemporaryUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'fdaedf554c96cb68b9c63fd8cae099f9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getLastTemporaryUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'b0fbfa5b86974381bd0fcde3f931128d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getRegisteredMediaCollections',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ab105e60888deec0351a6bcb3953ec27' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'affceae8ffa833dec5f326fb7c77a5e1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFallbackMediaUrl',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2071554f5cf08103aac2a452ad09a6f2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFallbackMediaPath',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e0b1229521882f547dcc8f32bf14f6c0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaItemPath',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'aae05245ad8a59dbdc5704b2af0a9eb3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getFirstMediaPath',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'b7b24d4138362422162d77e7ede7de3c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getLastMediaPath',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'fa06a943b68fd487a53c846ac4d03ec2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'updateMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '27cb2f0d63aba474f043315ccf687c7a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'removeMediaItemsNotPresentInArray',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '720587ac1d330d093a491fd80c39b154' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'clearMediaCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '48bd9de9ad0fcbd04ad016b2e9fdf3d7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'clearMediaCollectionExcept',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'b6e5e64c33608c7e9366dedbeda12d4b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'deleteMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '61338d70d05bae93b5d0e60ddd23c5bd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaConversion',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd22d8dd4a1bb8d59400e115705ffc330' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'addMediaCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'bffcc19879b5ebee912154c7c1bfc213' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'deletePreservingMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e0bac6a65dfae0ca40d4be2e119c5f0d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'shouldDeletePreservingMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '41b71ec0609e1ad7b2c12f06ea9d87e2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'mediaIsPreloaded',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6a2fb2f6a3d8cb80420e3335bd773b4a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'loadMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e3287f80aa459b736b82e2a0779cae98' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'prepareToAttachMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'aaa14e72e3506cf9c8f452484c988282' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'processUnattachedMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '133b05975ec8b6f68e22dba406ccd7f4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'guardAgainstInvalidMimeType',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e1c2ae7a24d34a3a2fb86b26e566c067' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'deleteAllMedia',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e27d1b593b52b2c597739c6ce24935ee' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerMediaConversions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f9637c9d4119243d520a71394a2ffd01' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerMediaCollections',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a2124e26537fe723aaff4f0a19716633' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerAllMediaConversions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e02679d3244c965c81066becfc1e4a66' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => '__sleep',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Spatie\\MediaLibrary',
           'uses' => 
          array (
            'datetimeinterface' => 'DateTimeInterface',
            'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'file' => 'Illuminate\\Http\\File',
            'arr' => 'Illuminate\\Support\\Arr',
            'collection' => 'Illuminate\\Support\\Collection',
            'validator' => 'Illuminate\\Support\\Facades\\Validator',
            'str' => 'Illuminate\\Support\\Str',
            'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
            'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
            'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
            'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
            'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
            'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
            'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
            'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
            'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
            'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
            'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
            'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
            'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
            'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
            'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
            'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TMedia' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TMedia',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'bdcf0b92ac5504282f337dce036bfa40' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
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
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '687a9a9f20487a013d5d2d3b75ca3302' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaFileNameForCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Concerns',
           'uses' => 
          array (
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '905187e13c62f1c334c4de6e675fec19' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getMediaFilePatternForCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Concerns',
           'uses' => 
          array (
            'str' => 'Illuminate\\Support\\Str',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasMediaCustomNaming',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '7e43b003da6fe453badbf436445c2619' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerMediaCollections',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Concerns',
           'uses' => 
          array (
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'cfe12f86a5e26510d858f546ba84a98a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerMediaConversions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Concerns',
           'uses' => 
          array (
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '44f9aa59976673b2738a59d669d0a4bc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'registerEntityImageMediaConversions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Concerns',
           'uses' => 
          array (
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'App\\Models\\Concerns\\HasEntityImageMedia',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '06937f7d7d4dc5b663d5dbb24fa6426f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
          'TFactory' => 
          array (
            0 => '@template',
            1 => 
            \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
               'name' => 'TFactory',
               'bound' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
               'default' => NULL,
               'lowerBound' => NULL,
               'description' => '',
               'attributes' => 
              array (
                'startLine' => 2,
                'endLine' => 2,
              ),
            )),
          ),
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'ff9aa6a1a02a1a354620714ce4d67ef6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'factory',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '85b3829055bd6fac0eafc8b31c803ff1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'newFactory',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'a89a939ce59ff84fd82bcfb60c03b2ad' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getUseFactoryAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
           'uses' => 
          array (
            'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
            'TFactory' => 
            array (
              0 => '@template',
              1 => 
              \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
                 'name' => 'TFactory',
                 'bound' => 
                \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                   'name' => '\\Illuminate\\Database\\Eloquent\\Factories\\Factory',
                   'attributes' => 
                  array (
                    'startLine' => 2,
                    'endLine' => 2,
                  ),
                )),
                 'default' => NULL,
                 'lowerBound' => NULL,
                 'description' => '',
                 'attributes' => 
                array (
                  'startLine' => 2,
                  'endLine' => 2,
                ),
              )),
            ),
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '4fa0c1822531bf6dd2a70392c20903a7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
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
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '3a6292205d0783806b474feb049eb113' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'bootSoftDeletes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '3713937500f1f43afa844a64df6c5995' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'initializeSoftDeletes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '0a2b04ddd25dd6fc945df27571466ea8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'forceDelete',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '945238275d44c471157c513cfe24a260' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'forceDeleteQuietly',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '2b315d07d5b2702560b81f1318bf7040' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'forceDestroy',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'cc3c47543e1567920ec8c7f6f0ac5697' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'performDeleteOnModel',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'cb014b4508215e8f578586c7ed4a491d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'runSoftDelete',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'e22afa6cad00c720533638dd36c0a1b9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'restore',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'aa5d6e2116959249078bfca05303244a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'restoreQuietly',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '5c40aad45bb377549312470ec891f770' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'trashed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'b11939be4604c1046ed9a219705bbd88' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'softDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '8c220a5c16a426c3cc9583ee15761885' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'restoring',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'be1e8d719e5f0a6bf2ec12c79e37f378' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'restored',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '26cb270985deaa82677061cf9184c04a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'forceDeleting',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '70469cb58080b20859aebc132000fd18' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'forceDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '80343e9c69e43ccf4515e1919f197495' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'isForceDeleting',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      'd6994ce35e07dd3e1ce589caae0a862f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getDeletedAtColumn',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '0a70ec8623bf6a6130b331b17451161c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getQualifiedDeletedAtColumn',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'Illuminate\\Database\\Eloquent',
           'uses' => 
          array (
            'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'basecollection' => 'Illuminate\\Support\\Collection',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
           'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
         'traitData' => 
        array (
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
          1 => 'App\\Models\\Entity\\Spell',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<\\\\Database\\\\Factories\\\\SpellFactory> */',
        ),
      )),
      '762d4f2d7f0699b3cd8540839e87692b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'setAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'c6f725df63466af05aadd75bdf449713' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'setDescriptionAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'cd10e96d451a0f96904ac840282e07b1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'createdBy',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'b8aa264bc2589df89702b14b327e4795' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'breeds',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      '2eb7ac5b0b8bde2e4e34f1f0bc4de119' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'creatures',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'accb8d3b201796987519cf818cdf8416' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'scenarios',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'bc8588cb256ebfef356f2c864ceaccbd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'campaigns',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'cdd8be25bafe0160d0c3873a2513a377' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'spellTypes',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'ca6b82a6ea41c4775af10d9f98ca22d3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'spellEffects',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'af33ca2970a21dd261b43ea202b3ea03' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'monsters',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      'f91a136089c2668951f057f5afa6a01b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'conditions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      '49f359e7d25f3aca03592881d0fe0a35' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'effects',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      '56f29983eb0dad264fa699a202980913' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getPoDisplayAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      '7382eed9fcd2e1ae806c82bbd20eacb4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effect' => 'App\\Models\\Effect',
          'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
          'spelleffect' => 'App\\Models\\SpellEffect',
          'spelltype' => 'App\\Models\\Type\\SpellType',
          'user' => 'App\\Models\\User',
          'areaconstants' => 'App\\Support\\AreaConstants',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Spell',
         'functionName' => 'getAreaAttribute',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effect' => 'App\\Models\\Effect',
            'breedspellpivot' => 'App\\Models\\Pivots\\BreedSpellPivot',
            'spelleffect' => 'App\\Models\\SpellEffect',
            'spelltype' => 'App\\Models\\Type\\SpellType',
            'user' => 'App\\Models\\User',
            'areaconstants' => 'App\\Support\\AreaConstants',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Spell',
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
      '/var/www/KrosmozJdr/app/Models/Entity/Spell.php' => '519105ea32ab65877ba8de3bf95ab61e74f7649609c5435b664063220c851d79',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasEntityImageMedia.php' => 'b5b633f6bcb54e2ca03cda21ef004f49d21e57737f3fcb3c153870e38dc9c2e7',
      '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/InteractsWithMedia.php' => '2fa4c26f5b3757892fb1f79083cefb514993dbef90e48da37597f36e834ace33',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasMediaCustomNaming.php' => '4d2d30c927978dfb2f19ff2fd24a406d3c814153d432d2738f419461d5ba131c',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Factories/HasFactory.php' => 'b6cb2b164e90168e80963a5549541f5f3188a3ec8cfd368bf3611bd94fbd46a7',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/SoftDeletes.php' => 'da1b0c13d78ba2f62e97e5627c3149f4e81b9cf9b6092d4ca7f02ca5e5bbcfec',
    ),
  ),
));