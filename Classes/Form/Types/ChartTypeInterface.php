<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Form\Types;

interface ChartTypeInterface
{
    public const LANGUAGE_FILE = 'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf';

    public static function getIdentifier(): string;

    public static function getIconIdentifier(): string;

    /**
     * @param array<mixed> $columnOverrides
     */
    public static function register(array $columnOverrides = []): void;
}
