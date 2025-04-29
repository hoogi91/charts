<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;

abstract class AbstractColoredLibrary extends AbstractLibrary
{
    /**
     * @param array<array<mixed>> $datasets
     *
     * @return array<mixed>
     */
    protected function buildEntityDatasetsForJavascript(array $datasets, ChartData $chartEntity, string $chartType): array
    {
        $processedDatasets = parent::buildEntityDatasetsForJavascript($datasets, $chartEntity, $chartType);

        return array_map(
            static function (int $key) use ($chartEntity, $datasets, $processedDatasets, $chartType) {
                $backgroundColors = $chartEntity instanceof ChartDataSpreadsheet
                    ? $chartEntity->getBackgroundColors($key)
                    : $chartEntity->getBackgroundColors();

                $borderColors = $chartEntity instanceof ChartDataSpreadsheet
                    ? $chartEntity->getBorderColors($key)
                    : $chartEntity->getBorderColors();

                if ($chartType === 'chart_pie' || $chartType === 'chart_doughnut') {
                    $paletteSize = count($datasets[$key]);
                } else {
                    $paletteSize = count($datasets);
                }

                $backgroundColors = self::getColorListByPalette($backgroundColors, $paletteSize);
                $borderColors = self::getColorListByPalette($borderColors, $paletteSize, null);

                return ['background' => $backgroundColors, 'border' => $borderColors] + $processedDatasets[$key];
            },
            array_keys($datasets)
        );
    }

    /**
     * @param array<mixed> $colorPalette
     *
     * @return array<mixed>
     */
    protected static function getColorListByPalette(
        array $colorPalette,
        int $size = 1,
        ?string $defaultColor = 'rgba(0, 0, 0, 0.1)'
    ): array {
        $colors = [];
        // return single color if palette does not define at least two colors
        if (count($colorPalette) < 2) {
            return array_filter($colorPalette ?: [$defaultColor]);
        }

        $paletteSize = count($colorPalette);
        for ($i = 0; $i < $size; $i++) {
            $paletteIndex = $i >= $paletteSize ? $i % $paletteSize : $i;
            $colors[] = $colorPalette[$paletteIndex] ?? $defaultColor;
        }

        return array_filter($colors);
    }
}
