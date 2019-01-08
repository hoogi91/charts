<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use TYPO3\CMS\Core\Page\PageRenderer;
use Hoogi91\Charts\DataProcessing\Charts\LibraryFlexformInterface;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Charts\Form\Types\Chart;

/**
 * Class Chartist
 * @package Hoogi91\Charts\DataProcessing\Charts\Library
 */
class Chartist extends AbstractColoredLibrary implements LibraryFlexformInterface
{
    const NAME = 'Chartist';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param int $type
     *
     * @return array
     */
    public function getDefaultColors($type = self::BACKGROUND)
    {
        return [
            "rgba(255, 99, 132, 0.6)",
            "rgba(255, 159, 64, 0.6)",
            "rgba(255, 205, 86, 0.6)",
            "rgba(75, 192, 192, 0.6)",
            "rgba(54, 162, 235, 0.6)",
            "rgba(153, 102, 255, 0.6)",
            "rgba(201, 203, 207, 0.6)",
        ];
    }

    /**
     * @return array
     */
    protected function getStylesheetAssetsToLoad()
    {
        return [
            'https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.css' => [
                'noConcat' => true,
            ],
            'typo3conf/ext/charts/Resources/Public/Css/chartist.css'                  => [
                'compress' => true,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getJavascriptAssetsToLoad()
    {
        return [
            'https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.js' => [
                'noConcat' => true,
            ],
            'typo3conf/ext/charts/Resources/Public/JavaScript/chartist.js'           => [
                'compress' => true,
            ],
        ];
    }

    /**
     * @param string       $chartIdentifier
     * @param string       $chartType
     * @param ChartData    $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null)
    {
        $datasets = $chartEntity->getDatasets();
        $backgroundColorsByDataset = array_map(function ($dataKey) use ($datasets, $chartEntity) {
            if ($chartEntity instanceof ChartDataSpreadsheet) {
                // try to get background colors from spreadsheet
                $backgroundColors = $chartEntity->getBackgroundColors($dataKey);
                if (empty($backgroundColors)) {
                    $backgroundColors = $this->getBackgroundColors(count($datasets[$dataKey]));
                }
            } else {
                $backgroundColors = $this->getBackgroundColors(count($datasets[$dataKey]));
            }

            return $backgroundColors;
        }, array_keys($datasets));

        if (empty($backgroundColorsByDataset)) {
            return '';
        }

        // create stylesheet pattern
        $stylesheetPatternBarAndLines = implode(' ', [
            '#%1$s .ct-series-%2$s .ct-bar',
            '#%1$s .ct-series-%2$s .ct-line',
            '#%1$s .ct-series-%2$s .ct-point',
            '#%1$s .ct-series-%2$s .ct-slice-donut{stroke: %3$s}',
        ]);
        $stylesheetPatternAreaAndPie = implode(' ', [
            ' #%1$s .ct-series-%2$s .ct-area',
            '#%1$s .ct-series-%2$s .ct-slice-donut-solid',
            '#%1$s .ct-series-%2$s .ct-slice-pie{fill: %3$s}',
        ]);

        $stylesheet = '';
        foreach ($backgroundColorsByDataset as $key => $colors) {
            $stylesheet .= vsprintf($stylesheetPatternBarAndLines, [
                $chartIdentifier,
                chr((int)$key + 97),
                $colors[0],
            ]);
        }

        // for area and pie chart => only one dataset is allow => directly iterator over first dataset
        foreach ($backgroundColorsByDataset[0] as $key => $color) {
            $stylesheet .= vsprintf($stylesheetPatternAreaAndPie, [
                $chartIdentifier,
                chr((int)$key + 97),
                $color,
            ]);
        }

        if ($pageRenderer instanceof PageRenderer) {
            $pageRenderer->addCssInlineBlock($chartIdentifier, $stylesheet, true, true);
        }

        return $stylesheet;
    }

    /**
     * please note that this is related to pointerField value:
     * https://docs.typo3.org/typo3cms/TCAReference/6.2/Reference/Columns/Flex/Index.html#ds
     *
     * this array should always contain the key 'default' which points to default data structure
     *
     * @return array
     */
    public function getDataStructures()
    {
        return [
            Chart::TYPE_BAR      => 'FILE:EXT:charts/Configuration/FlexForms/Chartist/Bar.xml',
            Chart::TYPE_LINE     => 'FILE:EXT:charts/Configuration/FlexForms/Chartist/Line.xml',
            Chart::TYPE_DOUGHNUT => 'FILE:EXT:charts/Configuration/FlexForms/Chartist/Doughnut.xml',
        ];
    }
}
