<?php

namespace Hoogi91\Charts\Form\Types;

class PieChart extends AbstractChartType
{

    public static function getIdentifier(): string
    {
        return 'chart_pie';
    }

    public static function getIconIdentifier(): string
    {
        return 'tx_charts_pie_chart';
    }
}
