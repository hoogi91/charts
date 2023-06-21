<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Domain\Model;

use Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\DsnValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\ExtractionValueObject;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Style\Border as CellBorder;
use PhpOffice\PhpSpreadsheet\Style\Color as CellColor;
use PhpOffice\PhpSpreadsheet\Style\Fill as CellBackground;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ChartDataSpreadsheet extends ChartData
{
    /**
     * @return array<array<mixed>>
     */
    protected function extractLabelList(string $labelData): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($labelData);
        } catch (SpreadsheetReaderException) {
            $datasetExtraction = null;
        }

        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->normalize($datasetExtraction);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item): void {
                if ($item instanceof CellDataValueObject) {
                    $item = $item->getRenderedValue();
                }
            }
        );

        return $spreadsheetData;
    }

    /**
     * @return array<array<mixed>>
     */
    protected function extractDatasetList(string $datasetData): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($datasetData);
        } catch (SpreadsheetReaderException) {
            $datasetExtraction = null;
        }

        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->normalize($datasetExtraction);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item): void {
                if ($item instanceof CellDataValueObject) {
                    $item = $item->getCalculatedValue();
                }
            }
        );

        return $spreadsheetData;
    }

    /**
     * @return array<mixed>
     */
    public function getBackgroundColors(int $dataKey = 0): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($this->datasets);
            $spreadsheet = $datasetExtraction->getSpreadsheet();
        } catch (SpreadsheetReaderException) {
            $datasetExtraction = $spreadsheet = null;
        }

        $spreadsheetData = array_slice($this->normalize($datasetExtraction), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData) ?? [];
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) use ($spreadsheet): void {
                $style = $spreadsheet !== null && $item instanceof CellDataValueObject
                    ? $spreadsheet->getCellXfByIndex($item->getStyleIndex())
                    : null;
                if ($style === null || $style->getFill()->getFillType() === CellBackground::FILL_NONE) {
                    $item = null;

                    return;
                }

                // get color from cell
                $color = $style->getFill()->getStartColor();
                $item = vsprintf(
                    'rgb(%s, %s, %s)',
                    [
                        hexdec((string)CellColor::getRed($color->getRGB())),
                        hexdec((string)CellColor::getGreen($color->getRGB())),
                        hexdec((string)CellColor::getBlue($color->getRGB())),
                    ]
                );
            }
        );

        // check if background colors have been found or only default color has been set
        $uniqueBackgroundColors = array_values(array_unique($spreadsheetData));

        return count($uniqueBackgroundColors) > 1 || isset($uniqueBackgroundColors[0])
            ? array_values(array_filter($spreadsheetData))
            : parent::getBackgroundColors();
    }

    /**
     * @return array<mixed>
     */
    public function getBorderColors(int $dataKey = 0): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($this->datasets);
            $spreadsheet = $datasetExtraction->getSpreadsheet();
        } catch (SpreadsheetReaderException) {
            $datasetExtraction = $spreadsheet = null;
        }

        $spreadsheetData = array_slice($this->normalize($datasetExtraction), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData) ?? [];
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) use ($spreadsheet): void {
                $style = $spreadsheet !== null && $item instanceof CellDataValueObject
                    ? $spreadsheet->getCellXfByIndex($item->getStyleIndex())
                    : null;
                if ($style === null) {
                    $item = null;

                    return;
                }

                // filter border data and return only rgb value
                $borders = array_map(
                    static fn (CellBorder $border) => $border->getColor()->getRGB(),
                    array_filter(
                        [
                            'top' => $style->getBorders()->getTop(),
                            'bottom' => $style->getBorders()->getBottom(),
                            'left' => $style->getBorders()->getLeft(),
                            'right' => $style->getBorders()->getRight(),
                        ],
                        static fn (CellBorder $border) => $border->getBorderStyle() !== CellBorder::BORDER_NONE
                    )
                );
                if (empty($borders)) {
                    $item = null;

                    return;
                }

                $borderCounts = array_count_values($borders);
                arsort($borderCounts);
                $borderCountKeys = array_keys($borderCounts);

                // extract border color
                $borderColor = array_shift($borderCountKeys);
                $item = vsprintf(
                    'rgb(%s, %s, %s)',
                    [
                        hexdec((string)CellColor::getRed((string) $borderColor)),
                        hexdec((string)CellColor::getGreen((string) $borderColor)),
                        hexdec((string)CellColor::getBlue((string) $borderColor)),
                    ]
                );
            }
        );

        // check if border colors have been found or only default color has been set
        $uniqueBorderColors = array_values(array_unique($spreadsheetData));

        return count($uniqueBorderColors) > 1 || isset($uniqueBorderColors[0])
            ? array_values(array_filter($spreadsheetData))
            : parent::getBorderColors();
    }

    /**
     * @return array<array<CellDataValueObject>>
     */
    private function normalize(?ExtractionValueObject $extraction): array
    {
        // get cell data from database value with spreadsheet extractor or return empty data
        /** @var array<array<CellDataValueObject>> $cellData */
        $cellData = $extraction?->getBodyData() ?? [];

        // only get zero-indexed value arrays
        return array_values(array_map(static fn ($data) => array_values($data), $cellData));
    }

    private function extractByDSN(string $dsn): ExtractionValueObject
    {
        $extractorService = GeneralUtility::makeInstance(ExtractorService::class);

        return $extractorService->getDataByDsnValueObject(DsnValueObject::createFromDSN($dsn));
    }
}
