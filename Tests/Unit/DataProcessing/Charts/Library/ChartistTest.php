<?php

namespace Hoogi91\Charts\Tests\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Class ChartistTest
 * @package Hoogi91\Charts\Tests\DataProcessing\Charts\Library
 */
class ChartistTest extends UnitTestCase
{
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

        // create xlsx reader and load default fixture spreadsheet
        $reader = new Xlsx();
        $spreadsheet = $reader->load(dirname(__DIR__, 4) . '/Fixtures/01_fixture.xlsx');
        $extractorService = new ExtractorService($spreadsheet);

        $this->chartDataSpreadsheetModel = $this->getMockBuilder(ChartDataSpreadsheet::class)
            ->setMethods(['getCellDataFromDatabaseString'])
            ->getMock();

        $this->chartDataSpreadsheetModel->method('getCellDataFromDatabaseString')->willReturnCallback(
            function () use ($extractorService) {
                return $extractorService->rangeToCellArray('A1:E1', false, true, false);
            }
        );
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
