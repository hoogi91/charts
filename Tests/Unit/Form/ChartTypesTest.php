<?php

namespace Hoogi91\Charts\Tests\Unit\Form;

use Hoogi91\Charts\Form\Types\BarChart;
use Hoogi91\Charts\Form\Types\DoughnutChart;
use Hoogi91\Charts\Form\Types\LineChart;
use Hoogi91\Charts\Form\Types\PieChart;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ChartTypesTest extends UnitTestCase
{
    public function testBarChart(): void
    {
        self::assertStringContainsString('bar', $id = BarChart::getIdentifier());
        self::assertStringContainsString('bar', BarChart::getIconIdentifier());

        // contains pi_flexform
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $id] = 'xyz';

        // validate tca registration
        BarChart::register(['bar-override' => true]);
        // CType has been added?
        self::assertContains(
            $id,
            array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 1)
        );
        // CType has been configured
        self::assertIsString($GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertStringContainsString('pi_flexform', $GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertEquals(['bar-override' => true], $GLOBALS['TCA']['tt_content']['types'][$id]['columnsOverrides']);
    }

    public function testLineChart(): void
    {
        self::assertStringContainsString('line', $id = LineChart::getIdentifier());
        self::assertStringContainsString('line', LineChart::getIconIdentifier());

        // contains pi_flexform
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $id] = 'xyz';

        // validate tca registration
        LineChart::register(['line-override' => true]);
        // CType has been added?
        self::assertContains(
            $id,
            array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 1)
        );
        // CType has been configured
        self::assertIsString($GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertStringContainsString('pi_flexform', $GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertEquals(['line-override' => true], $GLOBALS['TCA']['tt_content']['types'][$id]['columnsOverrides']);
    }

    public function testPieChart(): void
    {
        self::assertStringContainsString('pie', $id = PieChart::getIdentifier());
        self::assertStringContainsString('pie', PieChart::getIconIdentifier());

        // validate tca registration
        PieChart::register(['pie-override' => true]);
        // CType has been added?
        self::assertContains(
            $id,
            array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 1)
        );
        // CType has been configured
        self::assertIsString($GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertStringNotContainsString('pi_flexform', $GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertEquals(['pie-override' => true], $GLOBALS['TCA']['tt_content']['types'][$id]['columnsOverrides']);
    }

    public function testDoughnutChart(): void
    {
        self::assertStringContainsString('doughnut', $id = DoughnutChart::getIdentifier());
        self::assertStringContainsString('doughnut', DoughnutChart::getIconIdentifier());

        // validate tca registration
        DoughnutChart::register(['doughnut-override' => true]);
        // CType has been added?
        self::assertContains(
            $id,
            array_column($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'], 1)
        );
        // CType has been configured
        self::assertIsString($GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertStringNotContainsString('pi_flexform', $GLOBALS['TCA']['tt_content']['types'][$id]['showitem']);
        self::assertEquals(
            ['doughnut-override' => true],
            $GLOBALS['TCA']['tt_content']['types'][$id]['columnsOverrides']
        );
    }
}
