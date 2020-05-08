<?php

return [
    'frontend' => [
        'networkteam/pagelanguage/fallbacktype-resolver' => [
            'target' => \Networkteam\Pagelanguage\Middleware\LanguageFallbacktypeResolver::class,
            'after' => [
                // We need site + language to be able to determine the target page
                'typo3/cms-frontend/site'
            ],
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
                'typo3/cms-frontend/page-resolver'
            ]
        ]
    ]
];
