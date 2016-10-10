<?php
return [
    'zf-content-negotiation' => [
        'selectors' => [],
    ],
    'db'                     => [
        'adapters' => [
            'dummy'   => [],
            'vagrant' => [],
        ],
    ],
    'translator'             => [
        'locale'                    => 'es',
        'translation_file_patterns' => [
            [
                'base_dir' => Zend\I18n\Translator\Resources::getBasePath(),
                'pattern'  => Zend\I18n\Translator\Resources::getPatternForValidator(),
                'type'     => 'phparray'
            ]
        ]
    ],
];
