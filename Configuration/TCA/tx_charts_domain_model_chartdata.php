<?php
return (function ($extKey) {
    $ll = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:tx_charts_domain_model_chartdata';

    // default chartdata tca configuration
    $chartDataTcaConfig = [
        'ctrl'      => [
            'title'                    => $ll,
            'label'                    => 'title',
            'tstamp'                   => 'tstamp',
            'crdate'                   => 'crdate',
            'cruser_id'                => 'cruser_id',
            'dividers2tabs'            => true,
            'versioningWS'             => true,
            'languageField'            => 'sys_language_uid',
            'transOrigPointerField'    => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete'                   => 'deleted',
            'enablecolumns'            => [
                'disabled'  => 'hidden',
                'starttime' => 'starttime',
                'endtime'   => 'endtime',
            ],
            'type'                     => 'type',
            'searchFields'             => 'title',
            'dynamicConfigFile'        => '',
            'iconfile'                 => 'EXT:' . $extKey . '/Resources/Public/Icons/Extension.svg',
        ],
        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, type, assets, labels, datasets',
        ],
        'types'     => [
            0 => [
                'showitem' => '--palette--;;corePalette,--palette--;;spreadsheetOptionPalette,labels,--palette--;;datasetPalette,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden,starttime,endtime',
            ],
            1 => [
                'showitem' => '--palette--;;corePalette,--palette--;;spreadsheetOptionPalette,spreadsheet_assets,spreadsheet_labels,--palette--;;spreadsheetDatasetPalette,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden,starttime,endtime',
            ],
        ],
        'palettes'  => [
            'corePalette'               => [
                'showitem' => 'title, sys_language_uid, l10n_parent, l10n_diffsource',
            ],
            'datasetPalette'            => [
                'showitem' => 'datasets, datasets_labels',
            ],
            'spreadsheetDatasetPalette' => [
                'showitem' => 'spreadsheet_datasets, spreadsheet_datasets_labels',
            ],
            'spreadsheetOptionPalette'  => [
                'showitem' => 'type, alignment',
            ],
        ],
        'columns'   => [
            'sys_language_uid'            => [
                'exclude' => 1,
                'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
                'config'  => [
                    'type'       => 'select',
                    'renderType' => 'selectSingle',
                    'special'    => 'languages',
                    'items'      => [
                        [
                            'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                            -1,
                            'flags-multiple',
                        ],
                    ],
                    'default'    => 0,
                ],
            ],
            'l10n_parent'                 => [
                'displayCond' => 'FIELD:sys_language_uid:>:0',
                'exclude'     => 1,
                'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
                'config'      => [
                    'type'                => 'select',
                    'renderType'          => 'selectSingle',
                    'items'               => [
                        0 => [
                            0 => '',
                            1 => 0,
                        ],
                    ],
                    'foreign_table'       => 'tx_charts_domain_model_chartdata',
                    'foreign_table_where' => 'AND tx_charts_domain_model_chartdata.pid=###CURRENT_PID### AND tx_charts_domain_model_chartdata.sys_language_uid IN (-1,0)',
                ],
            ],
            'l10n_diffsource'             => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            't3ver_label'                 => [
                'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'max'  => 255,
                ],
            ],
            'hidden'                      => [
                'exclude' => 1,
                'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
                'config'  => [
                    'type' => 'check',
                ],
            ],
            'starttime'                   => [
                'exclude' => 1,
                'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
                'config'  => [
                    'behaviour'  => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'renderType' => 'inputDateTime',
                    'type'       => 'input',
                    'size'       => 13,
                    'eval'       => 'datetime',
                    'checkbox'   => 0,
                    'default'    => 0,
                ],
            ],
            'endtime'                     => [
                'exclude' => 1,
                'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
                'config'  => [
                    'behaviour'  => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'renderType' => 'inputDateTime',
                    'type'       => 'input',
                    'size'       => 13,
                    'eval'       => 'datetime',
                    'checkbox'   => 0,
                    'default'    => 0,
                ],
            ],
            'parentid'                    => [
                'config' => [
                    'type'                => 'select',
                    'renderType'          => 'selectSingle',
                    'items'               => [
                        0 => [
                            0 => '',
                            1 => 0,
                        ],
                    ],
                    'foreign_table'       => 'tt_content',
                    'foreign_table_where' => 'AND tt_content.pid=###CURRENT_PID### AND tt_content.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',
                ],
            ],
            'parenttable'                 => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'title'                       => [
                'exclude' => true,
                'label'   => $ll . '.title',
                'config'  => [
                    'type' => 'input',
                    'eval' => 'required',
                ],
            ],
            'type'                        => [
                'exclude' => true,
                'label'   => $ll . '.type',
                'config'  => [
                    'type'       => 'check',
                    'renderType' => 'hidden',
                    'default'    => 0,
                ],
            ],
            'alignment'                   => [
                'exclude'     => true,
                'label'       => $ll . '.alignment',
                'displayCond' => 'FIELD:type:=:1',
                'config'      => [
                    'type'    => 'check',
                    'default' => 0,
                ],
            ],
            'labels'                      => [
                'exclude' => true,
                'label'   => $ll . '.labels',
                'config'  => [
                    'type'         => 'text',
                    'renderType'   => 'textTable',
                    'rows'         => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput'  => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour'    => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'datasets'                    => [
                'exclude' => true,
                'label'   => $ll . '.datasets',
                'config'  => [
                    'type'         => 'text',
                    'renderType'   => 'textTable',
                    'rows'         => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput'  => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour'    => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'datasets_labels'             => [
                'exclude' => true,
                'label'   => $ll . '.datasets_labels',
                'config'  => [
                    'type'         => 'text',
                    'renderType'   => 'textTable',
                    'rows'         => '3',
                    'fieldControl' => [
                        'tableWizard' => [
                            'options' => [
                                'xmlOutput'  => true,
                                'numNewRows' => 1,
                            ],
                        ],
                    ],
                    'behaviour'    => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_labels'          => [
                'exclude' => true,
                'label'   => $ll . '.spreadsheet_labels',
                'config'  => [
                    'type'        => 'text',
                    'renderType'  => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly'  => false,
                    'behaviour'   => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_datasets'        => [
                'exclude' => true,
                'label'   => $ll . '.spreadsheet_datasets',
                'config'  => [
                    'type'        => 'text',
                    'renderType'  => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly'  => false,
                    'behaviour'   => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_datasets_labels' => [
                'exclude' => true,
                'label'   => $ll . '.spreadsheet_datasets_labels',
                'config'  => [
                    'type'        => 'text',
                    'renderType'  => 'spreadsheetInput',
                    'uploadField' => 'spreadsheet_assets',
                    'sheetsOnly'  => false,
                    'behaviour'   => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'spreadsheet_assets'          => [
                'exclude' => true,
                'label'   => $ll . '.spreadsheet_assets',
                'config'  => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('assets', [
                    'appearance'       => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.asset_references.addFileReference',
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '--palette--;;filePalette',
                            ],
                        ],
                    ],
                ], 'xls,xlsx,ods'),
            ],
        ],
    ];

    // if spreadsheet extension is loaded we enable visibility of type and set default to "spreadsheet based" ;)
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('spreadsheets')) {
        $chartDataTcaConfig['columns']['type']['config']['renderType'] = '';
        $chartDataTcaConfig['columns']['type']['config']['default'] = 1;
    }

    return $chartDataTcaConfig;
})('charts');

