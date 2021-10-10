<?php

/**
 * This file is defined as bootstrap configuration in UnitTests.xml and called by PHPUnit
 * before instantiating the test suites. It must also be called on CLI
 * with PHPUnit parameter --bootstrap if executing single test case classes.
 *
 * Example: call whole unit test suite
 * - cd /var/www/t3master/foo  # Document root of TYPO3 CMS sources (location of index.php)
 * - vendor/bin/phpunit -c vendor/nimut/testing-framework/res/Configuration/UnitTests.xml \
 *     typo3conf/ext/example_extension/Tests/Unit
 */

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') {
    die('This script supports command line usage only. Please check your command.');
}

// In case PHPUnit is invoked from a global composer installation or from a phar file, we need to include
// the autoloader to make the classes available
if (!class_exists('Nimut\\TestingFramework\\Bootstrap\\BootstrapFactory')) {
    require __DIR__ . '/../../../../autoload.php';
}

call_user_func(
    static function () {
        $GLOBALS['PHPUNIT_TESTING'] = true;

        $bootstrap = \Nimut\TestingFramework\Bootstrap\BootstrapFactory::createBootstrapInstance();
        $bootstrap->bootstrapUnitTestSystem();

        // if TYPO3 v10 is installed register typo3 version constants (legacy)
        $coreCache = 'cache_core';
        $runtimeCache = 'cache_runtime';
        if (class_exists(\TYPO3\CMS\Core\Information\Typo3Version::class)) {
            $version = new \TYPO3\CMS\Core\Information\Typo3Version();
            if ($version->getMajorVersion() >= 10) {
                $coreCache = 'core';
                $runtimeCache = 'runtime';
            }
        }

        // disable extbase object caching to let object manager work in unit tests
        /** @var CacheManager $cacheManager */
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
    }
);
