<?php

namespace Hoogi91\Charts\Tests\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Charts\Tests\Unit\LegacyTrait;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ChartistTest
 * @package Hoogi91\Charts\Tests\DataProcessing\Charts\Library
 */
class ChartistTest extends UnitTestCase
{
    use LegacyTrait;

    /**
     * @var Chartist
     */
    protected $library;

    /**
     * @var ChartDataSpreadsheet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chartDataSpreadsheetModel;

    protected function setUp()
    {
        parent::setUp();
        $this->library = new Chartist();
        $this->library->setPageRenderer($this->getMockBuilder(PageRenderer::class)->getMock());

        $this->chartDataSpreadsheetModel = $this->getMockBuilder(ChartDataSpreadsheet::class)
            ->setMethods(['getCellDataFromDatabaseString'])
            ->getMock();
        $this->chartDataSpreadsheetModel->setDatasets('file:10|0!A1:E1');

        $this->chartDataSpreadsheetModel->method('getCellDataFromDatabaseString')->willReturnCallback(
            $this->getDataCallbackForFixture('01_fixture.xlsx', 'A1:E1')
        );

        $fileRepositoryMock = $this->createMock(FileRepository::class);
        $fileRepositoryMock->method('findFileReferenceByUid')->willReturn(
            $this->createConfiguredMock(
                FileReference::class,
                [
                    'getOriginalFile' => $this->createConfiguredMock(File::class, ['exists' => true]),
                    'getExtension' => 'xlsx',
                    'getForLocalProcessing' => dirname(__DIR__, 4) . '/Fixtures/01_fixture.xlsx'
                ]
            )
        );
        \Closure::bind(
            static function () use ($fileRepositoryMock) {
                self::$singletonInstances[FileRepository::class] = $fileRepositoryMock;
            },
            null,
            GeneralUtility::class
        )();
    }

    /**
     * @test
     */
    public function testProperReturnTypes()
    {
        $this->assertEquals(Chartist::NAME, $this->library->getName());
        $this->assertInternalType('array', $this->library->getDefaultColors());
        $this->assertInternalType('array', $this->library->getDataStructures());
    }

    /**
     * @test
     */
    public function testStylesheetAssetBuilding()
    {
        $stylesheets = $this->library->getStylesheetAssets('bar');
        $this->assertNotEmpty($stylesheets);
        $this->assertInternalType('array', $stylesheets);
    }

    /**
     * @test
     */
    public function testJavascriptAssetBuilding()
    {
        $javascripts = $this->library->getJavascriptAssets('line');
        $this->assertInternalType('array', $javascripts);
        $this->assertCount(2, $javascripts);
    }

    /**
     * @test
     */
    public function testStylesheetEntityBuilding()
    {
        $stylesheet = $this->library->getEntityStylesheet(
            'test-identifier-123',
            'pie',
            $this->chartDataSpreadsheetModel
        );
        $this->assertNotEmpty($stylesheet);
        $this->assertInternalType('string', $stylesheet);
    }

    /**
     * @test
     */
    public function testJavascriptEntityBuilding()
    {
        $javascript = $this->library->getEntityJavascript(
            'test-identifier-123',
            'doughnut',
            $this->chartDataSpreadsheetModel
        );
        $this->assertNotEmpty($javascript);
        $this->assertInternalType('string', $javascript);
    }
}
