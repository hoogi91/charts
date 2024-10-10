<?php

defined('TYPO3') or die();

(static function (string $extKey = 'charts') {

    // override TextTableElement to create fix for old XML values
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\TextTableElement::class] = [
        'className' => \Hoogi91\Charts\Controller\Wizard\TextTableElement::class,
    ];

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
