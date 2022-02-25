<?php

defined('TYPO3') or die();

(static function (string $extKey = 'charts') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_charts_domain_model_chartdata');
    $GLOBALS['TBE_STYLES']['skins'][$extKey]['stylesheetDirectories'][] = 'EXT:' . $extKey . '/Resources/Public/Css/Backend/';
})();
