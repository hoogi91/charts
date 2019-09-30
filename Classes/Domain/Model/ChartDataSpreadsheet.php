<?php

namespace Hoogi91\Charts\Domain\Model;

use Hoogi91\Charts\Utility\ExtensionUtility;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
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
use Hoogi91\Spreadsheets\Domain\Model\CellValue;
use Hoogi91\Spreadsheets\Domain\Model\SpreadsheetValue;
use Hoogi91\Spreadsheets\Service\ExtractorService;

/**
 * Class ChartDataSpreadsheet
 * @package Hoogi91\Charts\Domain\Model
 */
class ChartDataSpreadsheet extends ChartData
{
    const ALIGNMENT_HORIZONTAL = 0;
    const ALIGNMENT_VERTICAL = 1;

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
    public function initializeObject()
    {
        $this->assets = new ObjectStorage();
    }

    /**
     * @return int
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @param int $alignment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * @return ObjectStorage
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @param ObjectStorage $assets
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param int    $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBackgroundColors($dataKey, $defaultColor = 'rgba(0, 0, 0, 0.1)')
    {
        $spreadsheetData = array_shift(array_slice($this->getSpreadsheetData($this->datasets), $dataKey, 1));
        array_walk_recursive($spreadsheetData, function (&$item) use ($defaultColor) {
            $result = $defaultColor;
            if ($item instanceof CellValue) {
                $style = $this->getStyleFromSpreadsheet($item->getCell());
                if ($style instanceof CellStyle && $style->getFill()->getFillType() !== CellBackground::FILL_NONE) {
                    $color = $style->getFill()->getStartColor();
                    $result = vsprintf('rgb(%s, %s, %s)', [
                        hexdec(CellColor::getRed($color->getRGB())),
                        hexdec(CellColor::getGreen($color->getRGB())),
                        hexdec(CellColor::getBlue($color->getRGB())),
                    ]);
                }
            }
            // set calculated result as new item value
            $item = $result;
        });

        // check if background colors have been found or only default color has been set
        $uniqueBackgroundColors = array_values(array_unique($spreadsheetData));
        if (count($uniqueBackgroundColors) === 1 && $uniqueBackgroundColors[0] === $defaultColor) {
            return [];
        }

        return array_values($spreadsheetData);
    }

    /**
     * @param int    $dataKey index of dataset to output
     * @param string $defaultColor
     *
     * @return array
     */
    public function getBorderColors($dataKey, $defaultColor = 'rgba(0, 0, 0, 0.1)')
    {
        $spreadsheetData = array_shift(array_slice($this->getSpreadsheetData($this->datasets), $dataKey, 1));
        array_walk_recursive($spreadsheetData, function (&$item) use ($defaultColor) {
            $result = $defaultColor;
            if ($item instanceof CellValue) {
                $style = $this->getStyleFromSpreadsheet($item->getCell());
                if ($style instanceof CellStyle && $style->getBorders() instanceof CellBorders) {
                    $rawBorderData = [
                        'top'    => $style->getBorders()->getTop(),
                        'bottom' => $style->getBorders()->getBottom(),
                        'left'   => $style->getBorders()->getLeft(),
                        'right'  => $style->getBorders()->getRight(),
                    ];

                    // filter border data and return only rgb value
                    $borders = array_map(function ($border) {
                        /** @var CellBorder $border */
                        return $border->getColor()->getRGB();
                    }, array_filter($rawBorderData, function ($border) {
                        /** @var CellBorder $border */
                        return $border->getBorderStyle() !== CellBorder::BORDER_NONE;
                    }));

                    // return new result only if borders are found ;)
                    if (!empty($borders)) {
                        $borderCount = array_count_values($borders);
                        arsort($borderCount);
                        $borderColor = array_shift(array_keys($borderCount));
                        $result = vsprintf('rgb(%s, %s, %s)', [
                            hexdec(CellColor::getRed($borderColor)),
                            hexdec(CellColor::getGreen($borderColor)),
                            hexdec(CellColor::getBlue($borderColor)),
                        ]);
                    }
                }
            }
            // set calculated result as new item value
            $item = $result;
        });

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
    protected function extractLabelList($labelData)
    {
        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->getSpreadsheetData($labelData);
        array_walk_recursive($spreadsheetData, function (&$item) {
            if ($item instanceof CellValue) {
                $item = $item->getValue();
            }
        });
        return $spreadsheetData;
    }

    /**
     * @param string $datasetData
     *
     * @return array
     */
    protected function extractDatasetList($datasetData)
    {
        // label data should be a reference/selection of spreadsheet data of an external asset
        $spreadsheetData = $this->getSpreadsheetData($datasetData);
        array_walk_recursive($spreadsheetData, function (&$item) {
            if ($item instanceof CellValue) {
                $item = floatval($item->getValue());
            }
        });
        return $spreadsheetData;
    }

    /**
     * @param Cell $cell
     *
     * @return null|\PhpOffice\PhpSpreadsheet\Style\Style
     */
    protected function getStyleFromSpreadsheet($cell)
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
     */
    protected function getSpreadsheetData($dataValue)
    {
        // try to get value from cache
        $cache = null;
        try {
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $cache = $cacheManager->getCache('cache_charts_data');

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
            $cache->set($cacheIdentifier, $entry, [
                sprintf('pageId_%d', $pageUid),
                sprintf('chartDataId_%d', $this->getUid()),
            ]);
        }

        return $entry;
    }

    /**
     * Flip given cell data
     *
     * @param array $cellData
     *
     * @return array
     * @deprecated
     */
    private function flipCellData(array $cellData):array
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
     * @param string $data
     *
     * @return array
     */
    protected function getCellDataFromDatabaseString($data = null)
    {
        if (empty($data) || !is_string($data)) {
            return [];
        }

        $spreadsheetValue = SpreadsheetValue::createFromDatabaseString($data);
        $spreadsheetExtractor = ExtractorService::createFromSpreadsheetValue($spreadsheetValue);
        if (!$spreadsheetExtractor instanceof ExtractorService) {
            return [];
        }

        try {
            if (ExtensionUtility::hasSpreadsheetExtensionWithDirectionSupport() === true) {
                return $spreadsheetExtractor->rangeToCellArray(
                    $spreadsheetValue->getSelection(),
                    false,
                    true,
                    false,
                    $spreadsheetValue->getDirectionOfSelection()
                );
            }

            return $spreadsheetExtractor->rangeToCellArray($spreadsheetValue->getSelection(), false, true, false);
        } catch (SpreadsheetException $e) {
            return [];
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
