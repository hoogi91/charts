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
            static function (int $key) use ($chartEntity, $datasets, $processedDatasets) {
                $backgroundColors = $chartEntity instanceof ChartDataSpreadsheet
                    ? $chartEntity->getBackgroundColors($key)
                    : $chartEntity->getBackgroundColors();

                $borderColors = $chartEntity instanceof ChartDataSpreadsheet
                    ? $chartEntity->getBorderColors($key)
                    : $chartEntity->getBorderColors();

                $paletteSize = count($datasets[$key]);
                $backgroundColors = self::getColorListByPalette($backgroundColors, $paletteSize);
                $borderColors = self::getColorListByPalette($borderColors, $paletteSize, null);

                return ['background' => $backgroundColors, 'border' => $borderColors] + $processedDatasets[$key];
            },
            array_keys($datasets)
        );
    }

    protected static function getColorListByPalette(
        array $colorPalette,
        int $size = 1,
        ?string $defaultColor = 'rgba(0, 0, 0, 0.1)'
    ): array {
        // return single color if palette does not define at least two colors
        if (count($colorPalette) < 2) {
            return array_filter($colorPalette ?: [$defaultColor]);
        }

        $paletteSize = count($colorPalette);
        for ($i = 0; $i < $size; $i++) {
            $paletteIndex = $i >= $paletteSize ? ($i % $paletteSize) : $i;
            $colors[] = $colorPalette[$paletteIndex] ?? $defaultColor;
        }

        return array_filter($colors ?? []);
    }
}
