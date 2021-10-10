<?php

namespace Hoogi91\Charts\Tests\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Spreadsheets\Domain\ValueObject\ExtractionValueObject;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ChartDataSpreadsheetTest
 * @package Hoogi91\Charts\Tests\Domain\Model
 */
class ChartDataSpreadsheetTest extends UnitTestCase
{

    public const LABEL_DSN = 'file:123|0!A1:E1';
    public const DATASET_DSN = 'file:456|0!A2:E7';
    public const DATASET_LABEL_DSN = 'file:789|0!A7:C7';

    /**
     * @var ChartDataSpreadsheet
     */
    private $chartData;

    protected function setUp(): void
    {
        parent::setUp();

        // mock file repository
        $filRepositoryMock = $this->getMockBuilder(FileRepository::class)->disableOriginalConstructor()->getMock();
        $filRepositoryMock->method('findFileReferenceByUid')->willReturnCallback(
            function (int $fileUid) {
                $mock = $this->getMockBuilder(FileReference::class)->disableOriginalConstructor()->getMock();
                $mock->method('getUid')->willReturn($fileUid);
                return $mock;
            }
        );
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn($filRepositoryMock);
        GeneralUtility::setContainer($container);

        // TODO: update spreadsheet extension first to v11 and tag new version which is min version for new version of charts
        $extractorService = $this->createMock(ExtractorService::class);
        $extractorService->method('getDataByDsnValueObject')->willReturn(
            ExtractionValueObject::create(
                $this->createMock(Spreadsheet::class),
                [
                    ['body-1-1', 'body-1-2', 'body-1-3'],
                    ['body-2-1', 'body-2-2', 'body-2-3'],
                    ['body-3-1', 'body-3-2', 'body-3-3'],
                ]
            )
        );

        $this->chartData = new ChartDataSpreadsheet();
        $this->chartData->injectExtractorService($extractorService);
        $this->chartData->initializeObject();
    }

    public function testTitleMethods(): void
    {
        $this->chartData->setTitle('Lorem Ipsum');
        $this->assertEquals('Lorem Ipsum', $this->chartData->getTitle());
    }

    public function testTypeMethods(): void
    {
        $this->chartData->setType(ChartData::TYPE_PLAIN);
        $this->assertEquals(ChartData::TYPE_PLAIN, $this->chartData->getType());

        $this->chartData->setType(ChartData::TYPE_SPREADSHEET);
        $this->assertEquals(ChartData::TYPE_SPREADSHEET, $this->chartData->getType());
    }

    public function testLabelMethods(): void
    {
        $this->chartData->setLabels(static::LABEL_DSN);
        $labels = $this->chartData->getLabels();
        $this->assertIsArray($labels);
        $this->assertCount(3, $labels);
        $this->assertEquals('body-1-2', $labels[1]);
    }

    public function testDatasetMethods(): void
    {
        $this->chartData->setDatasets(static::DATASET_DSN);
        $datasets = $this->chartData->getDatasets();
        $this->assertIsArray($datasets);
        $this->assertCount(3, $datasets);
        $this->assertEquals(['body-1-1', 'body-1-2', 'body-1-3'], $datasets[0]);
        $this->assertEquals(['body-2-1', 'body-2-2', 'body-2-3'], $datasets[1]);
        $this->assertEquals(['body-3-1', 'body-3-2', 'body-3-3'], $datasets[2]);
    }

    public function testDatasetLabelMethods(): void
    {
        $this->chartData->setDatasetsLabels(static::DATASET_LABEL_DSN);
        $labels = $this->chartData->getDatasetsLabels();
        $this->assertIsArray($labels);
        $this->assertCount(3, $labels);
        $this->assertEquals('body-1-2', $labels[1]);
    }
}
