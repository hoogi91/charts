<?php

defined('TYPO3_MODE') or die();

(static function ($extConfig = [], $extKey = 'charts') {
    $ll = sprintf('LLL:EXT:%s/Resources/Private/Language/locallang_db.xlf:', $extKey);

    // add new columns to tt_content to filter contacts from tt_address
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tt_content',
        [
            'tx_charts_chartdata' => [
                'exclude' => true,
                'l10n_mode' => 'copy',
                'label' => $ll . 'tt_content.tx_charts_chartdata',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_charts_domain_model_chartdata',
                    'size' => 1,
                    'minitems' => 1,
                    'maxitems' => 1,
                    'default' => 0,
                ],
            ],
        ]
    );

    // IMPORTANT! add this before type configuration so it's possible to check if pi_flexform field is needed
    // get current chart library and add flexform data structures if library supports them
    if (is_array($extConfig)) {
        /** @var \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry $libraryRegistry */
        $libraryRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry::class
        );
        $chartLibrary = $libraryRegistry->getLibrary($extConfig['library']);

        // add all data structures of library to tt_content TCA
        if ($chartLibrary instanceof \Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface) {
            foreach ($chartLibrary->getDataStructures() as $type => $structure) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', $structure, $type);
            }
        }
    }

    // configure all chart types in TCA
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = [
        sprintf('%s:tt_content.CType.div._charts_', \Hoogi91\Charts\Form\Types\ChartTypeInterface::LANGUAGE_FILE),
        '--div--',
    ];
    \Hoogi91\Charts\Form\Types\BarChart::register();
    \Hoogi91\Charts\Form\Types\LineChart::register();
    \Hoogi91\Charts\Form\Types\PieChart::register();
    \Hoogi91\Charts\Form\Types\DoughnutChart::register();
})(
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['charts'] ?? unserialize(
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['charts'],
        ['allowed_classes' => false]
    )
);
