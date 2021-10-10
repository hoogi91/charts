<?php

namespace Hoogi91\Charts\Tests\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartDataPlain;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ChartDataPlainTest
 * @package Hoogi91\Charts\Tests\Domain\Model
 */
class ChartDataPlainTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('isPackageActive')->with('spreadsheets')->willReturn(false);
        ExtensionManagementUtility::setPackageManager($packageManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        ExtensionManagementUtility::setPackageManager(GeneralUtility::makeInstance(PackageManager::class));
    }

    public function testTitleMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setTitle('Lorem Ipsum');
        $this->assertEquals('Lorem Ipsum', $chartData->getTitle());
    }

    public function testTypeMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setType(ChartDataPlain::TYPE_PLAIN);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $chartData->getType());

        $chartData->setType(ChartDataPlain::TYPE_SPREADSHEET);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $chartData->getType());
    }


    public function testLabelMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setLabels(
            trim(
                '
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <T3TableWizard>
                <numIndex index="2" type="array">
                    <numIndex index="2">Germany</numIndex>
                    <numIndex index="4">Europe</numIndex>
                    <numIndex index="6">America</numIndex>
                    <numIndex index="8">China</numIndex>
                </numIndex>
            </T3TableWizard>
        '
            )
        );
        $labels = $chartData->getLabels();
        $this->assertIsArray($labels);
        $this->assertCount(4, $labels);
        $this->assertEquals('Europe', $labels[1]);
    }

    public function testDatasetMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setDatasets(
            trim(
                '
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
        '
            )
        );
        $datasets = $chartData->getDatasets();
        $this->assertIsArray($datasets);
        $this->assertCount(2, $datasets);
        $this->assertIsFloat($datasets[0][0]);
        $this->assertEquals(29.8, $datasets[0][3]);
    }

    public function testDatasetLabelMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setDatasetsLabels(
            trim(
                '
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <T3TableWizard>
                <numIndex index="2" type="array">
                    <numIndex index="2">Germany</numIndex>
                    <numIndex index="4">Europe</numIndex>
                    <numIndex index="6">America</numIndex>
                    <numIndex index="8">China</numIndex>
                </numIndex>
            </T3TableWizard>
        '
            )
        );
        $labels = $chartData->getDatasetsLabels();
        $this->assertIsArray($labels);
        $this->assertCount(4, $labels);
        $this->assertEquals('Europe', $labels[1]);
    }
}
