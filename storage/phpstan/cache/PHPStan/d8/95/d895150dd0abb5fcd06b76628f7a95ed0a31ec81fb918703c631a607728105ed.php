<?php declare(strict_types = 1);

// ftm-/var/www/KrosmozJdr/vendor/spatie/laravel-medialibrary/src/InteractsWithMedia.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '18613c065c2b01c4e5e9fd5ce15c6cb1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '6e58b9fbc16f144c5d42fb88f9ea1080' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '4d13ba93e712dc57bd9653aece73cdb4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '605b90a67ac5f1eddbd950c98575af93' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'fe2a02a7ca6072dc3adc48b70aed2205' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '66ec8b9eafc8e3e9e153c29c42ff71d9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'b203312973bade9cb3fd7aacd37802ae' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '64f539d38bd7383455b417b635b3a545' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '912a3ac19a6aea77d345864fbbdb709a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '61a7497436d8d245cfaedc822f936c00' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '9ab0d3cc2b903920b818f2f1a3bbb658' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'b10619664d60a3ffa68712b6e42918b8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '7f071e86ae8b46524e2887e0f81c4cb5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5fdfd0cbf93b06c6566fa72e72c58c1e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'f5dfd20d5a05bf71647ee215201eb78a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '293210b71659f6c41371a86dda97a744' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '9f5d7ef4baf87eee27bcc912a92fc115' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '22043baa8110ade1a9ee847ef97c5c37' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '3b14298adac5af410e5c7ae96381f338' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5e798383a2015a9f7ae65ddccec83061' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '3ea92ea167c5b85461ebd454dcd36dcf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '461ef5c53279c17fcf554ab066d47bcf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'e4d6bead05ae5b0819aae89ba4916daf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'a6f32d67dda684aaa20dcfb50d3de014' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '8dd926ecf786e8fa1da8e6cb32440927' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'c984de3ff22306938b15bfc8b0475208' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '4aad4e0c45db6704849423d01b2a2f83' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '7ead59cb1a2ac4c67f139eed06292b70' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'dd4e07eb04191d954d4f1442f6ae24da' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5140372d4c1668df330d1d4e860265e8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'c59473bd825f8dfc0af78fc5cd3b9735' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '6767c12feda33735e47aae53829c82a3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5bfabe4415348a8ba41a9bd31b235fd6' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '71d659f82aee1635829167360d4569ea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '7baacafb2ca9582401a58dc9bf392279' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'e3cf0a1acbbaa98190e897f270b399a5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5f1a0b854cb2b6125b547f4564e009d4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '77a704d94bfe8fe7c1a7e99a96b64066' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '5b8b82b32519e8598a06c7a6c220d57b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '3b393f34d7846dfe931813aae62e90fa' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'c20be30ad7f06cf0e382fa2ceb7b2003' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '4a16ee16085ae05d94ecf14c247044f1' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '9b5ef66e8d7ec7cc4f59a18a7d5d0cf3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      'fdccbd87bf574216895aa70530d03b5f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '1dfd1a98d42d11c0351bf1c0c7fdf6e9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '07a7c0c88bf6319dcf7540a98a3ab4de' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '94b3c6a543af366a52276c43c732ca67' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '3517ee7d486f602519a612aeb9afd85f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '05bd51d45b61951fa00bd7be790df6e2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '329223d8257f5b6a0944072b705e390b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '92850e0a6b03fbc46aef26598d23480c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '1aa0eead2382f2c57f744ed8b8891b90' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '684de20d5c27037c65df838bfe623341' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '72711612bee2cfaf4afea45915ffb313' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Spatie\\MediaLibrary',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'morphmany' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
          'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
          'file' => 'Illuminate\\Http\\File',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'validator' => 'Illuminate\\Support\\Facades\\Validator',
          'str' => 'Illuminate\\Support\\Str',
          'conversion' => 'Spatie\\MediaLibrary\\Conversions\\Conversion',
          'defaultdownloader' => 'Spatie\\MediaLibrary\\Downloaders\\DefaultDownloader',
          'collectionposition' => 'Spatie\\MediaLibrary\\Enums\\CollectionPosition',
          'collectionhasbeenclearedevent' => 'Spatie\\MediaLibrary\\MediaCollections\\Events\\CollectionHasBeenClearedEvent',
          'filecannotbeadded' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\FileCannotBeAdded',
          'invalidbase64data' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidBase64Data',
          'invalidurl' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\InvalidUrl',
          'mediacannotbedeleted' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeDeleted',
          'mediacannotbeupdated' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MediaCannotBeUpdated',
          'mimetypenotallowed' => 'Spatie\\MediaLibrary\\MediaCollections\\Exceptions\\MimeTypeNotAllowed',
          'fileadder' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdder',
          'fileadderfactory' => 'Spatie\\MediaLibrary\\MediaCollections\\FileAdderFactory',
          'mediacollection' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaCollection',
          'mediarepository' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
          'media' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
          'medialibrarypro' => 'Spatie\\MediaLibrary\\Support\\MediaLibraryPro',
          'pendingmedialibraryrequesthandler' => 'Spatie\\MediaLibraryPro\\PendingMediaLibraryRequestHandler',
          'uploadedfile' => 'Symfony\\Component\\HttpFoundation\\File\\UploadedFile',
        ),
         'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
           'className' => 'Spatie\\MediaLibrary\\InteractsWithMedia',
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
      '/var/www/KrosmozJdr/vendor/spatie/laravel-medialibrary/src/InteractsWithMedia.php' => '2fa4c26f5b3757892fb1f79083cefb514993dbef90e48da37597f36e834ace33',
    ),
  ),
));