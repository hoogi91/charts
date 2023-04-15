<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts;

use Hoogi91\Charts\DataProcessing\Charts\Library\ApexCharts;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LibraryRegistryTest extends UnitTestCase
{
    private LibraryRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $extConf = $this->createMock(ExtensionConfiguration::class);
        $this->registry = new LibraryRegistry(
            new ServiceLocator(
                [
                    ChartJs::getServiceIndex() => static fn (): ChartJs => new ChartJs($extConf),
                    ApexCharts::getServiceIndex() => static fn (): ApexCharts => new ApexCharts($extConf),
                ]
            ),
            new ChartJs($extConf)
        );
    }

    public function testLibraryRegistration(): void
    {
        $this->assertInstanceOf(ChartJs::class, $this->registry->getLibrary('chart.js'));
        $this->assertInstanceOf(ApexCharts::class, $this->registry->getLibrary('apexcharts.js'));
        $this->assertNull($this->registry->getLibrary('unknown-identifier'));
        $this->assertInstanceOf(ChartJs::class, $this->registry->getDefaultLibrary());
    }

    public function testLibrarySelectGenerator(): void
    {
        $select = $this->registry->getLibrarySelect(
            [
                'fieldName' => 'html-fieldname',
                'fieldValue' => ChartJs::getServiceIndex(),
            ]
        );

        $this->assertNotEmpty($select);
        $this->assertIsString($select);
        $this->assertStringContainsString(
            '<input type="hidden" name="html-fieldname" value="chart.js"/>',
            $select
        );
        $this->assertStringContainsString(
            '<option value="chart.js" selected="selected">chart.js (' . ChartJs::class . ')</option>',
            $select
        );
        $this->assertStringContainsString(
            '<option value="apexcharts.js">apexcharts.js (' . ApexCharts::class . ')</option>',
            $select
        );
    }
}
