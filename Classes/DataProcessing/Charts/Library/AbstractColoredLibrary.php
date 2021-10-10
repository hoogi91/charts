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
    protected const BACKGROUND = 1;
    protected const BORDER = 2;

    /**
     * @param int $type
     *
     * @return array
     */
    abstract public function getDefaultColors(int $type = self::BACKGROUND): array;

    /**
     * map chart entities to short arrays with data for javascript processing
     *
     * @param array $datasets
     * @param ChartData $chartEntity
     *
     * @return array
     */
    protected function buildEntityDatasetsForJavascript(array $datasets, ChartData $chartEntity): array
    {
        // get processed datasets from above
        $processedDatasets = parent::buildEntityDatasetsForJavascript($datasets, $chartEntity);

        // process special mapping for spreadsheet based charts
        if ($chartEntity instanceof ChartDataSpreadsheet) {
            return array_map(
                function ($dataKey) use ($datasets, $processedDatasets, $chartEntity) {
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
                        'border' => $borderColors,
                    ];
                    return $additionalDatasetData + $processedDatasets[$dataKey];
                },
                array_keys($datasets)
            );
        }

        // create default mapping for all chart data entities
        return array_map(
            function ($dataKey) use ($datasets, $processedDatasets) {
                $additionalDatasetData = [
                    'background' => $this->getBackgroundColors(count($datasets[$dataKey])),
                    'border' => $this->getBorderColors(count($datasets[$dataKey])),
                ];
                return $additionalDatasetData + $processedDatasets[$dataKey];
            },
            array_keys($datasets)
        );
    }

    /**
     * @param int $count
     *
     * @return array
     */
    protected function getBackgroundColors(int $count = 1): array
    {
        return $this->getColor(self::BACKGROUND, $count);
    }

    /**
     * @param int $count
     *
     * @return array
     */
    protected function getBorderColors(int $count = 1): array
    {
        return $this->getColor(self::BORDER, $count);
    }

    /**
     * @param int $colorType
     * @param int $count
     *
     * @return array
     */
    private function getColor(int $colorType, int $count = 1): array
    {
        $defaultColors = $this->getDefaultColors($colorType);
        if (count($defaultColors) >= $count) {
            return array_slice($defaultColors, 0, $count);
        }

        $colors = $defaultColors;
        while (count($colors) < $count) {
            // get first color of default colors and remove it from there
            $firstItemOfDefaultColors = array_shift($defaultColors);

            // (re-)add first item to our default color and result array
            $defaultColors[] = $firstItemOfDefaultColors;
            $colors[] = $firstItemOfDefaultColors;
        }

        // final result should be a repeating array of our default colors
        return $colors;
    }
}
