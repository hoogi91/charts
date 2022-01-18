<?php

namespace Hoogi91\Charts\Form\Types;

interface ChartTypeInterface
{
    public const LANGUAGE_FILE = 'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf';

    public static function getIdentifier(): string;

    public static function getIconIdentifier(): string;

    public static function register(array $columnOverrides = []): void;
}
