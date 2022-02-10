<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\ApexCharts;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ApexChartsTest extends UnitTestCase
{

    private ApexCharts $library;

    protected function setUp(): void
    {
        parent::setUp();
        $this->library = new ApexCharts();
    }

    public function chartDataProvider(): array
    {
        $mockConfig = [
            'getUid' => 123456,
            'getLabels' => ['Label 1', 'Label 2', 'Label 3'],
            'getDatasets' => [
                ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ['Data 2-1', 'Data 2-2', 'Data 2-3'],
                ['Data 3-1', 'Data 3-2', 'Data 3-3'],
            ]
        ];

        return [
            'plain chart data' => [
                'chartData' => $this->createConfiguredMock(ChartData::class, $mockConfig),
            ],
            'spreadsheet chart data' => [
                'chartData' => $this->createConfiguredMock(ChartDataSpreadsheet::class, $mockConfig),
            ],
        ];
    }

    public function testProperReturnTypes(): void
    {
        $this->assertEquals(ApexCharts::TECHNICAL_NAME, $this->library->getName());
        $this->assertNotEmpty($this->library->getDataStructures());
    }

    public function testStylesheetAssetBuilding(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::atLeastOnce())->method('addCssLibrary');

        $this->assertCount(1, $this->library->getStylesheetAssets('bar', $pageRenderer));
    }

    public function testJavascriptAssetBuilding(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::atLeastOnce())->method('addJsFooterLibrary');

        $this->assertCount(2, $this->library->getJavascriptAssets('line', $pageRenderer));
    }

    /**
     * @dataProvider chartDataProvider
     * @param MockObject|ChartData $model
     */
    public function testStylesheetEntityBuilding(MockObject $model): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');

        $this->assertEmpty(
            $this->library->getEntityStylesheet('test-identifier-123', 'pie', $model, $pageRenderer)
        );
    }

    /**
     * @dataProvider chartDataProvider
     * @param MockObject|ChartData $model
     */
    public function testJavascriptEntityBuilding(MockObject $model): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::exactly(2))
            ->method('addJsFooterInlineCode')
            ->withConsecutive(
                ['chartsInitialization', self::isType('string')],
                ['chartsData123456', self::isType('string')]
            );

        $javascript = $this->library->getEntityJavascript('test-identifier-123', 'doughnut', $model, $pageRenderer);
        $this->assertStringContainsString('labels: ["Label 1","Label 2","Label 3"]', $javascript);
        // dataset 1
        $this->assertStringContainsString(
            '{"background":["rgba(255, 99, 132, 0.4)","rgba(255, 159, 64, 0.4)","rgba(255, 205, 86, 0.4)"],"border":["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)"],"data":["Data 1-1","Data 1-2","Data 1-3"],"label":""}',
            $javascript
        );
        // dataset 2
        $this->assertStringContainsString(
            '{"background":["rgba(255, 99, 132, 0.4)","rgba(255, 159, 64, 0.4)","rgba(255, 205, 86, 0.4)"],"border":["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)"],"data":["Data 2-1","Data 2-2","Data 2-3"],"label":""}',
            $javascript
        );
        // dataset 3
        $this->assertStringContainsString(
            '{"background":["rgba(255, 99, 132, 0.4)","rgba(255, 159, 64, 0.4)","rgba(255, 205, 86, 0.4)"],"border":["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)"],"data":["Data 3-1","Data 3-2","Data 3-3"],"label":""}]};',
            $javascript
        );
    }

    /**
     * @dataProvider spreadsheetMethodProvider
     */
    public function testEmptyJavascriptOnEmptyLabelsOrDataset($labels, $datasets): void
    {
        $this->assertEmpty(
            $this->library->getEntityJavascript(
                'test-identifier-123',
                'doughnut',
                $this->createConfiguredMock(
                    ChartDataSpreadsheet::class,
                    ['getLabels' => $labels, 'getDatasets' => $datasets]
                )
            )
        );
    }

    public function spreadsheetMethodProvider(): array
    {
        return [
            'empty labels' => [
                'getLabels' => [],
                'getDatasets' => [
                    ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ]
            ],
            'empty dataset' => [
                'getLabels' => ['Label 1', 'Label 2', 'Label 3'],
                'getDatasets' => []
            ],
        ];
    }
}
