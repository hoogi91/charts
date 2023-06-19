<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartDataPlain;
use Hoogi91\Charts\Tests\Unit\CacheTrait;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartDataPlainTest extends UnitTestCase
{
    use CacheTrait;

    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCaches();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->resetPackageManager();
    }

    public function testTitleMethods(): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setTitle('Lorem Ipsum');
        $this->assertEquals('Lorem Ipsum', $chartData->getTitle());
    }

    public function testTypeMethods(): void
    {
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('isPackageActive')->with('spreadsheets')->willReturn(false);
        ExtensionManagementUtility::setPackageManager($packageManager);

        $chartData = new ChartDataPlain();
        $chartData->setType(ChartDataPlain::TYPE_PLAIN);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $chartData->getType());

        $chartData->setType(ChartDataPlain::TYPE_SPREADSHEET);
        $this->assertEquals(ChartDataPlain::TYPE_PLAIN, $chartData->getType());
    }

    /**
     * @dataProvider labelProvider
     *
     * @param array<mixed> $expected
     */
    public function testLabelMethods(string $content, array $expected): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setLabels(trim($content));
        $labels = $chartData->getLabelList();
        $this->assertIsArray($labels);
        $this->assertSame($expected, $labels);
    }

    /**
     * @dataProvider datasetProvider
     */
    public function testDatasetMethods(string $content): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setDatasets(trim($content));
        $datasets = $chartData->getDatasetList();
        $this->assertIsArray($datasets);
        $this->assertCount(2, $datasets);
        $this->assertIsFloat($datasets[0][0]);
        $this->assertEquals(29.8, $datasets[0][3]);
    }

    /**
     * @dataProvider labelProvider
     */
    public function testDatasetLabelMethods(string $content): void
    {
        $chartData = new ChartDataPlain();
        $chartData->setDatasetsLabels(trim($content));
        $labels = $chartData->getDatasetsLabelList();
        $this->assertSame(['Germany', 'Europe'], $labels);
    }

    /**
     * @return array<mixed>
     */
    public static function labelProvider(): array
    {
        return [
            'as xml on a single row' => [
                'content' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                    <T3TableWizard>
                        <numIndex index="2" type="array">
                            <numIndex index="2">Germany</numIndex>
                            <numIndex index="4">Europe</numIndex>
                        </numIndex>
                    </T3TableWizard>',
                'expected' => ['Germany', 'Europe'],
            ],
            'as xml in columns' => [
                'content' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                    <T3TableWizard>
                        <numIndex index="2" type="array">
                            <numIndex index="2">Germany</numIndex>
                        </numIndex>
                        <numIndex index="4" type="array">
                            <numIndex index="2">Europe</numIndex>
                        </numIndex>
                    </T3TableWizard>',
                'expected' => ['Germany'],
            ],
            'as typo3 format in a single row' => [
                'content' => '|Germany|Europe|',
                'expected' => ['Germany', 'Europe'],
            ],
            'as typo3 format in columns' => [
                'content' => "|Germany|\n|Europe|",
                'expected' => ['Germany'],
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function datasetProvider(): array
    {
        return [
            'as xml' => [
                'content' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
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
                    </T3TableWizard>',
            ],
            'as typo3 format' => [
                'content' => "|16.7|15|31.2|29.8|7.3|\n|27.5|14.5|27.9|23.1|6.9|",
            ],
        ];
    }
}
