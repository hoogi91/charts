<?php

namespace Hoogi91\Charts\Tests\Unit\Controller;

use Hoogi91\Charts\Controller\Wizard\TableController;
use Hoogi91\Charts\Tests\Unit\CacheTrait;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TableControllerTest extends UnitTestCase
{

    use CacheTrait;

    protected $resetSingletonInstances = true;

    protected function setUp(): void
    {
        if (class_exists(Typo3Version::class) === true
            && version_compare((new Typo3Version())->getVersion(), '11.4', '>=') === true) {
            $this->markTestSkipped('TableController does not exists anymore in TYPO3 version >= 11.4');
        }
        parent::setUp();
        $this->setUpCaches();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testConfigurationFix(string $fieldValue, array $expected): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        self::assertEquals(
            $expected,
            \Closure::fromCallable(
                function () use ($fieldValue, $request) {
                    $this->xmlStorage = 1;
                    $this->P = ['field' => 'someField'];
                    return $this->getConfiguration(
                        ['someField' => $fieldValue, 'table_enclosure' => null, 'table_delimiter' => null],
                        $request
                    );
                }
            )->call(new TableController())
        );
    }

    public function dataProvider(): array
    {
        return [
            'empty xml creates default array' => [
                'fieldValue' => '',
                'expected' => [['', '', '', '']]
            ],
            'equal row length XML' => [
                'fieldValue' => file_get_contents(__DIR__ . '/../../Fixtures/two-column.xml'),
                'expected' => [
                    2 => [
                        0 => 'Field1',
                        1 => 'Field2',
                    ],
                ],
            ],
            'different row length XML' => [
                'fieldValue' => file_get_contents(__DIR__ . '/../../Fixtures/different-row-length.xml'),
                'expected' => [
                    2 => [
                        0 => 'Field1',
                        1 => 'Field2',
                        2 => '',
                        3 => '',
                    ],
                    4 => [
                        0 => 'Field1',
                        1 => 'Field2',
                        2 => 'Field3',
                        3 => 'Field4',
                    ],
                    6 => [
                        0 => 'Field1',
                        1 => 'Field2',
                        2 => 'Field3',
                        3 => '',
                    ],
                ],
            ],
        ];
    }
}
