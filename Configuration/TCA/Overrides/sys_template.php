<?php

defined('TYPO3_MODE') or die();

(static function ($extKey = 'charts') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/',
        'Charts'
    );
})();
