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

    protected function getDefaultColors(int $type = self::BACKGROUND): array
    {
        // TODO: create extension config to define a list of default colors for background and borders

        if ($type === self::BACKGROUND) {
            return [
                "rgba(255, 99, 132, 0.4)",
                "rgba(255, 159, 64, 0.4)",
                "rgba(255, 205, 86, 0.4)",
                "rgba(75, 192, 192, 0.4)",
                "rgba(54, 162, 235, 0.4)",
                "rgba(153, 102, 255, 0.4)",
                "rgba(201, 203, 207, 0.4)",
            ];
        }

        return [
            "rgb(255, 99, 132)",
            "rgb(255, 159, 64)",
            "rgb(255, 205, 86)",
            "rgb(75, 192, 192)",
            "rgb(54, 162, 235)",
            "rgb(153, 102, 255)",
            "rgb(201, 203, 207)",
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
