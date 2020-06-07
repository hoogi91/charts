<?php

(static function ($extConfig = [], $extKey = 'charts') {
    /** @var \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry $libraryRegistry */
    $libraryRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry::class
    );
    $libraryRegistry->register('chart.js', \Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs::class, true);
    $libraryRegistry->register('chartist', \Hoogi91\Charts\DataProcessing\Charts\Library\Chartist::class, true);

    // register cache for generated data (as example => reading and transforming spreadsheet data)
    $cacheConfiguration = &$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'];
    if (empty($cacheConfiguration['cache_charts_data'])) {
        $cacheConfiguration['cache_charts_data'] = [];
    }
    if (!isset($cacheConfiguration['cache_charts_data']['frontend'])) {
        $cacheConfiguration['cache_charts_data']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
    }
    if (!isset($cacheConfiguration['cache_charts_data']['backend'])) {
        if (isset($extConfig['disableCaching']) && (bool)$extConfig['disableCaching'] === true) {
            $cacheConfiguration['cache_charts_data']['backend'] = \TYPO3\CMS\Core\Cache\Backend\NullBackend::class;
        } else {
            $cacheConfiguration['cache_charts_data']['backend'] = \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class;
        }
    }
    if (!isset($cacheConfiguration['cache_charts_data']['groups'])) {
        $cacheConfiguration['cache_charts_data']['groups'] = ['pages'];
    }
    if (!isset($cacheConfiguration['cache_charts_data']['options']['defaultLifetime'])) {
        $cacheConfiguration['cache_charts_data']['options']['defaultLifetime'] = 86400; // on default one day ;)
    }

    if (TYPO3_MODE === 'BE') {
        // add content element to insert tables in content element wizard
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/NewContentElementWizard.typoscript">'
        );

        // register template for backend preview rendering
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/BackendPreview.typoscript">'
        );

        // override default table controller to fix issue on empty configuration array and not visible table wizard!
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Controller\Wizard\TableController::class] = [
            'className' => \Hoogi91\Charts\Controller\Wizard\TableController::class,
        ];

        // register extension relevant icons
        $icons = [
            'chart' => 'Extension',
            'bar_chart' => 'BarChart',
            'line_chart' => 'LineChart',
            'pie_chart' => 'PieChart',
            'doughnut_chart' => 'DoughnutChart',
        ];
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        foreach ($icons as $key => $icon) {
            $iconRegistry->registerIcon(
                'tx_charts_' . strtolower($key),
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => sprintf('EXT:%s/Resources/Public/Icons/%s.svg', $extKey, $icon)]
            );
        }
    }
})(
    $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['charts'] ?? unserialize(
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['charts'],
        ['allowed_classes' => false]
    )
);
