<?php

namespace Hoogi91\Charts\Tests\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Charts\Tests\Unit\LegacyTrait;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ChartDataSpreadsheetTest
 * @package Hoogi91\Charts\Tests\Domain\Model
 */
class ChartDataSpreadsheetTest extends UnitTestCase
{
    use LegacyTrait;

    const LABEL_POSITION = 'file:label|0!A1:E1';
    const DATASET_POSITION = 'file:dataset|0!A2:E7';
    const DATASETLABEL_POSITION = 'file:datasetLabels|0!A7:C7';

    /**
     * @var ChartDataSpreadsheet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chartDataSpreadsheetModel;

    protected function setUp()
    {
        parent::setUp();

        $this->chartDataSpreadsheetModel = $this->getMockBuilder(ChartDataSpreadsheet::class)
            ->setMethods(['getCellDataFromDatabaseString'])
            ->getMock();

        $this->chartDataSpreadsheetModel->method('getCellDataFromDatabaseString')->willReturnCallback(
            $this->getDataCallbackForFixture(
                '01_fixture.xlsx',
                static function ($data) {
                    return substr($data, strpos($data, '!') + 1);
                }
            )
        );
    }

    /**
     * @test
     */
    public function testTitleMethods()
    {
        $this->chartDataSpreadsheetModel->setTitle('Lorem Ipsum');
        $this->assertEquals('Lorem Ipsum', $this->chartDataSpreadsheetModel->getTitle());
    }

    /**
     * @test
     */
    public function testTypeMethods()
    {
        $this->chartDataSpreadsheetModel->setType(ChartDataSpreadsheet::TYPE_PLAIN);
        $this->assertEquals(ChartDataSpreadsheet::TYPE_PLAIN, $this->chartDataSpreadsheetModel->getType());

        $this->chartDataSpreadsheetModel->setType(ChartDataSpreadsheet::TYPE_SPREADSHEET);
        $this->assertEquals(ChartDataSpreadsheet::TYPE_SPREADSHEET, $this->chartDataSpreadsheetModel->getType());
    }

    /**
     * @test
     */
    public function testLabelMethods()
    {
        $this->chartDataSpreadsheetModel->setLabels(static::LABEL_POSITION);
        $labels = $this->chartDataSpreadsheetModel->getLabels();

        $this->assertInternalType('array', $labels);
        $this->assertCount(5, $labels); // first row A-E
        $this->assertEquals('2015', $labels[1]);
    }

    /**
     * @test
     */
    public function testDatasetMethods()
    {
        $this->chartDataSpreadsheetModel->setDatasets(static::DATASET_POSITION);
        $datasets = $this->chartDataSpreadsheetModel->getDatasets();

        $this->assertInternalType('array', $datasets);
        $this->assertCount(6, $datasets); // rows 2-7
        $this->assertCount(4, $datasets[0]);  // row 2 is the first and has one colspan column
        $this->assertCount(5, $datasets[4]); // row 6 has 5 columns

        // check if textual cells are floated (original value should be "Test123")
        $this->assertInternalType('float', $datasets[2][2]);
        $this->assertEquals(0.0, $datasets[2][2]);

        $this->assertInternalType('float', $datasets[0][2]);
        $this->assertEquals(70.0, $datasets[0][2]);
    }

    /**
     * @test
     */
    public function testDatasetLabelMethods()
    {
        $this->chartDataSpreadsheetModel->setDatasetsLabels(static::DATASETLABEL_POSITION);
        $labels = $this->chartDataSpreadsheetModel->getDatasetsLabels();

        $this->assertInternalType('array', $labels);
        $this->assertCount(2, $labels); // last row 7 is selected from A-C (column B is a rowspan and not counted)
        $this->assertEquals(70.0, $labels[1]);
    }
}
