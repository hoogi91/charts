<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface;
use Hoogi91\Charts\Form\Types\BarChart;
use Hoogi91\Charts\Form\Types\DoughnutChart;
use Hoogi91\Charts\Form\Types\LineChart;
use Hoogi91\Charts\Form\Types\PieChart;

use const ARRAY_FILTER_USE_KEY;

class ApexCharts extends AbstractColoredLibrary implements LibraryFlexformInterface
{
    final public const TECHNICAL_NAME = 'ApexCharts';
    final public const SERVICE_INDEX = 'apexcharts.js';

    public static function getServiceIndex(): string
    {
        return self::SERVICE_INDEX;
    }

    public function getName(): string
    {
        return self::TECHNICAL_NAME;
    }

    /**
     * @return array<mixed>
     */
    public function getDataStructures(): array
    {
        return [
            BarChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/Bar.xml',
            LineChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/Line.xml',
            PieChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/DoughnutAndPie.xml',
            DoughnutChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ApexCharts/DoughnutAndPie.xml',
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function getStylesheetAssetsToLoad(): array
    {
        return [];
    }

    /**
     * @return array<mixed>
     */
    protected function getJavascriptAssetsToLoad(): array
    {
        $cdnUrl = $this->getLibraryConfig(
            'javascript',
            'https://cdn.jsdelivr.net/npm/apexcharts@3/dist/apexcharts.min.js'
        );

        return array_filter(
            [
                $cdnUrl => ['noConcat' => true],
                'EXT:charts/Resources/Public/JavaScript/apexcharts.js' => ['compress' => true],
            ],
            static fn ($key) => empty($key) === false,
            ARRAY_FILTER_USE_KEY
        );
    }
}
