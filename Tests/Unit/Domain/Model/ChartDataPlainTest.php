<?php

namespace Hoogi91\Charts\Tests\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartDataPlain;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ChartDataPlainTest
 * @package Hoogi91\Charts\Tests\Domain\Model
 */
class ChartDataPlainTest extends UnitTestCase
{
    /**
     * @var ChartDataPlain|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chartDataPlainModel;

    protected function setUp()
    {
        parent::setUp();
        $this->chartDataPlainModel = $this->getMockBuilder(ChartDataPlain::class)
            ->setMethods(['getAllowedTypes'])
            ->getMock();

        // simulate not loaded spreadsheet extension
        $this->chartDataPlainModel->method('getAllowedTypes')->willReturn([
            ChartDataPlain::TYPE_PLAIN,
        ]);
    }

    /**
     * @test
     */
    public function testTitleMethods()
    {
        $this->chartDataPlainModel->setTitle('Lorem Ipsum');
        $this->assertEquals('Lorem Ipsum', $this->chartDataPlainModel->getTitle());
    }

    /**
     * @test
     */
    public function testTypeMethods()
    {
        $this->chartDataPlainModel->setType(ChartDataPlain::TYPE_PLAIN);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $this->chartDataPlainModel->getType());
        $this->chartDataPlainModel->setType(ChartDataPlain::TYPE_SPREADSHEET);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $this->chartDataPlainModel->getType());
    }

    /**
     * @test
     */
    public function testLabelMethods()
    {
        $this->chartDataPlainModel->setLabels(trim('
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <T3TableWizard>
                <numIndex index="2" type="array">
                    <numIndex index="2">Germany</numIndex>
                    <numIndex index="4">Europe</numIndex>
                    <numIndex index="6">America</numIndex>
                    <numIndex index="8">China</numIndex>
                </numIndex>
            </T3TableWizard>
        '));
        $labels = $this->chartDataPlainModel->getLabels();

        $this->assertInternalType('array', $labels);
        $this->assertCount(4, $labels);
        $this->assertEquals('Europe', $labels[1]);
    }

    /**
     * @test
     */
    public function testDatasetMethods()
    {
        $this->chartDataPlainModel->setDatasets(trim('
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <T3TableWizard>
                <numIndex index="2" type="array">
                    <numIndex index="2">16.7</numIndex>
                    <numIndex index="4">15</numIndex>
                    <numIndex index="6">31.2</numIndex>
                    <numIndex index="8">29.8</numIndex>
                    <numIndex index="10">7.3</numIndex>
                </numIndex>
                    <numIndex index="4" type="array">
                    <numIndex index="2">27.5</numIndex>
                    <numIndex index="4">14.5</numIndex>
                    <numIndex index="6">27.9</numIndex>
                    <numIndex index="8">23.1</numIndex>
                    <numIndex index="10">6.9</numIndex>
                </numIndex>
            </T3TableWizard>
        '));
        $datasets = $this->chartDataPlainModel->getDatasets();

        $this->assertInternalType('array', $datasets);
        $this->assertCount(2, $datasets);
        $this->assertInternalType('float', $datasets[0][0]);
        $this->assertEquals(29.8, $datasets[0][3]);
    }

    /**
     * @test
     */
    public function testDatasetLabelMethods()
    {
        $this->chartDataPlainModel->setDatasetsLabels(trim('
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <T3TableWizard>
                <numIndex index="2" type="array">
                    <numIndex index="2">Germany</numIndex>
                    <numIndex index="4">Europe</numIndex>
                    <numIndex index="6">America</numIndex>
                    <numIndex index="8">China</numIndex>
                </numIndex>
            </T3TableWizard>
        '));
        $labels = $this->chartDataPlainModel->getDatasetsLabels();

        $this->assertInternalType('array', $labels);
        $this->assertCount(4, $labels);
        $this->assertEquals('Europe', $labels[1]);
    }
}
