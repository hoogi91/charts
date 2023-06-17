<?php

defined('TYPO3') or die();

return (static function (string $extKey = 'charts') {
    $ll = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:tx_charts_domain_model_chartdata';

    // default chartdata tca configuration
    $chartDataTcaConfig = [
        'ctrl' => [
            'title' => $ll,
            'label' => 'title',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'versioningWS' => true,
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
            'type' => 'type',
            'searchFields' => 'title',
            'dynamicConfigFile' => '',
            'iconfile' => 'EXT:' . $extKey . '/Resources/Public/Icons/Extension.svg',
        ],
        'types' => [
            0 => [
                'showitem' => implode(
                    [
                        '--palette--;;corePalette,type,labels,--palette--;;datasetPalette,',
                        '--div--;' . $ll . '.tab_colors,background_colors, border_colors,',
                        '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden,starttime,endtime'
                    ]
                ),
            ],
            1 => [
                'showitem' => implode(
                    [
                        '--palette--;;corePalette,type,spreadsheet_assets,spreadsheet_labels,--palette--;;spreadsheetDatasetPalette,',
                        '--div--;' . $ll . '.tab_colors,background_colors, border_colors,',
                        '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden,starttime,endtime'
                    ]
                ),
            ],
        ],
        'palettes' => [
            'corePalette' => [
                'showitem' => 'title, sys_language_uid, l10n_parent, l10n_diffsource',
            ],
            'datasetPalette' => [
                'showitem' => 'datasets, datasets_labels',
            ],
            'spreadsheetDatasetPalette' => [
                'showitem' => 'spreadsheet_datasets, spreadsheet_datasets_labels',
            ],
        ],
        'columns' => [
            'sys_language_uid' => $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'],
            'l10n_parent' => $GLOBALS['TCA']['tt_content']['columns']['l18n_parent'],
            'l10n_diffsource' => $GLOBALS['TCA']['tt_content']['columns']['l18n_diffsource'],
            'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
            'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
            'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
            'title' => [
                'exclude' => true,
                'label' => $ll . '.title',
                'config' => [
                    'type' => 'input',
                    'eval' => 'required',
                ],
            ],
            'type' => [
                'exclude' => true,
                'label' => $ll . '.type',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'hidden',
                    'default' => 0,
                ],
            ],
            'labels' => [
                'exclude' => true,
                'label' => $ll . '.labels',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'textTable',
                    'rows' => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput' => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'datasets' => [
                'exclude' => true,
                'label' => $ll . '.datasets',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'textTable',
                    'rows' => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput' => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'datasets_labels' => [
                'exclude' => true,
                'label' => $ll . '.datasets_labels',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'textTable',
                    'rows' => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput' => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_labels' => [
                'exclude' => true,
                'label' => $ll . '.spreadsheet_labels',
                'config' => [
                    'type' => 'text',
                    'size' => null,
                    'renderType' => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly' => false,
                    'allowColumnExtraction' => true,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_datasets' => [
                'exclude' => true,
                'label' => $ll . '.spreadsheet_datasets',
                'config' => [
                    'type' => 'text',
                    'size' => null,
                    'renderType' => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly' => false,
                    'allowColumnExtraction' => true,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_datasets_labels' => [
                'exclude' => true,
                'label' => $ll . '.spreadsheet_datasets_labels',
                'config' => [
                    'type' => 'text',
                    'size' => null,
                    'renderType' => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly' => false,
                    'allowColumnExtraction' => true,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_assets' => [
                'exclude' => true,
                'label' => $ll . '.spreadsheet_assets',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'assets',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.asset_references.addFileReference',
                        ],
                        'overrideChildTca' => [
                            'types' => [
                                '0' => [
                                    'showitem' => '--palette--;;filePalette',
                                ],
                            ],
                        ],
                    ],
                    'xls,xlsx,ods'
                ),
            ],
            'background_colors' => [
                'exclude' => true,
                'label' => $ll . '.background_colors',
                'description' => $ll . '.background_colors.description',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'colorPalette',
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'border_colors' => [
                'exclude' => true,
                'label' => $ll . '.border_colors',
                'description' => $ll . '.border_colors.description',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'colorPalette',
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
        ],
    ];

    // if spreadsheet extension is loaded we enable visibility of type and set default to "spreadsheet based" ;)
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('spreadsheets')) {
        $chartDataTcaConfig['columns']['type']['config']['renderType'] = '';
        $chartDataTcaConfig['columns']['type']['config']['default'] = 1;
    }

    return $chartDataTcaConfig;
})();

