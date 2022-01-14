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

/**
 * Class ChartDataSpreadsheet
 * @package Hoogi91\Charts\Domain\Model
 */
class ChartDataSpreadsheet extends ChartData
{

    /**
     * @var ExtractorService
     */
    private $extractorService;

    /**
     * @param ExtractorService $extractorService
     */
    public function injectExtractorService(ExtractorService $extractorService): void
    {
        $this->extractorService = $extractorService;
    }

    /**
     * @param string $labelData
     *
     * @return array
     */
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

    /**
     * @param string $datasetData
     *
     * @return array
     */
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

    /**
     * @param int $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBackgroundColors(int $dataKey, string $defaultColor = 'rgba(0, 0, 0, 0.1)'): array
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
            static function (&$item) use ($spreadsheet, $defaultColor) {
                $style = $spreadsheet !== null && $item instanceof CellDataValueObject
                    ? $spreadsheet->getCellXfByIndex($item->getStyleIndex())
                    : null;
                if ($style === null || $style->getFill()->getFillType() === CellBackground::FILL_NONE) {
                    $item = $defaultColor;
                    return;
                }

                // get color from cell
                $color = $style->getFill()->getStartColor();
                $item = vsprintf(
                    'rgb(%s, %s, %s)',
                    [
                        hexdec(CellColor::getRed($color->getRGB())),
                        hexdec(CellColor::getGreen($color->getRGB())),
                        hexdec(CellColor::getBlue($color->getRGB())),
                    ]
                );
            }
        );

        // check if background colors have been found or only default color has been set
        $uniqueBackgroundColors = array_values(array_unique($spreadsheetData));
        return count($uniqueBackgroundColors) > 1 || $uniqueBackgroundColors[0] !== $defaultColor
            ? array_values($spreadsheetData)
            : [];
    }

    /**
     * @param int $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBorderColors(int $dataKey, string $defaultColor = 'rgba(0, 0, 0, 0.1)'): array
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
            static function (&$item) use ($spreadsheet, $defaultColor) {
                $style = $spreadsheet !== null && $item instanceof CellDataValueObject
                    ? $spreadsheet->getCellXfByIndex($item->getStyleIndex())
                    : null;
                if ($style === null) {
                    $item = $defaultColor;
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
                    // return default if no valid borders are given
                    $item = $defaultColor;
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
                        hexdec(CellColor::getRed($borderColor)),
                        hexdec(CellColor::getGreen($borderColor)),
                        hexdec(CellColor::getBlue($borderColor)),
                    ]
                );
            }
        );

        // check if border colors have been found or only default color has been set
        $uniqueBorderColors = array_values(array_unique($spreadsheetData));
        return count($uniqueBorderColors) > 1 || $uniqueBorderColors[0] !== $defaultColor
            ? array_values($spreadsheetData)
            : [];
    }

    /**
     * @param ExtractionValueObject|null $extraction
     *
     * @return CellDataValueObject[][]
     */
    private function normalize(?ExtractionValueObject $extraction): array
    {
        // get cell data from database value with spreadsheet extractor or return empty data
        $cellData = $extraction !== null ? $extraction->getBodyData() : null;

        // only get zero-indexed value arrays
        return is_array($cellData) ? array_values(array_map('array_values', $cellData)) : [];
    }

    /**
     * @param string $dsn DSN to extract
     * @return ExtractionValueObject
     * @throws SpreadsheetReaderException
     */
    private function extractByDSN(string $dsn): ExtractionValueObject
    {
        return $this->extractorService->getDataByDsnValueObject(DsnValueObject::createFromDSN($dsn));
    }
}
