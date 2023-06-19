<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Charts\Tests\Unit\ExtConfigTrait;
use Hoogi91\Charts\Tests\Unit\JavascriptCompareTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Traversable;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartJsTest extends UnitTestCase
{
    use ExtConfigTrait;
    use JavascriptCompareTrait;

    private ChartJs $library;

    protected function setUp(): void
    {
        parent::setUp();
        $this->library = new ChartJs(self::getExtensionConfig('chart_js'));
    }

    /**
     * @return array<mixed>
     */
    public static function chartDataProvider(): array
    {
        $mockConfig = [
            'getUid' => 123456,
            'getLabelList' => ['Label 1', 'Label 2', 'Label 3'],
            'getDatasetList' => [
                ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ['Data 2-1', 'Data 2-2', 'Data 2-3'],
                ['Data 3-1', 'Data 3-2', 'Data 3-3'],
            ],
            'getBackgroundColors' => [
                'rgb(255, 0, 0)',
                'rgb(0, 255, 0)',
                'rgb(0, 0, 255)',
            ],
        ];

        return [
            'plain chart data' => [
                'chartData' => self::createMockInProvider(ChartData::class, $mockConfig),
                'expectedFile' => __DIR__ . '/entity_chartjs.js',
            ],
            'spreadsheet chart data' => [
                'chartData' => self::createMockInProvider(ChartDataSpreadsheet::class, $mockConfig),
                'expectedFile' => __DIR__ . '/entity_chartjs.js',
            ],
        ];
    }

    public function testProperReturnTypes(): void
    {
        $this->assertEquals(ChartJs::TECHNICAL_NAME, $this->library->getName());
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
     * @param ChartData&MockObject $model
     */
    public function testJavascriptEntityBuilding(MockObject $model, string $expectedFile): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects($matcher = $this->exactly(2))
            ->method('addJsFooterInlineCode')
            ->willReturnCallback(function (string $param) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals($param, 'chartsInitialization'),
                    2 => $this->assertEquals($param, 'chartsData123456'),
                    default => true,
                };
            });

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
                    ['getLabelList' => $labels, 'getDatasetList' => $datasets]
                )
            )
        );
    }

    /**
     * @return array<mixed>
     */
    public static function spreadsheetMethodProvider(): array
    {
        return [
            'empty labels' => [
                'getLabelList' => [],
                'getDatasetList' => [
                    ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                ],
            ],
            'empty dataset' => [
                'getLabelList' => ['Label 1', 'Label 2', 'Label 3'],
                'getDatasetList' => [],
            ],
        ];
    }

    public function testDisableUseOfAssets(): void
    {
        $extConf = $this->createMock(ExtensionConfiguration::class);
        $extConf->method('get')->with('charts', 'chart_js_assets')->willReturn('false');

        $library = $this->getAccessibleMock(
            ChartJs::class,
            ['getStylesheetAssetsToLoad', 'getJavascriptAssetsToLoad'],
            ['extensionConfiguration' => $extConf]
        );
        $library->expects(self::never())->method('getStylesheetAssetsToLoad');
        $library->expects(self::never())->method('getJavascriptAssetsToLoad');

        self::assertEmpty($library->getStylesheetAssets('chart-type'));
        self::assertEmpty($library->getJavascriptAssets('chart-type'));
    }

    /**
     * @dataProvider renderingDataProvider
     * 
     * @param ExtensionConfiguration&MockObject $extConf
     * @param PageRenderer&MockObject $pageRenderer
     */
    public function testAssetRendering(MockObject $extConf, ?MockObject $pageRenderer): void
    {
        $library = $this->getAccessibleMock(
            ChartJs::class,
            ['getStylesheetAssetsToLoad', 'getJavascriptAssetsToLoad'],
            ['extensionConfiguration' => $extConf]
        );
        $library->expects(self::once())->method('getStylesheetAssetsToLoad')->willReturn(
            ['folder/chart.css' => ['compress' => true]]
        );
        self::assertSame(['folder/chart.css'], $library->getStylesheetAssets('chart-type', $pageRenderer));

        $library->expects(self::once())->method('getJavascriptAssetsToLoad')->willReturn(
            ['folder/chart.js' => ['compress' => true]]
        );
        self::assertSame(['folder/chart.js'], $library->getJavascriptAssets('chart-type', $pageRenderer));
    }

    /**
     * @return Traversable<mixed>
     */
    public static function renderingDataProvider(): Traversable
    {
        $extConf = self::createMockInProvider(ExtensionConfiguration::class);
        $extConf->method('get')->willThrowException(new ExtensionConfigurationPathDoesNotExistException());

        yield 'exception is caught and assets are returned' => [
            'extConf' => $extConf,
            'pageRenderer' => null,
        ];

        $extConf = self::createMockInProvider(ExtensionConfiguration::class);
        $extConf->method('get')->with('charts', 'chart_js_assets')->willReturn('true');
        $pageRenderer = self::createMockInProvider(PageRenderer::class);
        $pageRenderer->expects(self::once())->method('addCssLibrary');
        $pageRenderer->expects(self::once())->method('addJsFooterLibrary');

        yield 'page renderer is called and assets are returned' => [
            'extConf' => $extConf,
            'pageRenderer' => $pageRenderer,
        ];
    }
}
