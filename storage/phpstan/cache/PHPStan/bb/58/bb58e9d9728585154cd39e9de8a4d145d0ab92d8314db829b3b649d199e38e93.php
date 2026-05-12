<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/app/Models/Entity/Resource.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '6eb0de996502460c0036a9eb074a456d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
      'c393ea285ff46fad623e592f3252536e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '248b7a188051d6f70bf3255461169d7a' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2abcce3b423cfdf077ae4203b368566b' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'ea68a685723850eaefa017265b6639fe' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'da9dc389183469871a22341c140becf1' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '2bc608570c45cb0e46933d0fac77d02a' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '96753f8d88e3b73ef1871d2fe00f24ed' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '375ad74cddf96bee67a409bd2efdb1a9' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '46d974845b4fb513959fdcd5911880c3' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '40ee1664610836fab00a69dee29bab76' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '0312fbcb042f1f819c590235ce2415b9' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '82a907509585d3dedd77540beb344973' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '244d9fe86c82ac24361a30d89f07171a' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'b61106c3613edcd288bc5cd137c25a63' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '62047696e7dd9e5681b7b5dae40a4225' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a28b8da2e1f3fe75832a662f830abd5c' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'efcde1934b7c2be0e122e9e66aabea35' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'cba828e99b375610cf115a256ac96d97' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f73dd58b8b64cd4293225b39d1ff23e8' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '376d4a8e7c2e08727ef2d4b658e9b2e4' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '3780203979e31f3ecaf1b9d207c80f34' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c98729112d94a968e1e0f3cf5afd4f1f' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '0ceec28fb849b054218fe0049b05a497' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '81994ba83fdb9190e4fddb9e42f21068' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '5bc1ae4905311cdb714a924e08c7c3fb' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e7232f64378444c83be6d29cd760bcaf' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'fa7defb8c15b16677992cede8c977299' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd1249c828a5798fdfb5b6af8ccad874b' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '12551e9dbb4bf4cdb9cb974e6f7aa125' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'e284ba7e7e07734493e7a5ac19b3e66e' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a723174203ee187c1c5f01aca8336129' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd57d4973d184d6d5b6ab279841638c24' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd5b099dd30c71d58d4d7d20e41c87b00' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c767638c70cfc98e4c462bffd5f1142c' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'd83a60e34eba61894a7548b3ac3028c8' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f01a90ca499d3706757b31edd8e4c4d1' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '29ec48fd39c78d44d933845aacf66e88' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '84a6b4e8c82ed78405ffe171ee3d17e4' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '7fc98c57c0813da062df17bbce9044ec' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '8682450e22abb424ebe691733a3299eb' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'f0e9bb2fb253af01a439be6a484a7d2d' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '3b781a1a7ae5b26536ed685b2731e75d' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '3a45c99cec0918a4a0dd77415331ce27' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '38925623bb26f130a880e342df82a80a' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c3d17b736e8eae839d521f09b5b06969' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '108fb9afd259681ad12f4a40209d703a' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '78e85355f8e53edbf15885ec1b23c279' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '0400798b612cf8304bfe77e84f331433' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c2838d6625adcfeb4ab4fd4e28798d70' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '787fc6093687b421d2b0083a182ecf1b' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a48afd0b3f29fcd6d9ded548b62227cb' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'c5d187852f646d97845f3c2481688a00' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '92e9b93bf3e9111676fdcc790668e9e4' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a3ce54f65a6e3ae96f54ee56f420fb3c' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '52a4e7bb23bf1aef0227e8674e97fccc' => 
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
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '961521ce392302edf8d348b7c46ca2b8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'af65e43ca58e4364bbea064a1fba5652' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      'a817b5b2810ac9814ce6a699172c00d2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'str' => 'Illuminate\\Support\\Str',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
          3 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          4 => NULL,
        ),
      )),
      '4f5f230f24c1fdfaab3eb543d3e754f0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '3f7ee368828872a232e9796f0eb92dae' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '28b9525e937719d9d78ccc76f0b4916a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'interactswithmedia' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'App\\Models\\Concerns\\HasEntityImageMedia',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '475a49831df5e3e2325434edb1f9a387' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '597585785d25dc96ac9d5e114f67f71d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '24b992553e5c291373c780288cfb10ea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '0b78157dbab0a79385e053bb49c75244' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent\\Factories',
         'uses' => 
        array (
          'usefactory' => 'Illuminate\\Database\\Eloquent\\Attributes\\UseFactory',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'abb6e0f1183a5b8fce73dfee83f1faeb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'fb3bd17442947e6a4da1e3bc5e89bd1d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'e3aea6882d544cc605070344c2534311' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'af420d067ac10a626e184226f5b47cfc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'ab8566b235ebd910f06e7c6f27cfdaa6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'ef829c888000988ac0a5b51b47550803' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '54372cde217d8b0e43d7eebe4387b4b7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '60c7bf2e5d4e6f0e192b594fe609fc28' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '500cbe704f4e24d7e501fa7acd1cfd53' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'b7f154464b5f9281ce0c5f2fea081478' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'f24ff938bc9f497edd73901d1e782d13' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '9ec175c5cec64f4e62288844c3f5b3e2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      'e7f92676fb63065f412d395d00dd6bf7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '5027a220b6d9d329bf95934ee8b84579' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '56e11592d9cb78e32a61f030583cb54e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '5ba51a4a12b3e79110a6364b3f2d6bbb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '5c7b94af63c11c9299e891407e840d45' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '7c1321b3164f39e2cfe5781bb7fd6d4d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '1e2abb35bd395900dc8b357afa951c58' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Database\\Eloquent',
         'uses' => 
        array (
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'basecollection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
           'className' => 'App\\Models\\Entity\\Resource',
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
          0 => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
          1 => 'App\\Models\\Entity\\Resource',
          2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          3 => NULL,
          4 => '/** @use HasFactory<ResourceFactory> */',
        ),
      )),
      '36fda00f81ae7be907002360a0ab0c5a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '66f7a36bdc22a018263a05c1ce5bf31f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
         'functionName' => 'resourceType',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '2eecfcb4cceccf84cb7361dfa0069172' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      'b083993b13122f20b7ffca9a697b153a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '7e0cdedb223351369352bf70a53bb048' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      'e94cbe52e1a2f9deb4177aa495a3585f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
         'functionName' => 'recipeIngredients',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      'd397fbf578de5cb93b017b22a30dfa00' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '641f1eb13cc030820874053d0a9853bd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
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
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '8643ff06ddac0db17fd40636a7629d90' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
         'functionName' => 'shops',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      'e08ea03fadc2e9c4ee59c116f299c514' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
         'functionName' => 'effectUsages',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      'f7ffd544ca780fa441a31c0d1bac40a6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Models\\Entity',
         'uses' => 
        array (
          'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
          'effectusage' => 'App\\Models\\EffectUsage',
          'objecteffect' => 'App\\Models\\ObjectEffect',
          'resourcetype' => 'App\\Models\\Type\\ResourceType',
          'user' => 'App\\Models\\User',
          'resourcefactory' => 'Database\\Factories\\ResourceFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        ),
         'className' => 'App\\Models\\Entity\\Resource',
         'functionName' => 'objectEffects',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Models\\Entity',
           'uses' => 
          array (
            'hasentityimagemedia' => 'App\\Models\\Concerns\\HasEntityImageMedia',
            'effectusage' => 'App\\Models\\EffectUsage',
            'objecteffect' => 'App\\Models\\ObjectEffect',
            'resourcetype' => 'App\\Models\\Type\\ResourceType',
            'user' => 'App\\Models\\User',
            'resourcefactory' => 'Database\\Factories\\ResourceFactory',
            'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
            'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
            'model' => 'Illuminate\\Database\\Eloquent\\Model',
            'belongstomany' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
            'carbon' => 'Illuminate\\Support\\Carbon',
            'hasmedia' => 'Spatie\\MediaLibrary\\HasMedia',
            'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          ),
           'className' => 'App\\Models\\Entity\\Resource',
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
      '/var/www/KrosmozJdr/app/Models/Entity/Resource.php' => 'e3f5700087b7593d4503e81b8e8fad0cede4d2bc0db0200de3ecc62f85e1092e',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasEntityImageMedia.php' => 'b5b633f6bcb54e2ca03cda21ef004f49d21e57737f3fcb3c153870e38dc9c2e7',
      '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/InteractsWithMedia.php' => '2fa4c26f5b3757892fb1f79083cefb514993dbef90e48da37597f36e834ace33',
      '/var/www/KrosmozJdr/app/Models/Concerns/HasMediaCustomNaming.php' => '4d2d30c927978dfb2f19ff2fd24a406d3c814153d432d2738f419461d5ba131c',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Factories/HasFactory.php' => 'b6cb2b164e90168e80963a5549541f5f3188a3ec8cfd368bf3611bd94fbd46a7',
      '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/SoftDeletes.php' => 'da1b0c13d78ba2f62e97e5627c3149f4e81b9cf9b6092d4ca7f02ca5e5bbcfec',
    ),
  ),
));