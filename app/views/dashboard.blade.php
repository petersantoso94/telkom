@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
<a href="{{Route('showDashboard')}}" style="color: #ffffff;
   font-weight: 400;
   display: inline-block;
   font-size: 18px;
   padding: 5px 0;">Dashboard</a>
@stop

@section('main-section')
<div class="wrapper">          
    <!-- Content Wrapper. Contains page content -->
    <div>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-md-24">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Monthly Report</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-wrench"></i></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Action</a></li>
                                        <li><a href="#">Another action</a></li>
                                        <li><a href="#">Something else here</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#subs" data-toggle="tab" aria-expanded="true">Subsciber</a></li>
                                    <li class=""><a href="#vocres" data-toggle="tab" aria-expanded="false">Voucher Recharge</a></li>
                                    <li class=""><a href="#intus" data-toggle="tab" aria-expanded="false">Internet Usage</a></li>
                                    <li class=""><a href="#shipout" data-toggle="tab" aria-expanded="false">Shipout Report</a></li>
                                    <li class=""><a href="#weekly" data-toggle="tab" aria-expanded="false">Weekly Performance</a></li>
                                    <li class=""><a href="#subagent1" data-toggle="tab" aria-expanded="false">Subagent #1</a></li>
                                    <li class=""><a href="#subagent2" data-toggle="tab" aria-expanded="false">Subagent SIM #2</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="subs">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Monthly Internet Subscriber</span>

                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_ivr_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Subsriber and Churn</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_churn_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>



                                            <!-- /.col -->

                                            <!-- fix for small devices only -->
                                            <div class="clearfix visible-sm-block"></div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Productive User</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_prod_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Service Usage</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_sum_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                        <div class="row toogling" id="info_ivr_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="ivr_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend" class="legend"></div>
                                                <canvas id="barChart_ivr" ></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_churn_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="churn_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend2" class="legend"></div>
                                                <canvas id="barChart_churn"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_prod_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="prod_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend3" class="legend"></div>
                                                <canvas id="barChart_prod"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_sum_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="sum_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend4" class="legend"></div>
                                                <canvas id="barChart_sum"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="vocres">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Monthly average shipout</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="intus">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Monthly average shipout</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="shipout">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Channel Reporting</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="weekly">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Performance Report</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="subagent1">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Shipout to</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="subagent2">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">ASPROF ESTHER</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_month" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-md-24">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Weekly Recap Report</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-wrench"></i></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Action</a></li>
                                        <li><a href="#">Another action</a></li>
                                        <li><a href="#">Something else here</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#voc_w" data-toggle="tab" aria-expanded="true">VOUCHER</a></li>
                                    <li class=""><a href="#sim_w" data-toggle="tab" aria-expanded="false">SIM CARD</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="voc_w">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Weekly average shipout</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_voc_week" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Likes</span>
                                                        <span class="info-box-number">41,410</span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>



                                            <!-- /.col -->

                                            <!-- fix for small devices only -->
                                            <div class="clearfix visible-sm-block"></div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Sales</span>
                                                        <span class="info-box-number">760</span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">New Members</span>
                                                        <span class="info-box-number">2,000</span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="sim_w">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Weekly average shipout</span>
                                                        <span class="info-box-number"><small> pcs</small></span>
                                                        <a  class="small-box-footer" href="#info_sim_week" data-toggle="tab">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div>
@stop

