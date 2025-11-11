<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

use TYPO3\CMS\Core\Database\Connection;

class GetChartDataViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @testWith ["1", "Chart Title 1"]
     *           ["2", "Chart Title 2"]
     *           ["", null]
     *           ["999999", null]
     */
    public function testGetChartData(string $chartUidList, ?string $expected): void
    {
        if (method_exists(Connection::class, 'createSchemaManager') === false) {
            $this->markTestSkipped(
                'Testing framework can not handle data import without this method which is missing below TYPO3 v12.'
            );
        }
        $this->importCSVDataSet(dirname(__DIR__, 2) . '/Fixtures/tx_charts_domain_model_chartdata.csv');
        self::assertSame(
            $expected,
            $this->getView(
                '<f:alias map="{chartData: \'{test:getChartData(list: tx_charts_chartdata)}\'}">' .
                    '{chartData.0.title}' .
                '</f:alias>',
                ['tx_charts_chartdata' => $chartUidList]
            )->render()
        );
    }
}
