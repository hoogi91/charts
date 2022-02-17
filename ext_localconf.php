<?php

defined('TYPO3') or die();

(static function (string $extKey = 'charts') {
    // add content element to insert tables in content element wizard
    // and register template for backend preview rendering
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/NewContentElementWizard.typoscript">
            <INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extKey . '/Configuration/PageTSconfig/BackendPreview.typoscript">'
    );

    if (class_exists(\TYPO3\CMS\Core\Information\Typo3Version::class)
        && version_compare((new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion(), '11.4', '>=') === true) {
        // override TextTableElement to create fix for old XML values
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\TextTableElement::class] = [
            'className' => \Hoogi91\Charts\Controller\Wizard\TextTableElement::class,
        ];
    } else {
        // override default table controller to fix issue on empty configuration array and not visible table wizard!
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Controller\Wizard\TableController::class] = [
            'className' => \Hoogi91\Charts\Controller\Wizard\TableController::class,
        ];
    }

    // add field type to form engine
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1644609317993] = [
        'nodeName' => 'colorPalette',
        'priority' => 30,
        'class' => \Hoogi91\Charts\Form\Element\ColorPaletteInputElement::class,
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
})();
