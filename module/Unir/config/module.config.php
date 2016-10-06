<?php
return [
    'service_manager' => [
        'factories' => [
            \Unir\V1\Rest\Redirects\AcceptableTargetValidator::class => 'Unir\\V1\\Rest\\Redirects\\AcceptableTargetValidatorFactory',
        ],
    ],
    'validators' => [
        'factories' => [
            \Unir\V1\Rest\Redirects\AcceptableTargetValidator::class => \Unir\V1\Rest\Redirects\AcceptableUriValidatorFactory::class,
            \Unir\V1\Rest\Redirects\AcceptableOriginValidator::class => \Unir\V1\Rest\Redirects\AcceptableUriValidatorFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'unir.rest.redirects' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/global/redirects[/:redirects_id]',
                    'defaults' => [
                        'controller' => 'Unir\\V1\\Rest\\Redirects\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'unir.rest.redirects',
        ],
    ],
    'zf-rest' => [
        'Unir\\V1\\Rest\\Redirects\\Controller' => [
            'listener' => \Unir\V1\Rest\Redirects\RedirectsResource::class,
            'route_name' => 'unir.rest.redirects',
            'route_identifier_name' => 'redirects_id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'origin',
                1 => 'target',
                2 => 'owner',
                3 => 'active',
                4 => 'code',
                5 => 'precise_origin',
            ],
            'page_size' => '50',
            'page_size_param' => 'page_size',
            'entity_class' => \Unir\V1\Rest\Redirects\RedirectsEntity::class,
            'collection_class' => \Unir\V1\Rest\Redirects\RedirectsCollection::class,
            'service_name' => 'Redirects',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'Unir\\V1\\Rest\\Redirects\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'Unir\\V1\\Rest\\Redirects\\Controller' => [
                0 => 'application/vnd.unir.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'Unir\\V1\\Rest\\Redirects\\Controller' => [
                0 => 'application/vnd.unir.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \Unir\V1\Rest\Redirects\RedirectsEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'unir.rest.redirects',
                'route_identifier_name' => 'redirects_id',
                'hydrator' => \Zend\Hydrator\ObjectProperty::class,
            ],
            \Unir\V1\Rest\Redirects\RedirectsCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'unir.rest.redirects',
                'route_identifier_name' => 'redirects_id',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'db-connected' => [
            \Unir\V1\Rest\Redirects\RedirectsResource::class => [
                'adapter_name' => 'vagrant',
                'table_name' => 'redirects',
                'hydrator_name' => \Zend\Hydrator\ObjectProperty::class,
                'controller_service_name' => 'Unir\\V1\\Rest\\Redirects\\Controller',
                'entity_identifier_name' => 'id',
                'table_service' => 'Unir\\V1\\Rest\\Redirects\\RedirectsResource\\Table',
                'resource_class' => \Unir\V1\Rest\Redirects\RedirectsResource::class,
            ],
        ],
    ],
    'zf-content-validation' => [
        'Unir\\V1\\Rest\\Redirects\\Controller' => [
            'input_filter' => 'Unir\\V1\\Rest\\Redirects\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'Unir\\V1\\Rest\\Redirects\\Validator' => [
            0 => [
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'id',
                'description' => 'Rule Id',
                'field_type' => 'int',
            ],
            1 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\Uri::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Unir\V1\Rest\Redirects\AcceptableOriginValidator::class,
                        'options' => [],
                    ],
                    2 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '500',
                        ],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\UriNormalize::class,
                        'options' => [
                            'enforcedscheme' => 'http',
                        ],
                    ],
                ],
                'name' => 'origin',
                'description' => 'URL de Origen',
                'field_type' => 'url',
            ],
            2 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\Uri::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Unir\V1\Rest\Redirects\AcceptableTargetValidator::class,
                        'options' => [],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\UriNormalize::class,
                        'options' => [
                            'enforcedscheme' => 'http',
                        ],
                    ],
                ],
                'name' => 'target',
                'description' => 'URL Destino',
            ],
            3 => [
                'required' => false,
                'validators' => [
                    0 => [
                        'name' => \Zend\I18n\Validator\IsInt::class,
                        'options' => [
                            \locale::class => 'es',
                        ],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\ToInt::class,
                        'options' => [],
                    ],
                ],
                'name' => 'owner',
                'description' => 'Owner',
                'allow_empty' => false,
                'continue_if_empty' => true,
            ],
            4 => [
                'required' => false,
                'validators' => [
                    0 => [
                        'name' => \Zend\I18n\Validator\IsInt::class,
                        'options' => [],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\ToInt::class,
                        'options' => [],
                    ],
                ],
                'name' => 'active',
                'allow_empty' => false,
                'continue_if_empty' => true,
            ],
            5 => [
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'created_at',
                'allow_empty' => true,
                'continue_if_empty' => false,
            ],
            6 => [
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'modified_at',
                'description' => '',
            ],
            7 => [
                'required' => false,
                'validators' => [
                    0 => [
                        'name' => \Zend\I18n\Validator\IsInt::class,
                        'options' => [],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Zend\Filter\ToInt::class,
                        'options' => [],
                    ],
                ],
                'name' => 'redirect_type',
                'field_type' => 'int',
            ],
            8 => [
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'redirect_code',
                'description' => 'HTTP Status code for the redirection',
            ],
        ],
    ],
    'validator_metadata' => [
        \Unir\V1\Rest\Redirects\AcceptableTargetValidator::class => [],
    ],
];