<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Functional\ViewHelpers;

class GetChartDataViewHelperTest extends AbstractViewHelperTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->importDataSet(dirname(__DIR__, 2) . '/Fixtures/tx_charts_domain_model_chartdata.xml');
    }

    /**
     * @testWith ["1", "Chart Title 1"]
     *           ["2", "Chart Title 2"]
     *           ["", null]
     *           ["999999", null]
     */
    public function testGetChartData(string $chartUidList, ?string $expected): void
    {
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
