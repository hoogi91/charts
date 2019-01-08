<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;

/**
 * Class AbstractColoredLibrary
 * @package Hoogi91\Charts\DataProcessing\Charts\Library
 */
abstract class AbstractColoredLibrary extends AbstractLibrary
{
    const BACKGROUND = 1;
    const BORDER = 2;

    /**
     * @param int $type
     *
     * @return array
     */
    abstract public function getDefaultColors($type = self::BACKGROUND);

    /**
     * map chart entities to short arrays with data for javascript processing
     *
     * @param array     $datasets
     * @param ChartData $chartEntity
     *
     * @return array
     */
    protected function buildEntityDatasetsForJavascript($datasets, $chartEntity)
    {
        // get processed datasets from above
        $processedDatasets = parent::buildEntityDatasetsForJavascript($datasets, $chartEntity);

        // process special mapping for spreadsheet based charts
        if ($chartEntity instanceof ChartDataSpreadsheet) {
            return array_map(function ($dataKey) use ($datasets, $processedDatasets, $chartEntity) {
                // try to get background colors from spreadsheet
                $backgroundColors = $chartEntity->getBackgroundColors($dataKey);
                if (empty($backgroundColors)) {
                    $backgroundColors = $this->getBackgroundColors(count($datasets[$dataKey]));
                }

                // try to get border colors from spreadsheet
                $borderColors = $chartEntity->getBorderColors($dataKey);
                if (empty($borderColors)) {
                    $borderColors = $this->getBorderColors(count($datasets[$dataKey]));
                }

                $additionalDatasetData = [
                    'background' => $backgroundColors,
                    'border'     => $borderColors,
                ];
                return $additionalDatasetData + $processedDatasets[$dataKey];
            }, array_keys($datasets));
        }

        // create default mapping for all chart data entities
        return array_map(function ($dataKey) use ($datasets, $processedDatasets) {
            $additionalDatasetData = [
                'background' => $this->getBackgroundColors(count($datasets[$dataKey])),
                'border'     => $this->getBorderColors(count($datasets[$dataKey])),
            ];
            return $additionalDatasetData + $processedDatasets[$dataKey];
        }, array_keys($datasets));
    }

    /**
     * @param int $count
     *
     * @return array
     */
    protected function getBackgroundColors($count = 1)
    {
        $defaultColors = $this->getDefaultColors(self::BACKGROUND);
        if (count($defaultColors) >= $count) {
            return array_slice($defaultColors, 0, $count);
        }

        $colors = $defaultColors;
        while (count($colors) < $count) {
            // get first color of default colors and remove it from there
            $firstItemOfDefaultColors = array_shift($defaultColors);

            // (re-)add first item to our default color and result array
            array_push($defaultColors, $firstItemOfDefaultColors);
            array_push($colors, $firstItemOfDefaultColors);
        }

        // final result should be a repeating array of our defaultcolors
        return $colors;
    }

    /**
     * @param int $count
     *
     * @return array
     */
    protected function getBorderColors($count = 1)
    {
        $defaultColors = $this->getDefaultColors(self::BORDER);
        if (count($defaultColors) >= $count) {
            return array_slice($defaultColors, 0, $count);
        }

        $colors = $defaultColors;
        while (count($colors) < $count) {
            // get first color of default colors and remove it from there
            $firstItemOfDefaultColors = array_shift($defaultColors);

            // (re-)add first item to our default color and result array
            array_push($defaultColors, $firstItemOfDefaultColors);
            array_push($colors, $firstItemOfDefaultColors);
        }

        // final result should be a repeating array of our defaultcolors
        return $colors;
    }
}
