<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface;
use Hoogi91\Charts\Form\Types\BarChart;
use Hoogi91\Charts\Form\Types\DoughnutChart;
use Hoogi91\Charts\Form\Types\LineChart;
use Hoogi91\Charts\Form\Types\PieChart;

/**
 * Class ChartJs
 * @package Hoogi91\Charts\DataProcessing\Charts\Library
 */
class ChartJs extends AbstractColoredLibrary implements LibraryFlexformInterface
{
    public const NAME = 'ChartJS';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param int $type
     *
     * @return array
     */
    public function getDefaultColors($type = self::BACKGROUND): array
    {
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

    /**
     * @return array
     */
    protected function getJavascriptAssetsToLoad(): array
    {
        return [
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js' => [
                'noConcat' => true,
            ],
            'typo3conf/ext/charts/Resources/Public/JavaScript/chartjs.js' => [
                'compress' => true,
            ],
        ];
    }

    /**
     * please note that this is related to pointerField value:
     * https://docs.typo3.org/typo3cms/TCAReference/6.2/Reference/Columns/Flex/Index.html#ds
     *
     * this array should always contain the key 'default' which points to default data structure
     *
     * @return array
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
}
