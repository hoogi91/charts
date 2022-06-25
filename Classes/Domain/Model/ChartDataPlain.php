<?php

namespace Hoogi91\Charts\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ChartDataPlain extends ChartData
{

    protected function extractLabelList(string $labelData): array
    {
        // @todo call to xml2array fixes only old elements and should be removed in further versions
        $data = GeneralUtility::xml2array($labelData);
        if (!is_array($data)) {
            // parse default TYPO3 table layout
            $data = array_map(static function ($item) {
                return explode('|', trim($item, '| '));
            }, array_filter(explode("\n", $labelData)));
        }

        return array_values(array_map('array_values', $data));
    }

    protected function extractDatasetList(string $datasetData): array
    {
        // @todo call to xml2array fixes only old elements and should be removed in further versions
        $data = GeneralUtility::xml2array($datasetData);
        if (!is_array($data)) {
            // parse default TYPO3 table layout
            $data = array_map(static function ($item) {
                return explode('|', trim($item, '| '));
            }, array_filter(explode("\n", $datasetData)));
        }

        return array_values(array_map(static fn($item) => array_map('floatval', array_values($item)), $data));
    }

    public function getDatasetsLabels(): array
    {
        $labels = $this->extractLabelList($this->datasetsLabels);
        if (count($labels) === 1) {
            // return first label row values if only one is given
            return $labels[0] ?? [];
        }

        // grab first column of every row as dataset labels
        return array_column($labels, '0');
    }
}
