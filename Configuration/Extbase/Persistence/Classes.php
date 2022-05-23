<?php

declare(strict_types=1);

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataPlain;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return (static function () {
    $data = [
        ChartData::class => [
            'subclasses' => [ChartDataPlain::class],
            'properties' => [
                // TODO: update these fallback properties when TYPO3 supports array types in data mapper
                // see TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::thawProperties
                'databaseBackground' => [
                    'fieldName' => 'background_colors'
                ],
                'databaseBorder' => [
                    'fieldName' => 'border_colors'
                ],
            ],
        ],
        ChartDataPlain::class => [
            'tableName' => 'tx_charts_domain_model_chartdata',
            'recordType' => 0,
        ],
    ];

    if (ExtensionManagementUtility::isLoaded('spreadsheets')) {
        $data[ChartData::class]['subclasses'][] = ChartDataSpreadsheet::class;
        $data[ChartDataSpreadsheet::class] = [
            'tableName' => 'tx_charts_domain_model_chartdata',
            'recordType' => 1,
            'properties' => [
                'labels' => [
                    'fieldName' => 'spreadsheet_labels'
                ],
                'datasets' => [
                    'fieldName' => 'spreadsheet_datasets'
                ],
                'datasetsLabels' => [
                    'fieldName' => 'spreadsheet_datasets_labels'
                ],
            ],
        ];
    }

    return $data;
})();
