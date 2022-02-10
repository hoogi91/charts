<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;

abstract class AbstractColoredLibrary extends AbstractLibrary
{
    protected const BACKGROUND = 1;
    protected const BORDER = 2;

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
                        $backgroundColors = $this->getColor(self::BACKGROUND, count($datasets[$dataKey]));
                    }

                    // try to get border colors from spreadsheet
                    $borderColors = $chartEntity->getBorderColors($dataKey);
                    if (empty($borderColors)) {
                        $borderColors = $this->getColor(self::BORDER, count($datasets[$dataKey]));
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
                    'background' => $this->getColor(self::BACKGROUND, count($datasets[$dataKey])),
                    'border' => $this->getColor(self::BORDER, count($datasets[$dataKey])),
                ];
                return $additionalDatasetData + $processedDatasets[$dataKey];
            },
            array_keys($datasets)
        );
    }

    abstract protected function getDefaultColors(int $type = self::BACKGROUND): array;

    protected function getColor(int $colorType, int $count = 1): array
    {
        $colorPalette = $this->getDefaultColors($colorType);
        $paletteSize = count($colorPalette);

        for ($i = 0; $i < $count; $i++) {
            $paletteIndex = $i >= $paletteSize ? ($i % $paletteSize) : $i;
            $colors[] = $colorPalette[$paletteIndex];
        }

        return $colors ?? [];
    }
}
