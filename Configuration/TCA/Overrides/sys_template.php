<?php
defined('TYPO3') or die();

(static function (string $extKey) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript/', 'Charts');
})('charts');
