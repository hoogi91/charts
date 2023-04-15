<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\ApexCharts;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Charts\Tests\Unit\ExtConfigTrait;
use Hoogi91\Charts\Tests\Unit\JavascriptCompareTrait;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ApexChartsTest extends UnitTestCase
{
    use ExtConfigTrait;
    use JavascriptCompareTrait;

    private ApexCharts $library;

    protected function setUp(): void
    {
        parent::setUp();
        $this->library = new ApexCharts($this->getExtensionConfig('apexcharts_js'));
    }

    /**
     * @return array<mixed>
     */
    public function chartDataProvider(): array
    {
        $mockConfig = [
            'getUid' => 123_456,
            'getLabels' => ['Label 1', 'Label 2', 'Label 3'],
            'getDatasets' => [
                ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ['Data 2-1', 'Data 2-2', 'Data 2-3'],
                ['Data 3-1', 'Data 3-2', 'Data 3-3'],
            ],
            'getBorderColors' => [
                'rgb(0, 0, 255)',
                'rgb(0, 255, 255)',
                'rgb(255, 255, 0)',
            ],
        ];

        return [
            'plain chart data' => [
                'chartData' => $this->createConfiguredMock(ChartData::class, $mockConfig),
                'expectedFile' => __DIR__ . '/entity_apexcharts.js',
            ],
            'spreadsheet chart data' => [
                'chartData' => $this->createConfiguredMock(ChartDataSpreadsheet::class, $mockConfig),
                'expectedFile' => __DIR__ . '/entity_apexcharts.js',
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
        $pageRenderer->expects(self::never())->method('addCssLibrary');

        $this->assertEmpty($this->library->getStylesheetAssets('bar', $pageRenderer));
    }

    public function testJavascriptAssetBuilding(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::atLeastOnce())->method('addJsFooterLibrary');

        $this->assertCount(2, $this->library->getJavascriptAssets('line', $pageRenderer));
    }

    /**
     * @dataProvider chartDataProvider
     *
     * @param ChartData&MockObject $model
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
     *
     * @param ChartData&MockObject $model
     */
    public function testJavascriptEntityBuilding(MockObject $model, string $expectedFile): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::exactly(2))
            ->method('addJsFooterInlineCode')
            ->withConsecutive(
                ['chartsInitialization', self::isType('string')],
                ['chartsData123456', self::isType('string')]
            );

        $javascript = $this->library->getEntityJavascript('test-identifier-123', 'doughnut', $model, $pageRenderer);
        $this->assertStringEqualsJavascriptFile($expectedFile, $javascript);
    }

    /**
     * @dataProvider spreadsheetMethodProvider
     *
     * @param array<mixed> $labels
     * @param array<mixed> $datasets
     */
    public function testEmptyJavascriptOnEmptyLabelsOrDataset(array $labels, array $datasets): void
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

    /**
     * @return array<mixed>
     */
    public function spreadsheetMethodProvider(): array
    {
        return [
            'empty labels' => [
                'getLabels' => [],
                'getDatasets' => [
                    ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ],
            ],
            'empty dataset' => [
                'getLabels' => ['Label 1', 'Label 2', 'Label 3'],
                'getDatasets' => [],
            ],
        ];
    }
}
