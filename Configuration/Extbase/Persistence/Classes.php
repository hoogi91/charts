<?php

declare(strict_types=1);

return [
    \Hoogi91\Charts\Domain\Model\ChartData::class => [
        'subclasses' => [
            \Hoogi91\Charts\Domain\Model\ChartDataPlain::class,
            \Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet::class,
        ]
    ],
    \Hoogi91\Charts\Domain\Model\ChartDataPlain::class => [
        'tableName' => 'tx_charts_domain_model_chartdata',
        'recordType' => 0,
    ],
    \Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet::class => [
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
            'assets' => [
                'fieldName' => 'spreadsheet_assets'
            ],
        ],
    ],
];
