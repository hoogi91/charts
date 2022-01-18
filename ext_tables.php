<?php
defined('TYPO3') or die();

(static function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_charts_domain_model_chartdata');
})();
