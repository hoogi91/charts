<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\DataProcessing\ChartsProcessor;
use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Repository\ChartDataRepository;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartsProcessorTest extends UnitTestCase
{

    /**
     * @dataProvider chartDataProvider
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
            $this->createConfiguredMock(ExtensionConfiguration::class, []),
            $this->createConfiguredMock(LibraryRegistry::class, ['getLibrary' => $library ?? new ChartJs()]),
        );
        $result = $unit->process(new ContentObjectRenderer(), [], $processorConfig, $processedData);

        $targetVariable = $processorConfig['as'] ?? 'chart';
        unset($result[$targetVariable]['identifier']); // remove unique generated identifier

        self::assertEquals($expected, $result);
    }

    public function chartDataProvider(): array
    {
        $chartEntity = $this->createMock(ChartData::class);
        $expectedResult = static fn(
            object $entity = null,
            string $type = '',
            string $library = ChartJs::NAME
        ) => ['type' => $type, 'library' => $library, 'entity' => $entity];
        $expectedCss = static fn(array $libs = [], string ...$entities) => [
            'assets' => ['css' => ['libs' => $libs, 'entity' => $entities]]
        ];
        $expectedJs = static fn(array $libs = [], string ...$entities) => [
            'assets' => ['js' => ['libs' => $libs, 'entity' => $entities]]
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
                    'custom-variable' => $expectedResult($chartEntity, 'chart_line')
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
                        $expectedResult($chartEntity, 'chart_bar', Chartist::NAME),
                        $expectedJs(
                            [
                                'https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.js',
                                'typo3conf/ext/charts/Resources/Public/JavaScript/chartist.js'
                            ]
                        ),
                        $expectedCss(
                            [
                                'https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.css',
                                'typo3conf/ext/charts/Resources/Public/Css/chartist.css'
                            ]
                        ),
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
                'chartLibrary' => new Chartist()
            ],
        ];
    }
}
