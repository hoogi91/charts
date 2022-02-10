var Hoogi91 = Hoogi91 || {};
Hoogi91.Charts = {
    chartsData: null,
    chartsContainer: null,

    /**
     * initialize all charts that can be found if available options are filled
     */
    init: function () {
        if (typeof Hoogi91.chartsData === 'object') {
            this.chartsData = Hoogi91.chartsData;
        }

        this.chartsContainer = document.querySelectorAll('.chart-container > .chart');
        if (this.chartsContainer.length > 0 && this.chartsData !== null && Object.keys(this.chartsData).length > 0) {
            for (var i = 0; i < this.chartsContainer.length; ++i) {
                var element = this.chartsContainer[i];

                // check id, type and datakey of current chart element and break if something is not available
                var type = element.getAttribute('data-chart-type') || '';
                var dataKey = element.getAttribute('data-chart-data') || '';
                if (type.length <= 0 || dataKey.length <= 0) {
                    continue;
                }

                var chartData = this.getChartData(dataKey);
                if (chartData.length <= 0) {
                    continue;
                }

                var labels = chartData.labels || [];
                var datasets = chartData.datasets || [];
                if (labels.length > 0 && datasets.length > 0) {
                    switch (type) {
                        case 'chart_bar':
                            this.createBarChart(element, labels, datasets);
                            break;
                        case 'chart_line':
                            this.createLineChart(element, labels, datasets);
                            break;
                        case 'chart_pie':
                            this.createPieChart(element, labels, datasets);
                            break;
                        case 'chart_doughnut':
                            this.createDoughnutChart(element, labels, datasets);
                            break;
                    }
                }
            }
        }
    },

    /**
     * get chart data by key
     *
     * @param key
     * @returns object
     */
    getChartData: function (key) {
        if (typeof this.chartsData[key] === 'undefined') {
            return {};
        }
        return this.chartsData[key];
    },

    /**
     * build bar chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createBarChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        var chart = new ApexCharts(element, {
            chart: {
                type: 'bar',
                stacked: _this.getKeyOfObject(options, 'bar.stacked', 0) === '1',
            },
            series: _this.createDatasets(datasets, function (set) {
                var dataset = _this.createDatasets(set['data'], function (value, index) {
                    var dataRow = {x: labels[index] || '', y: value};
                    if (datasets.length === 1) {
                        dataRow['fillColor'] = set['background'][index] || '';
                        dataRow['strokeColor'] = set['border'][index] || '';
                    }
                    return dataRow;
                });
                return {name: set['label'], data: dataset}
            }),
            labels: labels,
            colors: datasets[0]['border'] || datasets[0]['background'] || undefined,
            dataLabels: {enabled: false},
            stroke: {width: 1},
            plotOptions: {
                bar: {
                    horizontal: _this.getKeyOfObject(options, 'bar.horizontal', 0) === '1',
                }
            },
            xaxis: _this.getTicksConfig(options, 'x'),
            yaxis: _this.getTicksConfig(options, 'y'),
            legend: {
                show: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                position: _this.getKeyOfObject(options, 'legend.position', 'top'),
            },
        });
        chart.render();

        return chart;
    },

    /**
     * build line chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createLineChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        var chart = new ApexCharts(element, {
            chart: {
                type: _this.getKeyOfObject(options, 'line.fill', 0) === '1' ? 'area' : 'line',
                stacked: _this.getKeyOfObject(options, 'line.stacked', 0) === '1',
            },
            series: _this.createDatasets(datasets, function (set) {
                return {name: set['label'], data: set['data']};
            }),
            labels: labels,
            colors: datasets[0]['border'] || datasets[0]['background'] || undefined,
            fill: {
                colors: datasets[0]['border'] || datasets[0]['background'] || undefined,
            },
            dataLabels: {enabled: false},
            stroke: {
                colors: datasets[0]['background'] || undefined,
                curve: _this.getKeyOfObject(options, 'line.stepped', 'smooth'),
            },
            markers: {
                size: [5, 7],
                colors: datasets[0]['border'] || undefined,
            },
            xaxis: _this.getTicksConfig(options, 'x'),
            yaxis: _this.getTicksConfig(options, 'y'),
            legend: {
                show: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                position: _this.getKeyOfObject(options, 'legend.position', 'top'),
            },
        });
        chart.render();

        return chart;
    },

    /**
     * build pie chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createPieChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        console.log(labels)
        var chart = new ApexCharts(element, {
            chart: {type: 'pie'},
            colors: datasets[0]['background'] || datasets[0]['border'] || [],
            series: datasets[0]['data'] || [],
            labels: labels,
            dataLabels: {enabled: false},
            legend: {
                show: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                position: _this.getKeyOfObject(options, 'legend.position', 'top'),
            },
        });
        chart.render();

        return chart;
    },

    /**
     * build doughnut chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createDoughnutChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        var cutoutValue = parseInt(_this.getKeyOfObject(options, 'doughnut.cutoutPercentage', 75), 10);
        var chart = new ApexCharts(element, {
            chart: {type: 'donut'},
            colors: datasets[0]['background'] || datasets[0]['border'] || [],
            series: datasets[0]['data'] || [],
            labels: labels,
            dataLabels: {enabled: false},
            plotOptions: {
                pie: {donut: {size: cutoutValue <= 0 ? 75 : cutoutValue}}
            },
            legend: {
                show: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                position: _this.getKeyOfObject(options, 'legend.position', 'top'),
            },
        });
        chart.render();

        return chart;
    },

    /**
     * create datasets by given data and an additional mapping callback
     *
     * @param datasets
     * @param mapping
     * @returns array
     */
    createDatasets: function (datasets, mapping) {
        var keyValueMapping = mapping || null,
            processedDatasets = [];

        if (typeof keyValueMapping !== 'function') {
            return processedDatasets;
        }

        if (typeof datasets === 'object' && datasets.length > 0) {
            for (var i = 0; i < datasets.length; ++i) {
                var set = keyValueMapping(datasets[i], i);
                processedDatasets.push(set);
            }
        }
        return processedDatasets;
    },

    getKeyOfObject: function (object, key, defaultValue) {
        var returnVal = key.split(".").reduce(function (o, x) {
            return (typeof o === 'undefined' || o === null) ? o : o[x];
        }, object);

        return typeof returnVal !== 'undefined' ? returnVal : defaultValue;
    },

    getTicksConfig: function (config, axis) {
        var ticksConf = {
            title: {text: this.getKeyOfObject(config, 'axis.' + axis + '.label', undefined)}
        };
        var calculateAutomatic = this.getKeyOfObject(config, 'axis.' + axis + '.auto', 0) === '1';
        if (calculateAutomatic === true) {
            return ticksConf;
        }

        var min = this.getKeyOfObject(config, 'axis.' + axis + '.min', null);
        if (min !== null) {
            ticksConf.min = parseInt(min, 10);
        }
        var max = this.getKeyOfObject(config, 'axis.' + axis + '.max', null);
        if (max !== null && max > min) {
            ticksConf.max = parseInt(max, 10);
        }
        return ticksConf;
    },

    getChartOptions: function (element) {
        var chartConfig = {};
        try {
            chartConfig = JSON.parse(element.getAttribute('data-chart-config')) || {};
        } catch (e) {
        }

        if (Object.keys(chartConfig).length <= 0) {
            return {};
        }
        return chartConfig;
    }
};

document.addEventListener("DOMContentLoaded", function () {
    Hoogi91.Charts.init();
});
