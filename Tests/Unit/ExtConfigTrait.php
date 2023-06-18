<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit;

use PHPUnit\Framework\MockObject\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

trait ExtConfigTrait
{
    /**
     * @return ExtensionConfiguration&MockObject
     */
    private static function getExtensionConfig(string $type, bool $enabled = true): MockObject
    {
        $mock = (new Generator())->getMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturnMap(
            [
                ['charts', $type . '_assets', $enabled],
                ['charts', $type . '_javascript', 'https://cdn.example.com/' . $type . '.js'],
            ]
        );

        return $mock;
    }

    /**
     * Creates (and configures) a mock object for the specified interface or class.
     *
     * @psalm-template RealInstanceType of object
     *
     * @psalm-param class-string<RealInstanceType> $originalClassName
     *
     * @psalm-return MockObject&RealInstanceType
     *
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private static function createMockInProvider(string $originalClassName, array $configuration = []): MockObject
    {
        $mock = (new Generator())->getMock(
            $originalClassName,
            callOriginalConstructor: false,
            callOriginalClone: false,
            cloneArguments: false,
            allowMockingUnknownTypes: false,
        );
        foreach ($configuration as $method => $return) {
            $mock->method($method)->willReturn($return);
        }

        return $mock;
    }
}