@section('js-content')
<script>
    var getIVR = '<?php echo Route('getIVR') ?>';
    var getCHURN = '<?php echo Route('getCHURN') ?>';
    var getProductive = '<?php echo Route('getProductive') ?>';
    var getSumService = '<?php echo Route('getSumService') ?>';
    var l_year = document.getElementById('ivr_year').value;
    var c_year = document.getElementById('churn_year').value;
    var p_year = document.getElementById('prod_year').value;
    var s_year = document.getElementById('sum_year').value;
    var colorNames = Object.keys(window.chartColors);
    $('#ivr_year').on('change', function (e) {
        l_year = document.getElementById('ivr_year').value;
        refreshBarChart();
    });

    $('#churn_year').on('change', function (e) {
        c_year = document.getElementById('churn_year').value;
        refreshBarChart();
    });

    $('#prod_year').on('change', function (e) {
        p_year = document.getElementById('prod_year').value;
        refreshBarChart();
    });

    $('#sum_year').on('change', function (e) {
        s_year = document.getElementById('sum_year').value;
        refreshBarChart();
    });

    var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var color = Chart.helpers.color;
    var barChartData = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData2 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData3 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData4 = {
        labels: MONTHS,
        datasets: []
    };

    window.onload = function () {
        var ctx = document.getElementById("barChart_ivr").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    } // end callbacks:
                },
                title: {
                    display: true,
                    text: 'Monthly internet subscriber'
                }, scales: {
                    yAxes: [{
                            id: 'A',
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            }
                        }, {
                            id: 'B',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                max: 1,
                                min: 0,
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            }
                        }]
                }
            }
        });

        var ctx2 = document.getElementById("barChart_churn").getContext("2d");
        window.myBar2 = new Chart(ctx2, {
            type: 'bar',
            data: barChartData2,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false
                },
                tooltips: {
                    position: 'average',
                    mode: 'index',
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    } // end callbacks:
                },
                title: {
                    display: true,
                    text: 'Subscriber and Churn'
                }, scales: {
                    xAxes: [{
                            stacked: true,
                        }],
                    yAxes: [{
                            id: 'A',
                            type: 'linear',
                            position: 'left',
                            stacked: true,
                            ticks: {
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            }
                        }, {
                            id: 'B',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                max: 1,
                                min: 0,
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            },
                            stacked: true
                        }]
                }
            }
        });

        var ctx3 = document.getElementById("barChart_prod").getContext("2d");
        window.myBar3 = new Chart(ctx3, {
            type: 'bar',
            data: barChartData3,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    callbacks: {
                        footer: function (tooltipItems, data) {
                            var sum = 0;
                            tooltipItems.forEach(function (tooltipItem) {
                                sum += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            });
                            return 'Total: ' + sum;
                        },
                        label: function (tooltipItem, data) {
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    } // end callbacks:
                },
                title: {
                    display: true,
                    text: 'Monthly Productive User'
                }, scales: {
                    xAxes: [{
                            stacked: true,
                        }],
                    yAxes: [{
                            id: 'A',
                            type: 'linear',
                            position: 'left',
                            stacked: true,
                            ticks: {
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            }
                        }, {
                            id: 'B',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                max: 1,
                                min: 0,
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            },
                            stacked: true
                        }]
                }
            }
        });

        var ctx4 = document.getElementById("barChart_sum").getContext("2d");
        window.myBar4 = new Chart(ctx4, {
            type: 'bar',
            data: barChartData4,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return value;
                        }
                    } // end callbacks:
                },
                title: {
                    display: true,
                    text: 'Monthly Total Service Usage'
                }, scales: {
                    yAxes: [{
                            id: 'A',
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            }
                        }, {
                            id: 'B',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                max: 1,
                                min: 0,
                                userCallback: function (value, index, values) {
                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);
                                    value = value.join(',');
                                    return value;
                                }
                            },
                        }]
                }
            }
        });

        refreshBarChart();
    };


    //-------------
    //- BAR CHART -
    //-------------
    var refreshBarChart = function () {
        var datasetz = [];
        if (chartID == 'info_ivr_month') {
            $.post(getIVR, {year: l_year}, function (data) {

            }).done(function (data) {
                barChartData.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar.update();
                // Find the scale in the chart instance
                var axis = myBar.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar.options.scales.yAxes[1].ticks.min = min;
                myBar.options.scales.yAxes[1].ticks.max = max;
                window.myBar.update();
                document.getElementById('legend').innerHTML = myBar.generateLegend();
            });
        } else if (chartID == 'info_churn_month') {

            $.post(getCHURN, {year: c_year}, function (data) {

            }).done(function (data) {
                barChartData2.datasets = [];
                $.each(data, function (index, value) {
                    $.each(value, function (index2, value2) {
                        var colorName = colorNames[barChartData2.datasets.length % colorNames.length];
                        var dsColor = window.chartColors[colorName];
                        barChartData2.datasets.push({
                            label: index2,
                            yAxisID: 'A',
                            backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                            borderColor: dsColor,
                            borderWidth: 1,
                            data: value2
                        });
                    });
                });
                window.myBar2.update();
                // Find the scale in the chart instance
                var axis = myBar2.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar2.options.scales.yAxes[1].ticks.min = min;
                myBar2.options.scales.yAxes[1].ticks.max = max;
                window.myBar2.update();
                document.getElementById('legend2').innerHTML = myBar2.generateLegend();
            });
        } else if (chartID == 'info_prod_month') {
            $.post(getProductive, {year: p_year}, function (data) {

            }).done(function (data) {
                barChartData3.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData3.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData3.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar3.update();
                // Find the scale in the chart instance
                var axis = myBar3.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar3.options.scales.yAxes[1].ticks.min = min;
                myBar3.options.scales.yAxes[1].ticks.max = max;
                window.myBar3.update();
                document.getElementById('legend3').innerHTML = myBar3.generateLegend();
            });
        } else if (chartID == 'info_sum_month') {
            $.post(getSumService, {year: s_year}, function (data) {

            }).done(function (data) {
                barChartData4.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData4.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData4.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar4.update();
                // Find the scale in the chart instance
                var axis = myBar4.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar4.options.scales.yAxes[1].ticks.min = min;
                myBar4.options.scales.yAxes[1].ticks.max = max;
                window.myBar4.update();
                document.getElementById('legend4').innerHTML = myBar4.generateLegend();
            });
        }
    }
    var chartID = '';
    window.showChart = function (element) {
        chartID = $(element).data('id');
        var x = document.getElementById(chartID);
        if (x.style.display === "none") {
            $('.toogling').hide();
            x.style.display = "block";
            $(element).html('Hide Chart <i class="fa fa-arrow-circle-right"></i>');
            refreshBarChart();
        } else if (x.style.display === "block") {
            x.style.display = "none";
            $(element).html('Show Chart <i class="fa fa-arrow-circle-right"></i>');
        }
    }
</script>
@stop
