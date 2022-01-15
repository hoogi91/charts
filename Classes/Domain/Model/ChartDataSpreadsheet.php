<?php

namespace Hoogi91\Charts\Domain\Model;

use Hoogi91\Charts\Utility\ExtensionUtility;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border as CellBorder;
use PhpOffice\PhpSpreadsheet\Style\Borders as CellBorders;
use PhpOffice\PhpSpreadsheet\Style\Color as CellColor;
use PhpOffice\PhpSpreadsheet\Style\Fill as CellBackground;
use PhpOffice\PhpSpreadsheet\Style\Style as CellStyle;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ChartDataSpreadsheet
 * @package Hoogi91\Charts\Domain\Model
 */
class ChartDataSpreadsheet extends ChartData
{
    public const ALIGNMENT_HORIZONTAL = 0;
    public const ALIGNMENT_VERTICAL = 1;

    /**
     * @var int
     */
    protected $alignment = self::ALIGNMENT_HORIZONTAL;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $assets;

    /**
     * ChartData constructor.
     */
    public function initializeObject(): void
    {
        // @phpstan-ignore-next-line
        $this->assets = new ObjectStorage();
    }

    /**
     * @return int
     */
    public function getAlignment(): int
    {
        return $this->alignment;
    }

    /**
     * @param int $alignment
     */
    public function setAlignment($alignment): void
    {
        $this->alignment = $alignment;
    }

    /**
     * @return ObjectStorage
     */
    public function getAssets(): ObjectStorage
    {
        return $this->assets;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $assets
     */
    public function setAssets($assets): void
    {
        $this->assets = $assets;
    }

    /**
     * @param int $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBackgroundColors($dataKey, $defaultColor = 'rgba(0, 0, 0, 0.1)'): array
    {
        $spreadsheetData = array_slice($this->getSpreadsheetData($this->datasets), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData);
        array_walk_recursive(
            $spreadsheetData,
            function (&$item) use ($defaultColor) {
                $style = null;
                if ($item instanceof \Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject) {
                    $style = $this->getDatasetSpreadsheetStyleByIndex($item->getStyleIndex());
                } elseif ($item instanceof \Hoogi91\Spreadsheets\Domain\Model\CellValue) {// @phpstan-ignore-line
                    /** @deprecated since v1.1.0 and will be removed in v2.0 */
                    $style = $this->getStyleFromSpreadsheet($item->getCell());// @phpstan-ignore-line
                }

                if (!$style instanceof CellStyle || $style->getFill()->getFillType() === CellBackground::FILL_NONE) {
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
        if (count($uniqueBackgroundColors) === 1 && $uniqueBackgroundColors[0] === $defaultColor) {
            return [];
        }

        return array_values($spreadsheetData);
    }

