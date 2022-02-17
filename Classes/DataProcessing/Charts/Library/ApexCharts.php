<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface;
use Hoogi91\Charts\Form\Types\BarChart;
use Hoogi91\Charts\Form\Types\DoughnutChart;
use Hoogi91\Charts\Form\Types\LineChart;
use Hoogi91\Charts\Form\Types\PieChart;

class ApexCharts extends AbstractColoredLibrary implements LibraryFlexformInterface
{

    public const TECHNICAL_NAME = 'ApexCharts';
    public const SERVICE_INDEX = 'apexcharts.js';

    public static function getServiceIndex(): string
    {
        return self::SERVICE_INDEX;
    }

    public function getName(): string
    {
        return self::TECHNICAL_NAME;
    }

    public function getDataStructures(): array
    {
        return [
            BarChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/Bar.xml',
            LineChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/Line.xml',
            PieChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/DoughnutAndPie.xml',
            DoughnutChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/DoughnutAndPie.xml',
        ];
    }

    protected function getStylesheetAssetsToLoad(): array
    {
        // TODO: add option to define cdn url
        return [
            'https://cdn.jsdelivr.net/npm/apexcharts@3/dist/apexcharts.min.css' => [
                'noConcat' => true,
            ],
        ];
    }

    protected function getJavascriptAssetsToLoad(): array
    {
        // TODO: add option to define cdn url
        // TODO: add option if library assets should be loaded
        return [
            'https://cdn.jsdelivr.net/npm/apexcharts@3/dist/apexcharts.min.js' => [
                'noConcat' => true,
            ],
            'typo3conf/ext/charts/Resources/Public/JavaScript/apexcharts.js' => [
                'compress' => true,
            ],
        ];
    }
}
