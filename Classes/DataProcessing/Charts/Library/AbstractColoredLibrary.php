<?php

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;

abstract class AbstractColoredLibrary extends AbstractLibrary
{

    protected function buildEntityDatasetsForJavascript(array $datasets, ChartData $chartEntity): array
    {
        $processedDatasets = parent::buildEntityDatasetsForJavascript($datasets, $chartEntity);
        return array_map(
            fn($key) => [
                    'background' => $this->getBackgroundColors($chartEntity, $key, count($datasets[$key])),
                    'border' => $this->getBorderColors($chartEntity, $key, count($datasets[$key])),
                ] + $processedDatasets[$key],
            array_keys($datasets)
        );
    }

    private function getBackgroundColors(ChartData $chartEntity, int $dataKey, int $count = 1): array
    {
        $colors = $chartEntity instanceof ChartDataSpreadsheet
            ? $chartEntity->getBackgroundColors($dataKey)
            // TODO: implement palette
            //$chartEntity->getColorCollection()->getBackgroundPalette(),
            : [];

        return self::getColorListByPalette($colors, $count);
    }

    private function getBorderColors(ChartData $chartEntity, int $dataKey, int $count = 1): array
    {
        $colors = $chartEntity instanceof ChartDataSpreadsheet
            ? $chartEntity->getBorderColors($dataKey)
            // TODO: implement palette
            //$chartEntity->getColorCollection()->getBorderPalette(),
            : [];

        return self::getColorListByPalette($colors, $count);
    }

    protected static function getColorListByPalette(array $colorPalette, int $count = 1): array
    {
        $paletteSize = count($colorPalette);
        for ($i = 0; $i < $count; $i++) {
            $paletteIndex = ($paletteSize !== 0 && $i >= $paletteSize) ? ($i % $paletteSize) : $i;
            $colors[] = $colorPalette[$paletteIndex] ?? 'rgba(0, 0, 0, 0.1)';
        }

        return $colors ?? [];
    }
}
