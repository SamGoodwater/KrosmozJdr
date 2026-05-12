<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/app/Models/Entity/Creature.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '52b43dc4bd6ade2068781d133698cde5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
      '4f78ee9b3fb84236ae9ba040a46f2f89' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '07a6cad54c1ee44223af880563fa1754' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ceb8d905c9355783ac0db5158237d48e' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ad6d84834f0b836e48785d14f6d516a7' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c607c5ef384156d92da7574233e55941' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '13b2ac1a560587b51bda729c8f7fac43' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '110337204cb0c76c293706e435bd83f3' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '275f051e7f80f0743bd9fad6efb87bfd' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '0c9756d9fecf545e6b2c980e7ea50b83' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6d569219e0c94e869c2b3f521a02b9b1' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a8d6514fb3c3f6c1eb3dfe5c4ebb60f5' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'aeb69718f908d8af6061b19a1c603368' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '20cbd18e2e28cb83cd5130aa7f129a1b' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '3d238c20da74062c52655f6206f6f053' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f1632d6f9ab7d102618fc57d97828e17' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd9c7d44982e95adee1407a46b25e271f' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '7796e0425ec0711a5caad41ab28b7ec3' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '8bd5b69c30fd127f9eec7e73ce365461' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2d601b6a494007d55baa226ac14757cd' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd34bdbb37356c3bdcd716ab4c3c90a8b' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '0ffc552a0c0aec0ee326ba08fd0690e9' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '88bb0b03e0a916bb1301974acacb0ae5' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ec5a1835c402f0870af05e543c8e23d1' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '86a45e93d11a187b4dc0d3a81ac47f53' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c750ab6b1d8566cad395c6aa06426b87' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f16e2fbd3dcd12b874d052fa8403c1ed' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '5688febc523edd0b409fbfba7b008fe2' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '5952f29a831e2fdae35fc066aad2544a' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '847fac3eac2d40bacad3f89a72589ad4' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '687b7fde7d7cc0c49d1869f965bc4b44' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '4883aa584a9483d66cd129c8340ad632' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a9a90f5b760bb15e7dbe356658578e0a' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '66309f6032b1b1a450f87698b64edeac' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f123834906d88df6f9086504491f902b' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'bf56d424fe2e42896273e41bbb388708' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '16a18350b3553f8f745774ffb5055254' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ce0e3008c3f5a88c18881e8dcbcd7a11' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd444fdcc47322b3c863a4baf55f7757f' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6388031d3a1fb569098bf38ffc4b5d3d' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ea3755b85d777efd53d22178cebef6ce' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd4a2d9eb40828fd5eda5708a67c6cefe' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '74675cfe2b8c47fa25f8c8fef4a76fdf' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '285fb9f858b5f2265e318991e52f3e24' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '9b9bb7036fc99027a8b581d605e9fa24' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f7849b79c6ad1c5303a6a3344c7d5a75' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'efae69077b3b5e65c6919c6e8507d5d9' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ae9f73eabc4627ae3beee49591885fa5' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f47fccc4c6cddfdeb89eff1379578e0a' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '74911c1f3e4fc705399ca38ff338eeb9' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'abfc31a9cf476535eebdb69003d084e4' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '26f7fdaab1c785345e2e9cda1318ef28' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '107ae9e285227a4c91ef53342a65039d' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'b03a4b2f86d3ea06f7732f5fa3380241' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '7f2bff081d9669cc95133ad509e88850' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6155c43c931648fb141dd2218fd41b49' => 
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
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '8c11101b87218400d2e588b13e1154b7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2eea8aee65efadd534b8947ecfc4df87' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '6272edf035e8c9e3a4e9d87de6cd6d1a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '84dfff143aa6fc763f2b0079b092cbac' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '88ef3c9245188f79e9250e836e8c3f80' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'e416449398998727d97cd8b6e350cac2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '0c9786c92d963aad19ed90c6f5b191ea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '5994ae120fe89a577ebd9ce94cad2bb9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'ccb0a799123dffaf165ddd418bce7d03' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'fc1e4d327d9e0d38184b91031aaf611b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'a6f2752263b0dc22c678a02f0880e23c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '5cc2cf4bedaa74411e6d943bc5366aef' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'fb192b49281aaa4d6de1573eedfdf0b3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '2f2a4c58f8039f5b6366d58f31d9c3f4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '332ba0f75156849da39f82591c018f4b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '491d26d1ee4347c388df7075b2c786b4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '77136b8688981576932a8aa30d5e321e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'd8b2c942fb5396e7a8286696abbb8fa2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '0d8a1670416d6b8879256804d8573b67' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '4f141271eb955dec5263a55f9927aa56' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'c68c15b8dba48a1f1ae07e64171e1858' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '5b7fffbb291b32787e0aa6c28bfa37a1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'e04d91da1167d2482d8713a0397c1b81' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'f0ec7d4dd8232a3f69828a2f3c78f60a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'ec435afc6951415bc31d10d47f103728' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'a9926239c2b68609547a0301ca442a37' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '14d63561dde41a5ef0e671ff290b0b79' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      'fe4353b5b718acd7a9000408223477f9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '2b6fcec15612732b2550bbecf81d3a19' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
           'className' => 'App\\Models\\Entity\\Creature',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
          1 => 'App\\Models\\Entity\\Creature',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<CreatureFactory> */',
        ),
      )),
      '8352c7cbbfd25c80286cf17d9bfdd381' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      'c336df35e2e367ce29df8d5699aa261d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
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
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '74d17f99f1d41fa0ece37d12786f4758' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'creatureTraits',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '249014fab008a9e65911a546024a5cc5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'capabilities',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '7f91119d25f647027ba4bb719db0aa54' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'items',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      'dec33e5abda1ce7dd58f7caeaaebad36' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'resources',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      'f40053059be578399d32f73d387c6d3f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'spells',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '37ef414da7bf0b5653d402ba5ee6e221' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'consumables',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '9848ef5b6d9fcc2ee68acc9121859bd5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'npc',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '153d0348597d4ba7e306e909253bff34' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'user' => 'App\\Models\\User',
          'creaturefactory' => 'Database\\Factories\\CreatureFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Creature',
         'functionName' => 'monster',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'user' => 'App\\Models\\User',
            'creaturefactory' => 'Database\\Factories\\CreatureFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Creature',
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
      '/var/www/KrosmozJdr/app/Models/Entity/Creature.php' => '91182146397ab1d7fb4fbe80856369d42d8a179f8062b239a010c4db4edbb753',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasEntityImageMedia.php' => 'b5b633f6bcb54e2ca03cda21ef004f49d21e57737f3fcb3c153870e38dc9c2e7',
      '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/InteractsWithMedia.php' => '2fa4c26f5b3757892fb1f79083cefb514993dbef90e48da37597f36e834ace33',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasMediaCustomNaming.php' => '4d2d30c927978dfb2f19ff2fd24a406d3c814153d432d2738f419461d5ba131c',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Factories/HasFactory.php' => 'b6cb2b164e90168e80963a5549541f5f3188a3ec8cfd368bf3611bd94fbd46a7',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/SoftDeletes.php' => 'da1b0c13d78ba2f62e97e5627c3149f4e81b9cf9b6092d4ca7f02ca5e5bbcfec',
    ),
  ),
));