    /**
     * @param int $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBorderColors($dataKey, $defaultColor = 'rgba(0, 0, 0, 0.1)'): array
    {
        $spreadsheetData = array_slice($this->getSpreadsheetData($this->datasets), $dataKey, 1);
        $spreadsheetData = array_shift($spreadsheetData);
        array_walk_recursive(
            $spreadsheetData,
            function (&$item) use ($defaultColor) {
                $style = null;
                if ($item instanceof \Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject) {
                    $style = $this->getDatasetSpreadsheetStyleByIndex($item->getStyleIndex());
                } elseif ($item instanceof \Hoogi91\Spreadsheets\Domain\Model\CellValue) {// @phpstan-ignore-line
                    /** @deprecated since v1.1.0 and will be removed in v2.0 */
                    $style = $this->getStyleFromSpreadsheet($item->getCell());// @phpstan-ignore-line
                }

                if (!$style instanceof CellStyle || !$style->getBorders() instanceof CellBorders) {
                    $item = $defaultColor;
                    return;
                }

                // filter border data and return only rgb value
                $borders = array_map(
                    static function ($border) {
                        /** @var CellBorder $border */
                        return $border->getColor()->getRGB();
                    },
                    array_filter(
                        [
                            'top' => $style->getBorders()->getTop(),
                            'bottom' => $style->getBorders()->getBottom(),
                            'left' => $style->getBorders()->getLeft(),
                            'right' => $style->getBorders()->getRight(),
                        ],
                        static function ($border) {
                            /** @var CellBorder $border */
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
        if (count($uniqueBorderColors) === 1 && $uniqueBorderColors[0] === $defaultColor) {
            return [];
        }

        return array_values($spreadsheetData);
    }

    /**
     * @param string $labelData
     *
     * @return array
     */
    protected function extractLabelList($labelData): array
    {
        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->getSpreadsheetData($labelData);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) {
                if ($item instanceof \Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject) {
                    $item = $item->getRenderedValue();
                } elseif ($item instanceof \Hoogi91\Spreadsheets\Domain\Model\CellValue) {// @phpstan-ignore-line
                    /** @deprecated since v1.1.0 and will be removed in v2.0 */
                    $item = $item->getValue();// @phpstan-ignore-line
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
    protected function extractDatasetList($datasetData): array
    {
        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->getSpreadsheetData($datasetData);
        array_walk_recursive(
            $spreadsheetData,
            static function (&$item) {
                if ($item instanceof \Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject) {
                    $item = (float)$item->getRenderedValue();
                } elseif ($item instanceof \Hoogi91\Spreadsheets\Domain\Model\CellValue) {// @phpstan-ignore-line
                    /** @deprecated since v1.1.0 and will be removed in v2.0 */
                    $item = (float)$item->getValue();// @phpstan-ignore-line
                }
            }
        );
        return $spreadsheetData;
    }

    /**
     * @param Cell $cell
     *
     * @return CellStyle|null
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    protected function getStyleFromSpreadsheet($cell): ?CellStyle
    {
        if (!$cell instanceof Cell) {
            return null;
        }

        $object = $cell;
        while (method_exists($object, 'getParent') && !$object instanceof Spreadsheet) {
            $object = $object->getParent();
        }

        if (!$object instanceof Spreadsheet) {
            return null;
        }
        return $object->getCellXfByIndex($cell->getXfIndex());
    }

    /**
     * @param string $dataValue
     *
     * @return array
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    protected function getSpreadsheetData($dataValue): array
    {
        // try to get value from cache
        $cache = null;
        try {
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $cache = $cacheManager->getCache(
            // @phpstan-ignore-next-line
                version_compare(TYPO3_version, '10.0', '>=') ? 'charts_data' : 'cache_charts_data'
            );

            // If $entry is found, it has been cached => return that value
            $cacheIdentifier = md5(trim($dataValue));
            if (($entry = $cache->get($cacheIdentifier)) !== false) {
                return $entry;
            }
        } catch (\TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e) {
            // cache couldn't be found => further process but keep in mind that cache couldn't be defined ;)
        }

        // get cell data from database value with spreadsheet extractor or return empty data
        $cellData = $this->getCellDataFromDatabaseString($dataValue);
        if (empty($cellData) || !is_array($cellData)) {
            return [];
        }

        // convert array structure if alignment is vertical instead of horizontal
        if ($this->getAlignment() === static::ALIGNMENT_VERTICAL) {
            $cellData = $this->flipCellData($cellData);
        }

        // only get zero-indexed value arrays
        $entry = array_values(array_map('array_values', $cellData));

        // save value in cache when identifier and cache is available
        if (!empty($cacheIdentifier) && $cache instanceof FrontendInterface) {
            $pageUid = static::getTyposcriptFrontendController()->page['uid'];
            $cache->set(
                $cacheIdentifier,
                $entry,
                [
                    sprintf('pageId_%d', $pageUid),
                    sprintf('chartDataId_%d', $this->getUid()),
                ]
            );
        }

        return $entry;
    }

    /**
     * Flip given cell data
     *
     * @param array $cellData
     *
     * @return array
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    private function flipCellData(array $cellData): array
    {
        if (ExtensionUtility::hasSpreadsheetExtensionWithDirectionSupport() === true) {
            return $cellData;
        }

        $resultData = [];
        foreach ($cellData as $row => $columns) {
            if (empty($columns)) {
                continue;
            }
            foreach ($columns as $column => $cell) {
                $resultData[$column][$row] = $cell;
            }
        }
        return $resultData;
    }

    /**
     * @param int $index
     * @return CellStyle|null
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    protected function getDatasetSpreadsheetStyleByIndex(int $index): ?CellStyle
    {
        // get spreadsheet DSN value from content object to parse and render
        try {
            $dsnValue = \Hoogi91\Spreadsheets\Domain\ValueObject\DsnValueObject::createFromDSN($this->datasets);

            /** @var \Hoogi91\Spreadsheets\Service\ReaderService $readerService */
            $readerService = GeneralUtility::makeInstance(\Hoogi91\Spreadsheets\Service\ReaderService::class);
            $spreadsheet = $readerService->getSpreadsheet($dsnValue->getFileReference());
            $spreadsheet->setActiveSheetIndex($dsnValue->getSheetIndex());

            return $spreadsheet->getCellXfByIndex($index);
        } catch (SpreadsheetReaderException $e) {
            return null;
        }
    }

    /**
     * @param string|null $data
     *
     * @return array
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    protected function getCellDataFromDatabaseString(?string $data = null): array
    {
        if (empty($data) || !is_string($data)) {
            return [];
        }

        // @phpstan-ignore-next-line
        if (version_compare(TYPO3_version, '10.0', '<')) {
            return $this->legacyGetCellData($data);
        }

        try {
            // get spreadsheet DSN value from content object to parse and render
            /** @var ExtractorService $spreadsheetExtractor */
            $spreadsheetExtractor = GeneralUtility::makeInstance(ExtractorService::class);
            $dsnValue = \Hoogi91\Spreadsheets\Domain\ValueObject\DsnValueObject::createFromDSN($data);
            $extraction = $spreadsheetExtractor->getDataByDsnValueObject($dsnValue, false);
            if ($extraction === null) { // @phpstan-ignore-line
                return [];
            }

            return $extraction->getBodyData();
        } catch (\Hoogi91\Spreadsheets\Exception\InvalidDataSourceNameException $e) {
            return [];
        }
    }

    /**
     * @param string|null $data
     * @return array
     * @deprecated since v1.1.0 and will be removed in v2.0
     */
    private function legacyGetCellData(?string $data = null): array
    {
        // @phpstan-ignore-next-line
        $spreadsheetValue = \Hoogi91\Spreadsheets\Domain\Model\SpreadsheetValue::createFromDatabaseString($data);
        // @phpstan-ignore-next-line
        $spreadsheetExtractor = ExtractorService::createFromSpreadsheetValue($spreadsheetValue);
        if (!$spreadsheetExtractor instanceof ExtractorService) {
            return [];
        }

        try {
            if (ExtensionUtility::hasSpreadsheetExtensionWithDirectionSupport() === true) {
                return $spreadsheetExtractor->rangeToCellArray(// @phpstan-ignore-line
                    $spreadsheetValue->getSelection(),
                    false, // @phpstan-ignore-line
                    true, // @phpstan-ignore-line
                    false,
                    $spreadsheetValue->getDirectionOfSelection()
                );
            }

            // @phpstan-ignore-next-line
            return $spreadsheetExtractor->rangeToCellArray($spreadsheetValue->getSelection(), false, true, false);
        } catch (SpreadsheetException $e) {
            return [];
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected static function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
