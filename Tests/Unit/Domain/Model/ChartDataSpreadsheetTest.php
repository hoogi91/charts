<?php

namespace Hoogi91\Charts\Tests\Unit\Domain\Model;

use Hoogi91\Charts\Domain\Model\ChartData;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\ExtractionValueObject;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartDataSpreadsheetTest extends UnitTestCase
{

    public const LABEL_DSN = 'file:123|0!A1:E1';
    public const DATASET_DSN = 'file:456|0!A2:E7';
    public const DATASET_LABEL_DSN = 'file:789|0!A7:C7';

    protected $resetSingletonInstances = true;

    private ChartDataSpreadsheet $chartData;

    protected function setUp(): void
    {
        parent::setUp();

        $createCellValue = function (float $value) {
            return CellDataValueObject::create(
                $this->createConfiguredMock(Cell::class, [
                    'getCalculatedValue' => $value,
                    'getFormattedValue' => '[formatted]' . $value,
                    'getDataType' => 's',
                    'getXfIndex' => 123,
                    'getStyle' => $this->createConfiguredMock(Style\Style::class, [
                        'getFont' => $this->createMock(Style\Font::class)
                    ])
                ]),
                '[rendered]' . $value
            );
        };

        $spreadsheetMock = $this->createConfiguredMock(Spreadsheet::class, [
            'getCellXfByIndex' => $this->createConfiguredMock(Style\Style::class, [
                'getBorders' => $this->createConfiguredMock(Style\Borders::class, [
                    'getTop' => $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => Style\Border::BORDER_DOTTED,
                        'getColor' => new Style\Color(Style\Color::COLOR_BLUE),
                    ]),
                    'getBottom' => $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => Style\Border::BORDER_NONE,
                    ]),
                    'getLeft' => $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => Style\Border::BORDER_THICK,
                        'getColor' => new Style\Color(Style\Color::COLOR_DARKYELLOW),
                    ]),
                    'getRight' => $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => Style\Border::BORDER_DASHDOT,
                        'getColor' => new Style\Color(Style\Color::COLOR_DARKYELLOW),
                    ]),
                ]),
                'getFill' => $this->createConfiguredMock(Style\Fill::class, [
                    'getFillType' => Style\Fill::FILL_SOLID,
                    'getStartColor' => new Style\Color(Style\Color::COLOR_RED),
                ])
            ])
        ]);

        $extractorService = $this->createMock(ExtractorService::class);
        $extractorService->method('getDataByDsnValueObject')->willReturn(
            ExtractionValueObject::create(
                $spreadsheetMock,
                [
                    [$createCellValue(1.1), $createCellValue(1.2), $createCellValue(1.3)],
                    [$createCellValue(2.1), $createCellValue(2.2), $createCellValue(2.3)],
                    [$createCellValue(3.1), $createCellValue(3.2), $createCellValue(3.3)],
                ]
            )
        );

        $this->chartData = new ChartDataSpreadsheet();
        $this->chartData->injectExtractorService($extractorService);
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
        $this->chartData->setLabels(self::LABEL_DSN);
        $labels = $this->chartData->getLabels();
        $this->assertIsArray($labels);
        $this->assertCount(3, $labels);
        $this->assertEquals('[rendered]1.2', $labels[1]);
    }

    public function testDatasetMethods(): void
    {
        $this->chartData->setDatasets(self::DATASET_DSN);
        $datasets = $this->chartData->getDatasets();
        $this->assertIsArray($datasets);
        $this->assertCount(3, $datasets);
        $this->assertEquals([1.1, 1.2, 1.3], $datasets[0]);
        $this->assertEquals([2.1, 2.2, 2.3], $datasets[1]);
        $this->assertEquals([3.1, 3.2, 3.3], $datasets[2]);
    }

    public function testDatasetLabelMethods(): void
    {
        $this->chartData->setDatasetsLabels(self::DATASET_LABEL_DSN);
        $labels = $this->chartData->getDatasetsLabels();
        $this->assertIsArray($labels);
        $this->assertCount(3, $labels);
        $this->assertEquals('[rendered]1.2', $labels[1]);
    }

    public function testBackgroundColorMethods(): void
    {
        // TODO: add data provider to check edge cases
        $this->chartData->setDatasets(self::DATASET_DSN);
        $colors = $this->chartData->getBackgroundColors(1);
        $this->assertIsArray($colors);
        $this->assertCount(3, $colors);
        $this->assertSame(['rgb(255, 0, 0)', 'rgb(255, 0, 0)', 'rgb(255, 0, 0)'], $colors);
    }

    public function testBorderColorMethods(): void
    {
        // TODO: add data provider to check edge cases
        $this->chartData->setDatasets(self::DATASET_DSN);
        $colors = $this->chartData->getBorderColors(1);
        $this->assertIsArray($colors);
        $this->assertCount(3, $colors);
        $this->assertSame(['rgb(128, 128, 0)', 'rgb(128, 128, 0)', 'rgb(128, 128, 0)'], $colors);
    }
}
