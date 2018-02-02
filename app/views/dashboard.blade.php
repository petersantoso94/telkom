@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
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
                            <h3 class="box-title">Monthly Recap Report</h3>

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
                                    <li class="active"><a href="#voc" data-toggle="tab" aria-expanded="true">VOUCHER</a></li>
                                    <li class=""><a href="#sim" data-toggle="tab" aria-expanded="false">SIM CARD</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="voc">
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
                                                        <span class="info-box-text">New Members</span>
                                                        <span class="info-box-number">2,000</span>
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
                                                <canvas id="barChart_ivr" style="height: 229px; width: 594px;" width="742" height="286"></canvas>
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
                                                <canvas id="barChart_churn" style="height: 229px; width: 594px;" width="742" height="286"></canvas>
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
                                                <canvas id="barChart_prod" style="height: 229px; width: 594px;" width="742" height="286"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="sim">
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
    var l_year = document.getElementById('ivr_year').value;
    var c_year = document.getElementById('churn_year').value;
    var p_year = document.getElementById('prod_year').value;
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

    window.onload = function () {
        var ctx = document.getElementById("barChart_ivr").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Monthly internet subscriber'
                }
            }
        });

        var ctx2 = document.getElementById("barChart_churn").getContext("2d");
        window.myBar2 = new Chart(ctx2, {
            type: 'bar',
            data: barChartData2,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Monthly internet subscriber'
                }, scales: {
                    xAxes: [{
                            stacked: true,
                        }],
                    yAxes: [{
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
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Monthly Productive User'
                }, scales: {
                    xAxes: [{
                            stacked: true,
                        }],
                    yAxes: [{
                            stacked: true
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
        $.post(getIVR, {year: l_year}, function (data) {

        }).done(function (data) {
            barChartData.datasets = [];
            $.each(data, function (index, value) {
                var colorName = colorNames[barChartData.datasets.length % colorNames.length];
                var dsColor = window.chartColors[colorName];
                barChartData.datasets.push({
                    label: index,
                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                    borderColor: dsColor,
                    borderWidth: 1,
                    data: value
                });
            });
            console.log(barChartData);
            window.myBar.update();
        });

        $.post(getCHURN, {year: c_year}, function (data) {

        }).done(function (data) {
            barChartData2.datasets = [];
            $.each(data, function (index, value) {
                $.each(value, function (index2, value2) {
                    var colorName = colorNames[barChartData2.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData2.datasets.push({
                        label: index2,
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value2
                    });
                });
            });
            console.log(barChartData2);
            window.myBar2.update();
        });

        $.post(getProductive, {year: p_year}, function (data) {

        }).done(function (data) {
            barChartData3.datasets = [];
            $.each(data, function (index, value) {
                var colorName = colorNames[barChartData3.datasets.length % colorNames.length];
                var dsColor = window.chartColors[colorName];
                barChartData3.datasets.push({
                    label: index,
                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                    borderColor: dsColor,
                    borderWidth: 1,
                    data: value
                });
            });
            console.log(barChartData3);
            window.myBar3.update();
        });
    }
    window.showChart = function (element) {
        var chartID = $(element).data('id');
        var x = document.getElementById(chartID);
        $('.toogling').hide();
        if (x.style.display === "none") {
            x.style.display = "block";
            refreshBarChart();
        } else {
            x.style.display = "none";
        }
    }
</script>
@stop
