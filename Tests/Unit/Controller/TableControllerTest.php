<?php

namespace Hoogi91\Charts\Tests\Unit\Controller;

use Hoogi91\Charts\Controller\Wizard\TableController;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TableControllerTest
 * @package Hoogi91\Charts\Tests\Unit\Controller
 */
class TableControllerTest extends UnitTestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testConfigurationFix(string $fieldValue, array $expected): void
    {
        $tableController = new StubXmlConfigTableController();
        self::assertEquals(
            $expected,
            $tableController->testProxy4GetConfiguration(
                ['someField' => $fieldValue],
                $this->createMock(ServerRequestInterface::class)
            )
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

class StubXmlConfigTableController extends TableController
{
    protected $xmlStorage = 1;
    protected $P = ['field' => 'someField'];

    /**
     * @return array|ResponseInterface
     */
    public function testProxy4GetConfiguration($row, $request)
    {
        return $this->getConfiguration($row, $request);
    }
}
