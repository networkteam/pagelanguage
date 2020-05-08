<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',[
    'fallbackType' => [
        'label' => 'LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration_tca.xlf:site_language.fallbackType',
        'displayCond' => 'FIELD:sys_language_uid:>:0',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['', ''],
                ['Strict: Show only translated content, based on overlays','strict'],
                ['Fallback: Show default language if no translation exists', 'fallback'],
                ['Free mode: Ignore translation and overlay concept, only show data from selected language', 'free'],
            ],
        ],
    ],
]);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'fallbackType', '', 'after:l18n_cfg');
