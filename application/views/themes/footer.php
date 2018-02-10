            <hr />
            <footer>
                <p style="float:left;">&copy; 07Tracker 2015 &middot; <?=anchor('contact', 'Contact Us');?></p>
                <p style="float:right;">RuneScape &reg; is a trademark of Jagex and &copy; 1999 - 2015 Jagex Ltd. <!--Page executed in {elapsed_time} seconds, Memory usage: {memory_usage} --></p>
            </footer>
        </div>
        <script src="<?=base_url();?>assets/js/bootstrap-dropdown.js"></script>
        <script src="<?=base_url();?>assets/js/bootstrap-select.min.js"></script>
        <script src="<?=base_url();?>assets/js/bootstrap-collapse.js"></script>
        <script type="text/javascript">$('.selectpicker').selectpicker();</script>
        <?php if($this->uri->segment(1) == "track" || $this->uri->segment(1) == "playertrack"): ?>
        <script src="http://code.highcharts.com/stock/highstock.js"></script>
        <?php endif; ?>
        <?php if(isset($userGraphData)): ?>
        <script type="text/javascript">
        $(function () {
            var chart;
            $(document).ready(function () {
                chart = new Highcharts.StockChart({
                    chart: {
                        renderTo: 'graph1',
                        zoomType: 'x'
                    },
                    title: {
                        text: 'Overall Graph'
                    },
                    credits: {
                        enabled: false
                    },
                    rangeSelector: {
                        buttons: [{
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
                        }, {
                            type: 'year',
                            count: 1,
                            text: '1y'
                        }, {
                            type: 'all',
                            text: 'All'
                        }],
                        selected: 5
                    },
                    legend: {
                        align: "right",
                        layout: "vertical",
                        enabled: true,
                        verticalAlign: "middle"
                    },
                    series: [{
                        name: 'Overall Rank',
                        data: [<?=implode(',', $userGraphData['ranks']);?>],
                        tooltip: {
                            valueDecimals: 0
                        }
                    }, {
                        name: 'Overall Experience',
                        data: [<?=implode(',', $userGraphData['xp']);?>],
                        tooltip: {
                            valueDecimals: 0
                        }
                    }, {
                        name: 'Overall Level',
                        data: [<?=implode(',', $userGraphData['levels']);?>],
                        tooltip: {
                            valueDecimals: 0
                        }
                    }]
                });
            });

        });
        </script>
        <?php endif;?>
    </body>
</html>