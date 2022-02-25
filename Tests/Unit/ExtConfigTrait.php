<?php

namespace Hoogi91\Charts\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

trait ExtConfigTrait
{

    /**
     * @return ExtensionConfiguration|MockObject
     */
    private function getExtensionConfig(string $type, bool $enabled = true): MockObject
    {
        $mock = $this->createMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturnMap(
            [
                ['charts', $type . '_assets', $enabled],
                ['charts', $type . '_javascript', 'https://cdn.example.com/' . $type . '.js'],
            ]
        );

        return $mock;
    }
}
