<?php

defined('TYPO3') or die();

(static function (string $extKey = 'charts') {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets'][$extKey] = 'EXT:' . $extKey . '/Resources/Public/Css/Backend/styles.css';
})();
