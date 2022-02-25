<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing;

use Hoogi91\Charts\DataProcessing\FlexFormProcessor;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FlexFormProcessorTest extends UnitTestCase
{

    /**
     * @dataProvider flexformDataProvider
     */
    public function testProcess(
        array $expected,
        array $parsedData = [],
        array $processorConfig = [],
        array $contentObjectConfig = [],
        array $processedData = []
    ): void {
        $unit = new FlexFormProcessor(
            $this->createConfiguredMock(FlexFormService::class, [
                'convertFlexFormContentToArray' => $parsedData
            ])
        );
        $result = $unit->process(new ContentObjectRenderer(), $contentObjectConfig, $processorConfig, $processedData);
        self::assertSame($expected, $result);
    }

    public function flexformDataProvider(): array
    {
        return [
            'empty data' => [
                'expected' => ['flexform' => []],
            ],
            'parsed data' => [
                'expected' => ['flexform' => ['any-cool-data' => 123]],
                'parsedData' => ['any-cool-data' => 123],
            ],
            'skipped because of condition' => [
                'expected' => ['existing-processed-data' => 'some-value'],
                'parsedData' => ['any-cool-data' => 123],
                'processorConfig' => [
                    'if.' => ['directReturn' => false],
                ],
                'contentObjectConfig' => [],
                'processedData' => ['existing-processed-data' => 'some-value'],
            ],
            'parsed data with custom target variable' => [
                'expected' => ['settings' => ['parsed-data' => 456]],
                'parsedData' => ['parsed-data' => 456],
                'processorConfig' => [
                    'as' => 'settings',
                ],
            ],
            'parsed data with custom target and content object configuration' => [
                'expected' => [
                    'settings' => [
                        'parsed-data' => 789,
                        'existing' => 'world hello',
                        'falseIsReplaced' => 123,
                        'nullIsReplaced' => 456
                    ]
                ],
                'parsedData' => [
                    'parsed-data' => 789,
                    'existing' => 'world hello',
                    'falseIsReplaced' => false,
                    'nullIsReplaced' => null,
                ],
                'processorConfig' => [
                    'as' => 'settings',
                ],
                'contentObjectConfig' => [
                    'settings.' => [
                        'existing' => 'hello world',
                        'falseIsReplaced' => 123,
                        'nullIsReplaced' => 456
                    ]
                ],
            ],
        ];
    }
}
