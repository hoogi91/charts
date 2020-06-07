<?php

namespace Hoogi91\Charts\Domain\Model;

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
    protected function extractLabelList($labelData): array
    {
        $data = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($labelData);
        if (!is_array($data)) {
            return [];
        }
        return array_values(array_map('array_values', $data));
    }

    /**
     * @param string $datasetData
     *
     * @return array
     */
    protected function extractDatasetList($datasetData): array
    {
        $data = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($datasetData);
        if (!is_array($data)) {
            return [];
        }
        return array_values(
            array_map(
                static function ($data) {
                    return array_values(array_map('floatval', $data));
                },
                $data
            )
        );
    }
}
