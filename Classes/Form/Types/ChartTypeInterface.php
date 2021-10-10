<?php

namespace Hoogi91\Charts\Form\Types;

interface ChartTypeInterface
{
    public const LANGUAGE_FILE = 'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf';

    /**
     * @return string
     */
    public static function getIdentifier(): string;

    /**
     * @return string
     */
    public static function getIconIdentifier(): string;

    /**
     * Register chart type
     */
    public static function register(array $columnOverrides = []): void;
}
