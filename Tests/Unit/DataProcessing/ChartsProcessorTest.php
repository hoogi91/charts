<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\DataProcessing;

use Hoogi91\Charts\DataProcessing\Charts\Library\ApexCharts;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\DataProcessing\ChartsProcessor;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use Hoogi91\Charts\Tests\Unit\ExtConfigTrait;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartsProcessorTest extends UnitTestCase
{
    use ExtConfigTrait;

    /**
     * @dataProvider chartDataProvider
     *
     * @param array<mixed> $expected
     * @param array<mixed> $processorConfig
     * @param array<array<string>> $processedData
     */
    public function testProcess(
        array $expected,
        array $processorConfig = [],
        array $processedData = [],
        ?MockObject $chartEntity = null,
        ?LibraryInterface $library = null
    ): void {
        $unit = new ChartsProcessor(
            $this->createConfiguredMock(PageRenderer::class, []),
            $this->createConfiguredMock(ChartDataRepository::class, ['findByUid' => $chartEntity]),
            $this->createConfiguredMock(ExtensionConfiguration::class, ['get' => 'chartjs']),
            $this->createConfiguredMock(
                LibraryRegistry::class,
                ['getLibrary' => $library ?? new ChartJs($this->getExtensionConfig('chart_js'))]
            )
        );
        $result = $unit->process(new ContentObjectRenderer(), [], $processorConfig, $processedData);

        $targetVariable = $processorConfig['as'] ?? 'chart';
        unset($result[$targetVariable]['identifier']); // remove unique generated identifier

        self::assertEquals($expected, $result);
    }

    /**
     * @return array<mixed>
     */
    public static function chartDataProvider(): array
    {
        $chartEntity = self::createMockInProvider(ChartData::class);
        $expectedResult = static fn (
            object $entity = null,
            string $type = '',
            string $library = ChartJs::TECHNICAL_NAME
        ) => ['type' => $type, 'library' => $library, 'entity' => $entity];
        $expectedCss = static fn (array $libs = [], string ...$entities) => [
            'assets' => ['css' => ['libs' => $libs, 'entity' => $entities]],
        ];
        $expectedJs = static fn (array $libs = [], string ...$entities) => [
            'assets' => ['js' => ['libs' => $libs, 'entity' => $entities]],
        ];

        return [
            'empty data' => [
                'expected' => ['chart' => $expectedResult()],
            ],
            'empty data with target variable' => [
                'expected' => ['custom-variable' => $expectedResult()],
                'processorConfig' => [
                    'as' => 'custom-variable',
                ],
            ],
            'with chart data' => [
                'expected' => [
                    'data' => ['CType' => 'chart_pie'],
                    'chart' => $expectedResult($chartEntity, 'chart_pie'),
                ],
                'processorConfig' => [
                    'data' => 123,
                ],
                'processedData' => [
                    'data' => ['CType' => 'chart_pie'],
                ],
                'chartEntity' => $chartEntity,
            ],
            'with chart data and target variable' => [
                'expected' => [
                    'data' => ['CType' => 'chart_line'],
                    'custom-variable' => $expectedResult($chartEntity, 'chart_line'),
                ],
                'processorConfig' => [
                    'data' => 123,
                    'as' => 'custom-variable',
                ],
                'processedData' => [
                    'data' => ['CType' => 'chart_line'],
                ],
                'chartEntity' => $chartEntity,
            ],
            'with chart data and excluded assets' => [
                'expected' => [
                    'data' => ['CType' => 'chart_bar'],
                    'chart' => array_merge_recursive(
                        $expectedResult($chartEntity, 'chart_bar', ApexCharts::TECHNICAL_NAME),
                        $expectedCss([]),
                        $expectedJs(
                            [
                                'https://cdn.example.com/apexcharts_js.js',
                                'EXT:charts/Resources/Public/JavaScript/apexcharts.js',
                            ]
                        )
                    ),
                ],
                'processorConfig' => [
                    'data' => 123,
                    'assets' => 0,
                ],
                'processedData' => [
                    'data' => ['CType' => 'chart_bar'],
                ],
                'chartEntity' => $chartEntity,
                'chartLibrary' => new ApexCharts(self::getExtensionConfig('apexcharts_js')),
            ],
        ];
    }
}
