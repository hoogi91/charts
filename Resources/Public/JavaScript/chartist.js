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
                        case 'bar':
                            this.createBarChart(element, labels, datasets);
                            break;
                        case 'line':
                            this.createLineChart(element, labels, datasets);
                            break;
                        case 'pie':
                            this.createPieChart(element, labels, datasets);
                            break;
                        case 'doughnut':
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
        return new Chartist.Bar(element, {
            labels: labels,
            series: _this.createDatasets(datasets, function (set) {
                return set['data']
            })
        }, {
            stackBars: _this.getKeyOfObject(options, 'bar.stacked', 0) === '1',
            horizontalBars: _this.getKeyOfObject(options, 'bar.horizontal', 0) === '1'
        });
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
        var steppedValue = _this.getKeyOfObject(options, 'line.stepped', false);

        var lineSmootheValue = _this.getKeyOfObject(options, 'line.interpolation', 0) === '1';
        if (steppedValue === '1' || steppedValue === 'after') {
            lineSmootheValue = Chartist.Interpolation.step({
                postpone: steppedValue === 'after',
                fillHoles: true
            });
        }

        return new Chartist.Line(element, {
            labels: labels,
            series: _this.createDatasets(datasets, function (set) {
                return set['data']
            })
        }, {
            fullWidth: true,
            chartPadding: {
                right: 40
            },
            showArea: _this.getKeyOfObject(options, 'line.fill', 0) === '1',
            lineSmooth: lineSmootheValue
        });
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
        var series = _this.createDatasets(datasets, function (set) {
            return set['data']
        });
        return new Chartist.Pie(element, {
            labels: labels,
            series: series[0]
        }, {});
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
        var series = _this.createDatasets(datasets, function (set) {
            return set['data']
        });
        var cutoutValue = parseInt(_this.getKeyOfObject(options, 'doughnut.cutoutPercentage', 60), 10);
        cutoutValue = 100 - (cutoutValue <= 0 ? 60 : cutoutValue);

        return new Chartist.Pie(element, {
            labels: labels,
            series: series[0]
        }, {
            donut: true,
            donutWidth: cutoutValue + '%',
            donutSolid: true
        });
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
                var set = keyValueMapping(datasets[i]);
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
