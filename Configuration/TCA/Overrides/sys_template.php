<?php
defined('TYPO3_MODE') or die();

(function ($extKey) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/',
        'Charts'
    );
})('charts');
