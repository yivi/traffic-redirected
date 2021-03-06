<?php

return [
	'validators'          => [
		'factories' => [
			\Unir\V1\Rest\Redirects\AcceptableTargetValidator::class => \Unir\V1\Rest\Redirects\AcceptableUriValidatorFactory::class,
			\Unir\V1\Rest\Redirects\AcceptableOriginValidator::class => \Unir\V1\Rest\Redirects\AcceptableUriValidatorFactory::class,
		],
	],
	'translator'          => [
		'locale'                    => 'es',
		'translation_file_patterns' => [
			[
				'base_dir' => Zend\I18n\Translator\Resources::getBasePath(),
				'pattern'  => Zend\I18n\Translator\Resources::getPatternForValidator(),
				'type'     => 'phparray'
			]
		]
	],
	'router'              => [
		'routes' => [
			'unir.redirects.import.error.log' => [
				'type'    => 'Segment',
				'options' => [
					'route'    => '/global/redirects/collection/import_log/:filename',
					'defaults' => [
						'controller' => 'importer_log',
						'action'     => 'index'
					],
				],
			],
		]
	],
	'controllers'         => [
		'invokables' => [
			'importer_log' => \Unir\Controller\RedirectCollectionImportLogController::class
		]
	],
	'importer_validators' => [
		'origin' => [
			\Zend\Validator\Uri::class                               => [ 'allowRelative' => false ],
			\Unir\V1\Rest\Redirects\AcceptableOriginValidator::class => []
		],
		'target' => [
			\Zend\Validator\Uri::class                              => [ 'allowRelative' => false ],
			Unir\V1\Rest\Redirects\AcceptableTargetValidator::class => []
		]
	]

];