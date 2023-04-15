<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface;
use Hoogi91\Charts\Form\Types\BarChart;
use Hoogi91\Charts\Form\Types\DoughnutChart;
use Hoogi91\Charts\Form\Types\LineChart;
use Hoogi91\Charts\Form\Types\PieChart;

use const ARRAY_FILTER_USE_KEY;

class ChartJs extends AbstractColoredLibrary implements LibraryFlexformInterface
{
    final public const TECHNICAL_NAME = 'ChartJS';
    final public const SERVICE_INDEX = 'chart.js';

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
            BarChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ChartJS/Bar.xml',
            LineChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ChartJS/Line.xml',
            PieChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ChartJS/DoughnutAndPie.xml',
            DoughnutChart::getIdentifier() => 'FILE:EXT:charts/Configuration/FlexForms/ChartJS/DoughnutAndPie.xml',
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
            'https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js'
        );

        return array_filter(
            [
                $cdnUrl => ['noConcat' => true],
                'typo3conf/ext/charts/Resources/Public/JavaScript/chartjs.js' => ['compress' => true],
            ],
            static fn ($key) => empty($key) === false,
            ARRAY_FILTER_USE_KEY
        );
    }
}
