<?php

namespace Hoogi91\Charts\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ChartDataPlain
 * @package Hoogi91\Charts\Domain\Model
 */
class ChartDataPlain extends ChartData
{
    /**
     * @param string $labelData
     *
     * @return array
     */
    protected function extractLabelList(string $labelData): array
    {
        // @todo call to xml2array fixes only old elements and should be removed in further versions
        $data = GeneralUtility::xml2array($labelData);
        if (!is_array($data)) {
            // parse default TYPO3 table layout
            $data = array_map(static function ($item) {
                return (array)explode('|', trim($item, '| '));
            }, array_filter((array)explode("\n", $labelData)));
        }

        return array_values(array_map('array_values', $data));
    }

    /**
     * @param string $datasetData
     *
     * @return array
     */
    protected function extractDatasetList(string $datasetData): array
    {
        // @todo call to xml2array fixes only old elements and should be removed in further versions
        $data = GeneralUtility::xml2array($datasetData);
        if (!is_array($data)) {
            // parse default TYPO3 table layout
            $data = array_map(static function ($item) {
                return (array)explode('|', trim($item, '| '));
            }, array_filter((array)explode("\n", $datasetData)));
        }

        return array_values(
            array_map(
                static function ($item) {
                    return array_values(array_map('floatval', $item));
                },
                $data
            )
        );
    }
}
