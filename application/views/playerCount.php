<?php include 'themes/header.php'; ?>
            <div class="row">
            	<div class="alert alert-info">
	        	Head over to <a style="color:white;" href="http://www.runestats.com/#total">RuneStats.com</a> for live Oldschool RuneScape world count and total player count.
	        </div>
                <div class="span12">
                    <h3>RuneScape Player Count Tracker</h3>
                    <div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
                </div>
                <div class="span8 offset4">
                    <table class="table table-striped span4">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Lowest</th>
                                <th>Highest</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2007Scape</td>
                                <td><?=number_format($counters['minCount']);?></td>
                                <td><?=number_format($counters['maxCount']);?></td>
                            </tr>
                            <tr>
                                <td>RuneScape 3</td>
                                <td><?=number_format($counters['minRSCount']);?></td>
                                <td><?=number_format($counters['maxRSCount']);?></td>
                            </tr>
                            <tr>
                                <td>Combined</td>
                                <td><?=number_format($counters['minTotalCount']);?></td>
                                <td><?=number_format($counters['maxTotalCount']);?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    var chart;
                    var start = +new Date();
                    $(document).ready(function () {
                        chart = new Highcharts.StockChart({
                            chart: {
                                renderTo: 'container',
                                events: {
                                    load: function (chart) {
                                        this.setTitle(null, {
                                            text: 'Built chart at ' + (new Date() - start) + 'ms'
                                        });
                                    }
                                },
                                zoomType: 'x'
                            },
                            title: {
                                text: 'RuneScape Player Tracker'
                            },
                            subtitle: {
                                // text: ''
                            },
                            credits: {
                                enabled: false
                            },
                            rangeSelector: {
                                buttons: [{
                                    type: 'day',
                                    count: 1,
                                    text: '1d'
                                }, {
                                    type: 'day',
                                    count: 3,
                                    text: '3d'
                                }, {
                                    type: 'week',
                                    count: 1,
                                    text: '1w'
                                }, {
                                    type: 'month',
                                    count: 1,
                                    text: '1m'
                                }, {
                                    type: 'month',
                                    count: 6,
                                    text: '6m'
                                },/* {
                                    type: 'year',
                                    count: 1,
                                    text: '1y'
                                },*/ {
                                    type: 'all',
                                    text: 'All'
                                }],
                                selected: 0
                            },
                            yAxis: {
                                title: {
                                    text: 'Player Count'
                                }
                            },
                            legend: {
                                align: "center",
                                layout: "horizontal",
                                enabled: true,
                                verticalAlign: "bottom"
                            },
                            series: [{
                                name: '2007 Count',
                                pointStart: Date.UTC(2013, 1, 22),
                                pointInterval: 300 * 1000,
                                data: [ <?= implode(',', $javascript['07count']); ?> ],
                                tooltip: {
                                    valueDecimals: 0
                                }
                            }, {
                                name: 'RS3 Count',
                                pointStart: Date.UTC(2013, 1, 22),
                                pointInterval: 300 * 1000,
                                data: [ <?= implode(',', $javascript['rsCount']); ?> ],
                                tooltip: {
                                    valueDecimals: 0
                                }
                            }, {
                                name: 'Total Count',
                                pointStart: Date.UTC(2013, 1, 22),
                                pointInterval: 300 * 1000,
                                data: [ <?= implode(',', $javascript['totalCount']); ?> ],
                                tooltip: {
                                    valueDecimals: 0
                                }
                            }]
                        });
                    });
                });
            </script>
<?php include 'themes/footer.php'; ?>