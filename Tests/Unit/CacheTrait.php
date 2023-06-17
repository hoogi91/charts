<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit;

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait CacheTrait
{
    private function setUpCaches(): CacheManager
    {
        // if TYPO3 v10 is installed register typo3 version constants (legacy)
        $coreCache = 'cache_core';
        $runtimeCache = 'cache_runtime';
        if (class_exists(Typo3Version::class) && (new Typo3Version())->getMajorVersion() >= 10) {
            $coreCache = 'core';
            $runtimeCache = 'runtime';
        }

        // disable extbase object caching to let object manager work in unit tests
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->setCacheConfigurations(
            [
                $coreCache => [
                    'backend' => NullBackend::class,
                    'frontend' => VariableFrontend::class,
                ],
                $runtimeCache => [
                    'backend' => NullBackend::class,
                    'frontend' => VariableFrontend::class,
                ],
                'extbase_object' => [
                    'backend' => NullBackend::class,
                    'frontend' => VariableFrontend::class,
                ],
            ]
        );

        return $cacheManager;
    }

    private function resetPackageManager(): void
    {
        $cache = new PhpFrontend('core', new NullBackend('production', []));
        $packageManager = Bootstrap::createPackageManager(
            PackageManager::class,
            Bootstrap::createPackageCache($cache)
        );
        ExtensionManagementUtility::setPackageManager($packageManager);
    }
}
