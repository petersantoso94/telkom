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
                                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Voucher Topup</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_voc_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">eVoucher Topup</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_evoc_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Subsriber Topup</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_subs_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_voc_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="voc_topup_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend8" class="legend"></div>
                                                <canvas id="barChart_voc_topup"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_evoc_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="evoc_topup_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend9" class="legend"></div>
                                                <canvas id="barChart_evoc_topup"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_subs_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="subs_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend10" class="legend"></div>
                                                <canvas id="barChart_subs_topup"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="intus">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">IVR Purchased Internet</span>

                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_ivr_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Internet Users and Usage</span>

                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_internet_payloads">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Payload per User</span>

                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_internet_payloads_peruser">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>

                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-sellsy"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Internet User vs Non-Internet User</span>

                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_internet_vs">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
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
                                        <div class="row toogling" id="info_internet_payloads" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="payload_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend5" class="legend"></div>
                                                <canvas id="barChart_internet_payload" ></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_internet_payloads_peruser" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="payload_peruser_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend6" class="legend"></div>
                                                <canvas id="barChart_internet_payload_peruser" ></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_internet_vs" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="vs_year">
                                                    @foreach(DB::table('r_stats')->select('Year')->orderBy('Year','DESC')->distinct()->get() as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="chart">
                                                <div id="legend7" class="legend"></div>
                                                <canvas id="barChart_internet_vs" ></canvas>
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
    var getPayload = '<?php echo Route('getPayload') ?>';
    var getPayloadPeruser = '<?php echo Route('getPayloadPerUser') ?>';
    var getInternetVsNon = '<?php echo Route('getInternetVsNon') ?>';
    var getVouchersTopUp = '<?php echo Route('getVouchersTopUp') ?>';
    var getMSISDNTopUp = '<?php echo Route('getMSISDNTopUp') ?>';
    var l_year = document.getElementById('ivr_year').value;
    var c_year = document.getElementById('churn_year').value;
    var p_year = document.getElementById('prod_year').value;
    var s_year = document.getElementById('sum_year').value;
    var internet_payload_year = document.getElementById('payload_year').value;
    var internet_payload_peruser_year = document.getElementById('payload_peruser_year').value;
    var internet_vs_year = document.getElementById('vs_year').value;
    var voc_topup_year = document.getElementById('voc_topup_year').value;
    var evoc_topup_year = document.getElementById('evoc_topup_year').value;
    var subs_year = document.getElementById('subs_year').value;
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
    $('#payload_year').on('change', function (e) {
        internet_payload_year = document.getElementById('payload_year').value;
        refreshBarChart();
    });
    $('#payload_peruser_year').on('change', function (e) {
        internet_payload_peruser_year = document.getElementById('payload_peruser_year').value;
        refreshBarChart();
    });
    $('#vs_year').on('change', function (e) {
        internet_vs_year = document.getElementById('vs_year').value;
        refreshBarChart();
    });
    $('#voc_topup_year').on('change', function (e) {
        voc_topup_year = document.getElementById('voc_topup_year').value;
        refreshBarChart();
    });
    $('#evoc_topup_year').on('change', function (e) {
        evoc_topup_year = document.getElementById('evoc_topup_year').value;
        refreshBarChart();
    });
    $('#subs_year').on('change', function (e) {
        subs_year = document.getElementById('subs_year').value;
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
    var barChartData5 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData6 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData7 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData8 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData9 = {
        labels: MONTHS,
        datasets: []
    };
    var barChartData10 = {
        labels: MONTHS,
        datasets: []
    };

    // Define a plugin to provide data labels
    Chart.plugins.register({
        afterDatasetsDraw: function (chart, easing) {
            // To only draw at the end of animation, check for easing === 1
            var ctx = chart.ctx;
            var total = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            var grandtotal = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            var x_axis = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            var y_axis = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            var write = false;
            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                var idx = i;
                if (!meta.hidden) {
                    meta.data.forEach(function (element, index) {
                        // Draw the text in black, with the specified font
                        ctx.fillStyle = 'rgb(0, 0, 0)';
                        var fontSize = 16;
                        var fontStyle = 'normal';
                        var fontFamily = 'Helvetica Neue';
                        ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                        // Just naively convert to string for now

                        total[index] += dataset.data[index];
                        x_axis[index] = element._model.x;
                        y_axis[index] = element._model.y - 7;

                        var dataString = dataset.data[index].toString();
                        dataString = dataString.split(/(?=(?:...)*$)/);
                        dataString = dataString.join(',');
                        // Make sure alignment settings are correct
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        var canvas_height = ctx.canvas.clientHeight;
                        var padding = ((element._model.base - element._model.y) / 2);
                        var position = element.tooltipPosition();
                        var y_height = element._yScale.height;
//                        if (dataString.includes('-')) {
//                            padding = padding * -1;
//                        }
//                        ctx.fillText(dataString, position.x, position.y +((canvas_height -position.y )/2)+ (fontSize / 2) + padding - (canvas_height - y_height));
                        ctx.fillText(dataString, element._model.x, element._model.y + padding);
                    });
                }
                if (meta.controller.chart.canvas.id == 'barChart_prod')
                    write = true;
            });
            if (write) {
                for (var i = 0; i < 12; i++) {
                    ctx.fillText('Total: '+total[i].toString(), x_axis[i], y_axis[i]);
                }
            }
        }
    });

    Chart.defaults.global.responsive = true;
    Chart.defaults.global.maintainAspectRatio = true;

    window.onload = function () {
        var ctx = document.getElementById("barChart_ivr").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: barChartData,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'IVR Purchased Internet'
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
//                maintainAspectRatio: true,
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
//                maintainAspectRatio: true,
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
//                maintainAspectRatio: true,
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

        var ctx5 = document.getElementById("barChart_internet_payload").getContext("2d");
        window.myBar5 = new Chart(ctx5, {
            type: 'bar',
            data: barChartData5,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Monthly Internet Usage'
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
        var ctx6 = document.getElementById("barChart_internet_payload_peruser").getContext("2d");
        window.myBar6 = new Chart(ctx6, {
            type: 'bar',
            data: barChartData6,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Monthly Payload Per User'
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

        var ctx7 = document.getElementById("barChart_internet_vs").getContext("2d");
        window.myBar7 = new Chart(ctx7, {
            type: 'bar',
            data: barChartData7,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Internet vs Non-Internet User'
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

        var ctx8 = document.getElementById("barChart_voc_topup").getContext("2d");
        window.myBar8 = new Chart(ctx8, {
            type: 'bar',
            data: barChartData8,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Monthly Voucher Topup'
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

        var ctx9 = document.getElementById("barChart_evoc_topup").getContext("2d");
        window.myBar9 = new Chart(ctx9, {
            type: 'bar',
            data: barChartData9,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Monthly eVoucher Topup'
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

        var ctx10 = document.getElementById("barChart_subs_topup").getContext("2d");
        window.myBar10 = new Chart(ctx10, {
            type: 'bar',
            data: barChartData10,
            options: {
                responsive: true,
//                maintainAspectRatio: true,
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
                    text: 'Monthly Subscriber Topup'
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
        } else if (chartID == 'info_internet_payloads') {
            $.post(getPayload, {year: internet_payload_year}, function (data) {

            }).done(function (data) {
                barChartData5.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData5.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData5.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar5.update();
                // Find the scale in the chart instance
                var axis = myBar5.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar5.options.scales.yAxes[1].ticks.min = min;
                myBar5.options.scales.yAxes[1].ticks.max = max;
                window.myBar5.update();
                document.getElementById('legend5').innerHTML = myBar5.generateLegend();
            });
        } else if (chartID == 'info_internet_payloads_peruser') {
            $.post(getPayloadPeruser, {year: internet_payload_peruser_year}, function (data) {

            }).done(function (data) {
                barChartData6.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData6.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData6.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar6.update();
                // Find the scale in the chart instance
                var axis = myBar6.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar6.options.scales.yAxes[1].ticks.min = min;
                myBar6.options.scales.yAxes[1].ticks.max = max;
                window.myBar6.update();
                document.getElementById('legend6').innerHTML = myBar6.generateLegend();
            });
        } else if (chartID == 'info_internet_vs') {
            $.post(getInternetVsNon, {year: internet_vs_year}, function (data) {

            }).done(function (data) {
                barChartData7.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData7.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData7.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar7.update();
                // Find the scale in the chart instance
                var axis = myBar7.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar7.options.scales.yAxes[1].ticks.min = min;
                myBar7.options.scales.yAxes[1].ticks.max = max;
                window.myBar7.update();
                document.getElementById('legend7').innerHTML = myBar7.generateLegend();
            });
        } else if (chartID == 'info_voc_topup') {
            $.post(getVouchersTopUp, {year: voc_topup_year}, function (data) {

            }).done(function (data) {
                barChartData8.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData8.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData8.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar8.update();
                // Find the scale in the chart instance
                var axis = myBar8.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar8.options.scales.yAxes[1].ticks.min = min;
                myBar8.options.scales.yAxes[1].ticks.max = max;
                window.myBar8.update();
                document.getElementById('legend8').innerHTML = myBar8.generateLegend();
            });
        } else if (chartID == 'info_evoc_topup') {
            $.post(getVouchersTopUp, {year: evoc_topup_year, type: 1}, function (data) {

            }).done(function (data) {
                barChartData9.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData9.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData9.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar9.update();
                // Find the scale in the chart instance
                var axis = myBar9.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar9.options.scales.yAxes[1].ticks.min = min;
                myBar9.options.scales.yAxes[1].ticks.max = max;
                window.myBar9.update();
                document.getElementById('legend9').innerHTML = myBar9.generateLegend();
            });
        } else if (chartID == 'info_subs_topup') {
            $.post(getMSISDNTopUp, {year: subs_year}, function (data) {

            }).done(function (data) {
                barChartData10.datasets = [];
                $.each(data, function (index, value) {
                    var colorName = colorNames[barChartData10.datasets.length % colorNames.length];
                    var dsColor = window.chartColors[colorName];
                    barChartData10.datasets.push({
                        label: index,
                        yAxisID: 'A',
                        backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                        borderColor: dsColor,
                        borderWidth: 1,
                        data: value
                    });
                });
                window.myBar10.update();
                // Find the scale in the chart instance
                var axis = myBar10.scales.A;
                var max = axis.max;
                var min = axis.min;
                myBar10.options.scales.yAxes[1].ticks.min = min;
                myBar10.options.scales.yAxes[1].ticks.max = max;
                window.myBar10.update();
                document.getElementById('legend10').innerHTML = myBar10.generateLegend();
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
