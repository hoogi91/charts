<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\Domain\Model;

use Composer\InstalledVersions;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use Hoogi91\Spreadsheets\Domain\ValueObject\CellDataValueObject;
use Hoogi91\Spreadsheets\Domain\ValueObject\ExtractionValueObject;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartDataSpreadsheetColorTest extends UnitTestCase
{
    private const DATASET_DSN = 'file:456|0!A2:E7';

    protected bool $resetSingletonInstances = true;

    /**
     * @dataProvider backgroundDataProvider
     *
     * @param array<array<string>> $consecutiveBackground
     * @param array<mixed> $expectedColors
     */
    public function testBackgroundColorMethods(
        array $consecutiveBackground,
        int $expectedCount,
        array $expectedColors
    ): void {
        if (
            empty($consecutiveBackground)
            && version_compare((string)InstalledVersions::getVersion('phpoffice/phpspreadsheet'), '2.0.0') !== -1
        ) {
            return;
        }

        $styleMocks = [];
        foreach ($consecutiveBackground as $background) {
            $configuration = ['getFillType' => Style\Fill::FILL_NONE];
            if (empty($background) === false) {
                $fillType = array_key_first($background);
                $configuration = [
                    'getFillType' => $fillType,
                    'getStartColor' => new Style\Color($background[$fillType] ?? Style\Color::COLOR_BLACK),
                ];
                if ($fillType === Style\Fill::FILL_GRADIENT_LINEAR) {
                    $configuration['getEndColor'] = new Style\Color('FFE1580C');
                }
            }

            $styleMocks[] = $this->createConfiguredMock(Style\Style::class, [
                'getFill' => $this->createConfiguredMock(Style\Fill::class, $configuration),
            ]);
        }

        $spreadsheetMock = $this->createMock(Spreadsheet::class);
        $spreadsheetMock->method('getCellXfByIndex')->willReturnOnConsecutiveCalls(...$styleMocks ?: [null]);

        $colors = $this->getChartData($spreadsheetMock)->getBackgroundColors(1);
        $this->assertCount($expectedCount, $colors);
        $this->assertSame($expectedColors, $colors);
    }

    /**
     * @return array<mixed>
     */
    public static function backgroundDataProvider(): array
    {
        return [
            'no background style' => [
                'consecutiveBackground' => [],
                'expectedCount' => 0,
                'expectedColors' => [],
            ],
            'no background color' => [
                'consecutiveBackground' => [[], [], []],
                'expectedCount' => 0,
                'expectedColors' => [],
            ],
            'with background color' => [
                'consecutiveBackground' => [
                    [Style\Fill::FILL_SOLID => Style\Color::COLOR_BLUE],
                    [Style\Fill::FILL_NONE => null], // will be filled with default color
                    [Style\Fill::FILL_PATTERN_LIGHTDOWN => Style\Color::COLOR_DARKGREEN],
                ],
                'expectedCount' => 2,
                'expectedColors' => ['rgb(0, 0, 255)', 'rgb(0, 128, 0)'],
            ],
            'with background gradient' => [
                'consecutiveBackground' => [
                    [Style\Fill::FILL_GRADIENT_LINEAR => Style\Color::COLOR_BLUE],
                    [Style\Fill::FILL_GRADIENT_LINEAR => Style\Color::COLOR_YELLOW],
                    [Style\Fill::FILL_GRADIENT_LINEAR => Style\Color::COLOR_RED],
                ],
                'expectedCount' => 3,
                'expectedColors' => ['rgb(0, 0, 255)', 'rgb(255, 255, 0)', 'rgb(255, 0, 0)'],
            ],
        ];
    }

    /**
     * @dataProvider borderDataProvider
     *
     * @param array<array<string, array<string>>> $consecutiveBorders
     * @param array<mixed> $expectedColors
     */
    public function testBorderColorMethods(array $consecutiveBorders, int $expectedCount, array $expectedColors): void
    {
        if (
            empty($consecutiveBorders)
            && version_compare((string)InstalledVersions::getVersion('phpoffice/phpspreadsheet'), '2.0.0') !== -1
        ) {
            return;
        }

        // generate border mock objects
        $styleMocks = [];
        foreach ($consecutiveBorders as $borders) {
            $configuration = [];
            foreach (['top', 'bottom', 'left', 'right'] as $position) {
                if (isset($borders[$position])) {
                    $borderStyle = array_key_first($borders[$position]);
                    $configuration['get' . ucfirst($position)] = $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => $borderStyle,
                        'getColor' => new Style\Color($borders[$position][$borderStyle] ?? Style\Color::COLOR_BLACK),
                    ]);
                } else {
                    $configuration['get' . ucfirst($position)] = $this->createConfiguredMock(Style\Border::class, [
                        'getBorderStyle' => Style\Border::BORDER_NONE,
                    ]);
                }
            }

            $styleMocks[] = $this->createConfiguredMock(Style\Style::class, [
                'getBorders' => $this->createConfiguredMock(Style\Borders::class, $configuration),
            ]);
        }

        $spreadsheetMock = $this->createMock(Spreadsheet::class);
        $spreadsheetMock->method('getCellXfByIndex')->willReturnOnConsecutiveCalls(...$styleMocks ?: [null]);

        $colors = $this->getChartData($spreadsheetMock)->getBorderColors(1);
        $this->assertCount($expectedCount, $colors);
        $this->assertSame($expectedColors, $colors);
    }

    /**
     * @return array<mixed>
     */
    public static function borderDataProvider(): array
    {
        return [
            'no border style' => [
                'consecutiveBorders' => [],
                'expectedCount' => 0,
                'expectedColors' => [],
            ],
            'no border color' => [
                'consecutiveBorders' => [[], [], []], // no border colors are set
                'expectedCount' => 0,
                'expectedColors' => [],
            ],
            'same border color' => [
                'consecutiveBorders' => [
                    ['top' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_BLUE]],
                    ['bottom' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_BLUE]],
                    ['right' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_BLUE]],
                ],
                'expectedCount' => 3,
                'expectedColors' => ['rgb(0, 0, 255)', 'rgb(0, 0, 255)', 'rgb(0, 0, 255)'],
            ],
            'multiple border color' => [
                'consecutiveBorders' => [
                    [
                        'top' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_BLUE],
                        'left' => [Style\Border::BORDER_THICK => Style\Color::COLOR_DARKYELLOW],
                        'right' => [Style\Border::BORDER_DASHDOT => Style\Color::COLOR_DARKYELLOW],
                    ],
                    [
                        'top' => [Style\Border::BORDER_NONE => null],
                        'bottom' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_BLUE],
                    ],
                    [
                        'top' => [Style\Border::BORDER_DASHDOT => Style\Color::COLOR_GREEN],
                        'left' => [Style\Border::BORDER_THICK => Style\Color::COLOR_DARKYELLOW],
                        'right' => [Style\Border::BORDER_DASHDOT => Style\Color::COLOR_GREEN],
                        'bottom' => [Style\Border::BORDER_DOTTED => Style\Color::COLOR_DARKYELLOW],
                    ],
                ],
                'expectedCount' => 3,
                'expectedColors' => ['rgb(128, 128, 0)', 'rgb(0, 0, 255)', 'rgb(0, 255, 0)'],
            ],
        ];
    }

    /**
     * @param Spreadsheet&MockObject $spreadsheetMock
     */
    private function getChartData(MockObject $spreadsheetMock): ChartDataSpreadsheet
    {
        $createCellValue = fn (float $value) => CellDataValueObject::create(
            $this->createConfiguredMock(Cell::class, [
                'getCalculatedValue' => $value,
                'getFormattedValue' => '[formatted]' . $value,
                'getDataType' => 's',
                'getXfIndex' => 123,
                'getStyle' => $this->createConfiguredMock(Style\Style::class, [
                    'getFont' => $this->createMock(Style\Font::class),
                ]),
            ]),
            '[rendered]' . $value
        );

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
        GeneralUtility::addInstance(ExtractorService::class, $extractorService);

        $chartData = new ChartDataSpreadsheet();
        $chartData->setDatasets(self::DATASET_DSN);

        return $chartData;
    }
}
