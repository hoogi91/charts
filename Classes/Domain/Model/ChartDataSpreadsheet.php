<?php

namespace Hoogi91\Charts\Domain\Model;

use Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\DsnValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\ExtractionValueObject;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Style\Border as CellBorder;
use PhpOffice\PhpSpreadsheet\Style\Color as CellColor;
use PhpOffice\PhpSpreadsheet\Style\Fill as CellBackground;

class ChartDataSpreadsheet extends ChartData
{

    private ExtractorService $extractorService;

    public function injectExtractorService(ExtractorService $extractorService): void
    {
        $this->extractorService = $extractorService;
    }

    protected function extractLabelList(string $labelData): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($labelData);
        } catch (SpreadsheetReaderException $e) { // @codeCoverageIgnoreStart
            $datasetExtraction = null; // @codeCoverageIgnoreEnd
        }

        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->normalize($datasetExtraction);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) {
                if ($item instanceof CellDataValueObject) {
                    $item = $item->getRenderedValue();
                }
            }
        );
        return $spreadsheetData;
    }

    protected function extractDatasetList(string $datasetData): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($datasetData);
        } catch (SpreadsheetReaderException $e) { // @codeCoverageIgnoreStart
            $datasetExtraction = null; // @codeCoverageIgnoreEnd
        }

        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->normalize($datasetExtraction);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) {
                if ($item instanceof CellDataValueObject) {
                    $item = (float)$item->getCalculatedValue();
                }
            }
        );
        return $spreadsheetData;
    }

    public function getBackgroundColors(int $dataKey = 0): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($this->datasets);
            $spreadsheet = $datasetExtraction->getSpreadsheet();
        } catch (SpreadsheetReaderException $e) { // @codeCoverageIgnoreStart
            $datasetExtraction = $spreadsheet = null; // @codeCoverageIgnoreEnd
        }

        $spreadsheetData = array_slice($this->normalize($datasetExtraction), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) use ($spreadsheet) {
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

    public function getBorderColors(int $dataKey = 0): array
    {
        try {
            $datasetExtraction = $this->extractByDSN($this->datasets);
            $spreadsheet = $datasetExtraction->getSpreadsheet();
        } catch (SpreadsheetReaderException $e) { // @codeCoverageIgnoreStart
            $datasetExtraction = $spreadsheet = null; // @codeCoverageIgnoreEnd
        }

        $spreadsheetData = array_slice($this->normalize($datasetExtraction), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) use ($spreadsheet) {
                $style = $spreadsheet !== null && $item instanceof CellDataValueObject
                    ? $spreadsheet->getCellXfByIndex($item->getStyleIndex())
                    : null;
                if ($style === null) {
                    $item = null;
                    return;
                }

                // filter border data and return only rgb value
                $borders = array_map(
                    static function (CellBorder $border) {
                        return $border->getColor()->getRGB();
                    },
                    array_filter(
                        [
                            'top' => $style->getBorders()->getTop(),
                            'bottom' => $style->getBorders()->getBottom(),
                            'left' => $style->getBorders()->getLeft(),
                            'right' => $style->getBorders()->getRight(),
                        ],
                        static function (CellBorder $border) {
                            return $border->getBorderStyle() !== CellBorder::BORDER_NONE;
                        }
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
                        hexdec((string)CellColor::getRed($borderColor)),
                        hexdec((string)CellColor::getGreen($borderColor)),
                        hexdec((string)CellColor::getBlue($borderColor)),
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
     * @return CellDataValueObject[][]
     */
    private function normalize(?ExtractionValueObject $extraction): array
    {
        // get cell data from database value with spreadsheet extractor or return empty data
        $cellData = $extraction !== null ? $extraction->getBodyData() : null;

        // only get zero-indexed value arrays
        return is_array($cellData) ? array_values(array_map('array_values', $cellData)) : [];
    }

    private function extractByDSN(string $dsn): ExtractionValueObject
    {
        return $this->extractorService->getDataByDsnValueObject(DsnValueObject::createFromDSN($dsn));
    }
}
