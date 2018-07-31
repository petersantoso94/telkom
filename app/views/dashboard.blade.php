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
                            <div class="loader" id="loading-animation-top" style="display:none;" style="position: absolute; left:50%; top: 40%;"></div>
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#subs" data-toggle="tab" aria-expanded="true">Subscriber</a></li>
                                    <li class=""><a href="#shipout" data-toggle="tab" aria-expanded="true">Shipout</a></li>
                                    <li class=""><a href="#churn" data-toggle="tab" aria-expanded="true">Churn</a></li>
                                    <li class=""><a href="#vocres" data-toggle="tab" aria-expanded="false">Voucher Recharge</a></li>
                                    <li class=""><a href="#intus" data-toggle="tab" aria-expanded="false">Internet Usage</a></li>
                                    <li class=""><a href="#channel" data-toggle="tab" aria-expanded="false">Channel Reporting</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="subs">
                                        <div class="row">

                                            <!-- /.col -->
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">subscriber and Churn</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_churn_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">subscriber</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_subs_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
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
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

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
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

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
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global"  style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend2" class="legend"></div>
                                                <canvas id="barChart_churn" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_subs_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="subs2_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend13" class="legend"></div>
                                                <canvas id="barChart_subs" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_prod_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="prod_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend3" class="legend"></div>
                                                <canvas id="barChart_prod" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_sum_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="sum_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend4" class="legend"></div>
                                                <canvas id="barChart_sum" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="channel">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">subscriber per Channel</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_channel">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Churn per Channel</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_channel_churn">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_channel" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="channel_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend16" class="legend" style="font-size: 80%"></div>
                                                <canvas id="barChart_channel" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_channel_churn" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="channel_churn_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend17" class="legend" style="font-size: 80%"></div>
                                                <canvas id="barChart_channel_churn" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="shipout">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Sim Card</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_shipout_sim">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Vouchers</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_shipout_voc">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_shipout_sim" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="shipout_sim_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend18" class="legend"></div>
                                                <canvas id="barChart_shipout_sim" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_shipout_voc" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="shipout_voc_year">>
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global"  style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend19" class="legend"></div>
                                                <canvas id="barChart_shipout_voc" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="churn">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Churn</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_churn2">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Detail Churn</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_detail_churn_month">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_detail_churn_month" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="detail_churn_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend11" class="legend"></div>
                                                <canvas id="barChart_detail_churn" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_churn2" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="churn2_year">>
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global"  style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend12" class="legend"></div>
                                                <canvas id="barChart_churn2" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="vocres">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

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
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Unique Subscriber Topup</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_unique_subs_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Voucher Topup300</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_voc_topup300">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>

                                                    <div class="info-box-content">
                                                        <span class="info-box-text">eVoucher Topup</span>
                                                        <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_evoc_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <!--                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                                            <div class="info-box">
                                                                                                <span class="info-box-icon bg-yellow"><i class="fa fa-bar-chart"></i></span>
                                            
                                                                                                <div class="info-box-content">
                                                                                                    <span class="info-box-text">Subsriber Topup</span>
                                                                                                    <a href="#" class="small-box-footer" onclick="showChart(this)" data-id="info_subs_topup">Show Chart<i class="fa fa-arrow-circle-right"></i></a>
                                                                                                </div>
                                                                                                 /.info-box-content 
                                                                                            </div>
                                                                                             /.info-box 
                                                                                        </div>-->
                                        </div>
                                        <div class="row toogling" id="info_voc_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="voc_topup_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend8" class="legend"></div>
                                                <canvas id="barChart_voc_topup" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_unique_subs_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="unique_subs_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend15" class="legend"></div>
                                                <canvas id="barChart_unique_subs_topup" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_voc_topup300" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="voc_topup300_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend14" class="legend"></div>
                                                <canvas id="barChart_voc300_topup" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_evoc_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="evoc_topup_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend9" class="legend"></div>
                                                <canvas id="barChart_evoc_topup" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_subs_topup" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="subs_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend10" class="legend"></div>
                                                <canvas id="barChart_subs_topup" height="100"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="intus">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-area-chart"></i></span>

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
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-bar-chart"></i></span>

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
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-bar-chart"></i></span>

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
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-bar-chart"></i></span>

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
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend" class="legend"></div>
                                                <canvas id="barChart_ivr" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_internet_payloads" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="payload_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend5" class="legend"></div>
                                                <canvas id="barChart_internet_payload" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_internet_payloads_peruser" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="payload_peruser_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend6" class="legend"></div>
                                                <canvas id="barChart_internet_payload_peruser" height="100"></canvas>
                                            </div>
                                        </div>
                                        <div class="row toogling" id="info_internet_vs" style="display: none;">
                                            <div class="form-group col-md-2">
                                                <select class="form-control" id="vs_year">
                                                    @foreach($years as $year)
                                                    @if($year->Year >0)
                                                    <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'>
                                                <button type="button" class="btn btn-default btn-save-data" aria-label="Left Align">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <div class="loader loading-animation-global" style="display: none;"></div>
                                            </div>
                                            <div class="chart">
                                                <div id="legend7" class="legend"></div>
                                                <canvas id="barChart_internet_vs" height="100"></canvas>
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
            <div class="row" 
                 style='<?php if (Auth::user()->Position > 1) echo "visibility:hidden;" ?>'
                 >
                <div class="col-md-24">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Export excel Report</h3>
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
                            <div class="box-body">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs excel-report">
                                        <li class="active"><a href="#excel_shipout_container" data-toggle="tab" aria-expanded="true">Shipout Reporting</a></li>
                                        <li><a href="#excel_shipin_container" data-toggle="tab" aria-expanded="true">Shipin Reporting</a></li>
                                        <li><a href="#excel_usage_container" data-toggle="tab" aria-expanded="true">Usage Reporting</a></li>
                                        <li><a href="#excel_user_container" data-toggle="tab" aria-expanded="true">Per Customer Reporting</a></li>
                                        <li><a href="#excel_subagent_container" data-toggle="tab" aria-expanded="true">Per SubAgent Reporting</a></li>
                                        <li><a href="#excel_weekly_container" data-toggle="tab" aria-expanded="true">Weekly Performance</a></li>
                                        <li><a href="#excel_sim1_container" data-toggle="tab" aria-expanded="true">Shipout to Subagent</a></li>
                                        <li><a href="#excel_sim2_container" data-toggle="tab" aria-expanded="true">SubAgent SIM card Reporting</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="excel_sim2_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class="row margtop20">
                                                        <div class="col-xs-10">
                                                            Warehouse: 
                                                            <select data-placeholder="Choose a warehouse..." class="chosen-select" style="width: 100%" id="sim2_wh">
                                                                <option selected="" value="">All</option>
                                                                @foreach(DB::table('m_historymovement')->select('Warehouse')->distinct()->get() as $sn)
                                                                @if($sn->Warehouse != '')
                                                                <option value="{{$sn->Warehouse}}">
                                                                    {{$sn->Warehouse}}
                                                                </option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Year
                                                            <select style="width: 100%" id="sim2_year" class="chosen-select">
                                                                @foreach($years as $year)
                                                                @if($year->Year >0)
                                                                <option value="{{$year->Year}}">{{$year->Year}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Sub Agent: 
                                                            <select data-placeholder="Choose a destination..." class="chosen-select"  style="width: 100%" id="sim2_subagent">
                                                                <?php $counter = 0; ?>
                                                                @foreach(DB::table('m_historymovement')->select('SubAgent')->distinct()->get() as $agent)
                                                                @if($agent->SubAgent != '')
                                                                <option value="{{$agent->SubAgent}}" <?php
                                                                if ($counter == 1) {
                                                                    echo 'selected=""';
                                                                } $counter++;
                                                                ?>>
                                                                    {{$agent->SubAgent}}
                                                                </option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Quartal: 
                                                            <select data-placeholder="Choose a destination..." class="chosen-select"  style="width: 100%" id="sim2_quartal">
                                                                <option value="1" selected="">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row margtop20 margbot20">
                                                        <div class="col-sm-12"><button type="button" class="button btn-wide wide-h" id="btn_ceksn" style="background-color: #424242; color: white;">Put on Table</button></div>
                                                        <button type="button" onclick="exportExcel(this)" data-id='1' data-nama='sim2'><span class="glyphicon glyphicon-export"></span></button> Export excel
                                                        <div class="loader" id="loading-animation1" style="display:none;"></div>
                                                    </div>

                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="row">
                                                <div class="white-pane__bordered margbot20">
                                                    <h4>SubAgent SIM card Reporting</h4>
                                                    <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr id="sim2_table_container">
                                                                <th>SubAgent</th>
                                                                <th>January Shipout</th>
                                                                <th>January Active</th>
                                                                <th>January APF Return</th>
                                                                <th>February Shipout</th>
                                                                <th>February Active</th>
                                                                <th>February APF Return</th>
                                                                <th>March Shipout</th>
                                                                <th>March Active</th>
                                                                <th>March APF Return</th>
                                                                <!--<th>Actions</th>-->
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane active" id="excel_shipout_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class='row margbot20'>
                                                        <div class="col-md-6">
                                                            Year:
                                                            <select style="width: 100%" id="shipout_year" class="chosen-select">
                                                                @foreach(DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year)
                                                                @if($year->year >0)
                                                                <option value="{{$year->year}}">{{$year->year}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <!--                                                        <div class="col-md-6">
                                                                                                                    Type:
                                                                                                                    <select style="width: 100%" id="shipout_type" class="chosen-select">
                                                                                                                        <option value='SIM Card'>SIM Card</option>
                                                                                                                        <option value='Voucher'>Voucher</option>
                                                                                                                    </select>
                                                                                                                </div>-->
                                                        <div class="col-md-6">
                                                            Channel:
                                                            <select style="width: 100%" id="shipout_channel" class="chosen-select">
                                                                @foreach(DB::table('m_historymovement')->select(DB::raw(" DISTINCT SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'channel'"))->where('Status', 2)->get() as $channel)
                                                                <option value="{{$channel->channel}}">{{$channel->channel}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row margtop20 margbot20">
                                                        <div class="col-sm-6"><button type="button" class="button btn-wide wide-h" id="btn_set_table" style="background-color: #424242; color: white;">Set</button></div>
                                                        <button type="button" onclick="exportExcel(this)" data-id='2' data-nama='shipout'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                        <div class="loader" id="loading-animation2" style="display:none;"></div>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="row margbot20">
                                                <div class="white-pane__bordered margbot20">
                                                    <div id="h4container"></div>
                                                    <div id="h5container"></div>
                                                    <table id="example" class="display table-rwd table-inventory table text-center" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>January</th>
                                                                <th>February</th>
                                                                <th>March</th>
                                                                <th>April</th>
                                                                <th>May</th>
                                                                <th>June</th>
                                                                <th>July</th>
                                                                <th>August</th>
                                                                <th>September</th>
                                                                <th>October</th>
                                                                <th>November</th>
                                                                <th>December</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="shipout_table_container">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_shipin_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class='row margbot20'>
                                                        <div class="col-md-6">
                                                            Year:
                                                            <select style="width: 100%" id="shipin_year" class="chosen-select">
                                                                @foreach(DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year)
                                                                @if($year->year >0)
                                                                <option value="{{$year->year}}">{{$year->year}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row margtop20 margbot20">
                                                        <div class="col-sm-6"><button type="button" class="button btn-wide wide-h" id="btn_set_table2" style="background-color: #424242; color: white;">Set</button></div>
                                                        <button type="button" onclick="exportExcel(this)" data-id='10' data-nama='shipin'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                        <div class="loader" id="loading-animation10" style="display:none;"></div>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="row margbot20">
                                                <div class="white-pane__bordered margbot20">
                                                    <div id="h4container2"></div>
                                                    <div id="h5container2"></div>
                                                    <table id="example" class="display table-rwd table-inventory table text-center" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>January</th>
                                                                <th>February</th>
                                                                <th>March</th>
                                                                <th>April</th>
                                                                <th>May</th>
                                                                <th>June</th>
                                                                <th>July</th>
                                                                <th>August</th>
                                                                <th>September</th>
                                                                <th>October</th>
                                                                <th>November</th>
                                                                <th>December</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="shipin_table_container">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_usage_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class='row margbot20'>
                                                        <div class="col-md-6">
                                                            Year:
                                                            <select style="width: 100%" id="usage_year" class="chosen-select">
                                                                @foreach(DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year)
                                                                @if($year->year >0)
                                                                <option value="{{$year->year}}">{{$year->year}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row margtop20 margbot20">
                                                        <div class="col-sm-6"><button type="button" class="button btn-wide wide-h" id="btn_set_table3" style="background-color: #424242; color: white;">Set</button></div>
                                                        <button type="button" onclick="exportExcel(this)" data-id='11' data-nama='usage'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                        <div class="loader" id="loading-animation11" style="display:none;"></div>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                            <div class="row margbot20">
                                                <div class="white-pane__bordered margbot20">
                                                    <div id="h4container3"></div>
                                                    <div id="h5container3"></div>
                                                    <table id="example" class="display table-rwd table-inventory table text-center" cellspacing="0" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>January</th>
                                                                <th>February</th>
                                                                <th>March</th>
                                                                <th>April</th>
                                                                <th>May</th>
                                                                <th>June</th>
                                                                <th>July</th>
                                                                <th>August</th>
                                                                <th>September</th>
                                                                <th>October</th>
                                                                <th>November</th>
                                                                <th>December</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="usage_table_container">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_subagent_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class='row margbot20'>
                                                        <div class="col-md-6">
                                                            Year:
                                                            <select style="width: 100%" id="subagent_year" class="chosen-select">
                                                                @foreach(DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year)
                                                                @if($year->year >0)
                                                                <option value="{{$year->year}}">{{$year->year}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row margtop20 margbot20">
                                                        <button type="button" onclick="exportExcel(this)" data-id='12' data-nama='subagent'><span class="glyphicon glyphicon-export"></span></button> Export excel
                                                        <div class="loader" id="loading-animation12" style="display:none;"></div>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_user_container">
                                            <div class="row">
                                                <!-- /.col -->
                                                <div class="info-box">
                                                    <div class="row margtop20">
                                                        <div class="col-xs-10">
                                                            Active Status: 
                                                            <select  style="width: 100%" id="filter_act">
                                                                <option value="1" selected="">All</option>
                                                                <option value="2">Active</option>
                                                                <option value="3">Non-Active</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Using v300: 
                                                            <select  style="width: 100%" id="filter_v300">
                                                                <option value="1" selected="">All</option>
                                                                <option value="2">Yes</option>
                                                                <option value="3">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Using v100 and v50: 
                                                            <select  style="width: 100%" id="filter_v100">
                                                                <option value="1" selected="">All</option>
                                                                <option value="2">Yes</option>
                                                                <option value="3">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-10">
                                                            Service Usage: 
                                                            <select style="width: 100%" id="filter_service">
                                                                <option value="1" selected="">All</option>
                                                                <option value="2">Use Service</option>s
                                                                <option value="3">No Service</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="margtop20" style="margin-left: 20px;">
                                                        <button type="button" onclick="resetFilter(this)" data-id='filter_'>Reset Filter</button> 
                                                        <button type="button" onclick="exportExcel(this)" data-id='8' data-nama='user'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                        <div class="loader" id="loading-animation8" style="display:none;"></div>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                                <!-- /.info-box -->
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_weekly_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="info-box">
                                                        <div class='row margbot20'>
                                                            Date:
                                                            <input type="date" class="input-stretch" id='weekly_year' name="eventDate" data-validation="required" required>
                                                        </div>
                                                        <div class="row margtop20 margbot20">
                                                            <button type="button" onclick="exportExcel(this)" data-id='3' data-nama='weekly'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                            <div class="loader" id="loading-animation3" style="display:none;"></div>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                    <!-- /.info-box -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="excel_sim1_container">
                                            <div class="row">

                                                <!-- /.col -->
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="info-box">
                                                        <div class='row margbot20'>
                                                            From:
                                                            <input type="date" class="input-stretch" id='sim1_from_year' name="eventDate" data-validation="required" required>
                                                        </div>
                                                        <div class='row margbot20'>
                                                            To:
                                                            <input type="date" class="input-stretch" id='sim1_to_year' name="eventDate" data-validation="required" required>
                                                        </div>
                                                        <div class="row margtop20 margbot20">
                                                            <button type="button" onclick="exportExcel2(this)" data-id='4' data-nama='sim1'><span class="glyphicon glyphicon-export"></span></button> Export list detail excel
                                                            <div class="loader" id="loading-animation4" style="display:none;"></div>
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
            <!--chart js-->
            <script>
                var getIVR = '<?php echo Route('getIVR') ?>';
                var getCHURN = '<?php echo Route('getCHURN') ?>';
                var getChannel = '<?php echo Route('getChannel') ?>';
                var getChannelChurn = '<?php echo Route('getChannelChurn') ?>';
                var getCHURN2 = '<?php echo Route('getCHURN2') ?>';
                var getSubsriber = '<?php echo Route('getSubsriber') ?>';
                var getProductive = '<?php echo Route('getProductive') ?>';
                var getSumService = '<?php echo Route('getSumService') ?>';
                var getPayload = '<?php echo Route('getPayload') ?>';
                var getPayloadPeruser = '<?php echo Route('getPayloadPerUser') ?>';
                var getInternetVsNon = '<?php echo Route('getInternetVsNon') ?>';
                var getVouchersTopUp = '<?php echo Route('getVouchersTopUp') ?>';
                var geteVouchersTopUp = '<?php echo Route('geteVouchersTopUp') ?>';
                var getVouchers300TopUp = '<?php echo Route('getVouchers300TopUp') ?>';
                var getMSISDNTopUp = '<?php echo Route('getMSISDNTopUp') ?>';
                var getChurnDetail = '<?php echo Route('getChurnDetail') ?>';
                var getSubsriberTopUp = '<?php echo Route('getSubsriberTopUp') ?>';
                var getShipoutSim = '<?php echo Route('getShipoutSim') ?>';
                var getShipoutVoc = '<?php echo Route('getShipoutVoc') ?>';
                var default_year = document.getElementById('ivr_year').value;
                var ivr_year = document.getElementById('ivr_year').value;
                var churn_year = document.getElementById('churn_year').value;
                var prod_year = document.getElementById('prod_year').value;
                var sum_year = document.getElementById('sum_year').value;
                var internet_payload_year = document.getElementById('payload_year').value;
                var payload_peruser_year = document.getElementById('payload_peruser_year').value;
                var vs_year = document.getElementById('vs_year').value;
                var channel_year = document.getElementById('channel_year').value;
                var channel_churn_year = document.getElementById('channel_churn_year').value;
                var voc_topup_year = document.getElementById('voc_topup_year').value;
                var voc_topup300_year = document.getElementById('voc_topup300_year').value;
                var evoc_topup_year = document.getElementById('evoc_topup_year').value;
                var subs_year = document.getElementById('subs_year').value;
                var detail_churn_year = document.getElementById('detail_churn_year').value;
                var churn2_year = document.getElementById('churn2_year').value;
                var subs2_year = document.getElementById('subs2_year').value;
                var shipout_sim_year = document.getElementById('shipout_sim_year').value;
                var shipout_voc_year = document.getElementById('shipout_voc_year').value;
                var unique_subs_year = document.getElementById('unique_subs_year').value;
                var colorNames = Object.keys(window.chartColors);
                var scroll = true;
                var excelbutton = false;
                $('.btn-save-data').on('click', function (e) {
                    $('.loading-animation-global').show();
                    scroll = false;
                    excelbutton = true;
                    refreshBarChart();
                });
                $('#ivr_year').on('change', function (e) {
                    ivr_year = document.getElementById('ivr_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#shipout_sim_year').on('change', function (e) {
                    shipout_sim_year = document.getElementById('shipout_sim_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#shipout_voc_year').on('change', function (e) {
                    shipout_voc_year = document.getElementById('shipout_voc_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#channel_year').on('change', function (e) {
                    channel_year = document.getElementById('channel_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#channel_churn_year').on('change', function (e) {
                    channel_churn_year = document.getElementById('channel_churn_year').value;
                    scroll = false;
                    refreshBarChart();
                });

                $('#churn_year').on('change', function (e) {
                    churn_year = document.getElementById('churn_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#subs2_year').on('change', function (e) {
                    subs2_year = document.getElementById('subs2_year').value;
                    scroll = false;
                    refreshBarChart();
                });

                $('#detail_churn_year').on('change', function (e) {
                    detail_churn_year = document.getElementById('detail_churn_year').value;
                    scroll = false;
                    refreshBarChart();
                });

                $('#churn2_year').on('change', function (e) {
                    churn2_year = document.getElementById('churn2_year').value;
                    scroll = false;
                    refreshBarChart();
                });

                $('#prod_year').on('change', function (e) {
                    prod_year = document.getElementById('prod_year').value;
                    scroll = false;
                    refreshBarChart();
                });

                $('#sum_year').on('change', function (e) {
                    sum_year = document.getElementById('sum_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#payload_year').on('change', function (e) {
                    internet_payload_year = document.getElementById('payload_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#payload_peruser_year').on('change', function (e) {
                    payload_peruser_year = document.getElementById('payload_peruser_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#vs_year').on('change', function (e) {
                    vs_year = document.getElementById('vs_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#voc_topup_year').on('change', function (e) {
                    voc_topup_year = document.getElementById('voc_topup_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#voc_topup300_year').on('change', function (e) {
                    voc_topup300_year = document.getElementById('voc_topup300_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#evoc_topup_year').on('change', function (e) {
                    evoc_topup_year = document.getElementById('evoc_topup_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#subs_year').on('change', function (e) {
                    subs_year = document.getElementById('subs_year').value;
                    scroll = false;
                    refreshBarChart();
                });
                $('#unique_subs_year').on('change', function (e) {
                    unique_subs_year = document.getElementById('unique_subs_year').value;
                    scroll = false;
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
                var barChartData11 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData12 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData13 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData14 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData15 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData16 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData17 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData18 = {
                    labels: MONTHS,
                    datasets: []
                };
                var barChartData19 = {
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
                        var str_write = 'Total';
                        chart.data.datasets.forEach(function (dataset, i) {
                            var meta = chart.getDatasetMeta(i);
                            var idx = i;
                            var counter_idx = 0;
                            if (!meta.hidden) {
                                meta.data.forEach(function (element, index) { //jalan ke kanan bukan ke atas
                                    // Draw the text in black, with the specified font
                                    ctx.fillStyle = 'rgb(0, 0, 0)';
                                    var fontSize = 12;
                                    var fontStyle = 'normal';
                                    var fontFamily = 'Helvetica Neue';
                                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                                    // Just naively convert to string for now
                                    if (meta.controller.chart.canvas.id == 'barChart_channel' || meta.controller.chart.canvas.id == 'barChart_channel_churn') {
//                                        console.log(dataset.data[index])
//                                        dataset.data[index].forEach(function (dataz){
//                                           console.log(dataz);
//                                        });
                                        if (idx % 2 == 0 && index == 0) {
                                            total = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                                        }
                                        if (idx % 2 == 1) {
                                            total[index] += dataset.data[index];
                                            var dataString = total[index].toString();
                                            var temp_arr = dataString.split('.');
                                            if (temp_arr.length == 2) {
                                                dataString = temp_arr[0].split(/(?=(?:...)*$)/);
                                                dataString = dataString.join(',');
                                                dataString += '.' + temp_arr[1];
                                            } else {
                                                dataString = dataString.split(/(?=(?:...)*$)/);
                                                dataString = dataString.join(',');
                                            }
                                            if (total[index] > 500)
                                                ctx.fillText('Total : ' + dataString, element._model.x, element._model.y - 7);
                                        }
                                        counter_idx++;
                                    }

                                    if ((meta.controller.chart.canvas.id == 'barChart_churn' || meta.controller.chart.canvas.id == 'barChart_detail_churn') && dataset.data[index] < 0) {
                                        total[index] -= dataset.data[index];
                                    } else {
                                        total[index] += dataset.data[index];
                                    }
                                    x_axis[index] = element._model.x;
                                    y_axis[index] = element._model.y - 7;

                                    var dataString = dataset.data[index].toString();
                                    var temp_arr = dataString.split('.');
                                    if (temp_arr.length == 2) {
                                        dataString = temp_arr[0].split(/(?=(?:...)*$)/);
                                        dataString = dataString.join(',');
                                        dataString += '.' + temp_arr[1];
                                    } else {
                                        dataString = dataString.split(/(?=(?:...)*$)/);
                                        dataString = dataString.join(',');
                                    }

                                    // Make sure alignment settings are correct
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    var canvas_height = ctx.canvas.clientHeight;
                                    var temp_base = element._model.base;
                                    if (element._model.base > 584)
                                        temp_base = 584;
                                    var padding = ((temp_base - element._model.y) / 2);
                                    var position = element.tooltipPosition();
                                    var y_height = element._yScale.height;
                                    //                        if (dataString.includes('-')) {
                                    //                            padding = padding * -1;
                                    //                        }
                                    //                        ctx.fillText(dataString, position.x, position.y +((canvas_height -position.y )/2)+ (fontSize / 2) + padding - (canvas_height - y_height));
                                    if (dataString != '0') {
                                        if (meta.controller.chart.canvas.id == 'barChart_voc_topup' || meta.controller.chart.canvas.id == 'barChart_channel' || meta.controller.chart.canvas.id == 'barChart_channel_churn' || meta.controller.chart.canvas.id == 'barChart_evoc_topup' || meta.controller.chart.canvas.id == 'barChart_unique_subs_topup' || meta.controller.chart.canvas.id == 'barChart_churn2') {
                                            if (dataset.data[index] > 300) {
                                                ctx.fillText(dataString, element._model.x, (element._model.y + padding));
                                            }
                                        } else {
                                            ctx.fillText(dataString, element._model.x, (element._model.y + padding));
                                        }
                                    }
                                });
                            }
                            if (meta.controller.chart.canvas.id == 'barChart_detail_churn') {
                                write = true;
                                str_write = 'Net';
                            }
                            if (meta.controller.chart.canvas.id == 'barChart_prod' || meta.controller.chart.canvas.id == 'barChart_shipout_voc' || meta.controller.chart.canvas.id == 'barChart_shipout_sim') {
                                write = true;
                                str_write = 'Total';
                            }
                            if (meta.controller.chart.canvas.id == 'barChart_ivr' || meta.controller.chart.canvas.id == 'barChart_subs' || meta.controller.chart.canvas.id == 'barChart_churn2') {
                                write = true;
                                str_write = 'Total';
                            }
                            if (meta.controller.chart.canvas.id == 'barChart_churn' || meta.controller.chart.canvas.id == 'barChart_detail_churn') {
                                write = true;
                                str_write = 'Activation';
                            }
                            if (meta.controller.chart.canvas.id == 'barChart_voc_topup' || meta.controller.chart.canvas.id == 'barChart_evoc_topup' || meta.controller.chart.canvas.id == 'barChart_voc300_topup') {
                                write = true;
                                str_write = 'Total';
                            }
                        });
                        if (write) {
                            for (var i = 0; i < 12; i++) {
                                if (total[i].toString() != '0') {
                                    var dataString = total[i].toString();
                                    var temp_arr = dataString.split('.');
                                    if (temp_arr.length == 2) {
                                        dataString = temp_arr[0].split(/(?=(?:...)*$)/);
                                        dataString = dataString.join(',');
                                        dataString += '.' + temp_arr[1];
                                    } else {
                                        dataString = dataString.split(/(?=(?:...)*$)/);
                                        dataString = dataString.join(',');
                                    }
                                    ctx.fillText(str_write + ': ' + dataString, x_axis[i], y_axis[i]);
                                }

                            }
                        }
                    }
                });

                Chart.defaults.global.responsive = true;
                Chart.defaults.global.maintainAspectRatio = true;

                window.onload = function () {
                    var ctx = document.getElementById("barChart_ivr").getContext("2d");
                    window.myBar = new Chart(ctx, {
                        type: 'bar',
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'IVR Purchased Internet'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Subscriber and Churn'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
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
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Total Service Usage'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        }
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Internet Usage'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        }
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Payload Per User'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false,
                                            stepSize: 1
                                        }
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
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
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Voucher Topup'
                            }, scales: {xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly eVoucher Topup'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Subscriber Topup'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        }
                                    }]
                            }
                        }
                    });

                    var ctx11 = document.getElementById("barChart_detail_churn").getContext("2d");
                    window.myBar11 = new Chart(ctx11, {
                        type: 'bar',
                        data: barChartData11,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'DetailChurn'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
                                    }]
                            }
                        }
                    });
                    var ctx12 = document.getElementById("barChart_churn2").getContext("2d");
                    window.myBar12 = new Chart(ctx12, {
                        type: 'bar',
                        data: barChartData12,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Churn'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
                                    }]
                            }
                        }
                    });
                    var ctx13 = document.getElementById("barChart_subs").getContext("2d");
                    window.myBar13 = new Chart(ctx13, {
                        type: 'bar',
                        data: barChartData13,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Subscriber'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
                                    }]
                            }
                        }
                    });

                    var ctx14 = document.getElementById("barChart_voc300_topup").getContext("2d");
                    window.myBar14 = new Chart(ctx14, {
                        type: 'bar',
                        data: barChartData14,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Voucher 300NT Topup'
                            }, scales: {xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
                                    }]
                            }
                        }
                    });

                    var ctx15 = document.getElementById("barChart_unique_subs_topup").getContext("2d");
                    window.myBar15 = new Chart(ctx15, {
                        type: 'bar',
                        data: barChartData15,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Unique Subscriber TopUp'
                            }, scales: {
                                xAxes: [{
                                        stacked: true,
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
                                    }]
                            }
                        }
                    });
                    var ctx16 = document.getElementById("barChart_channel").getContext("2d");
                    window.myBar16 = new Chart(ctx16, {
                        type: 'bar',
                        data: barChartData16,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Channel Report'
                            }, scales: {
                                xAxes: [{
                                        stacked: true,
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
                                    }]
                            }
                        }
                    });
                    var ctx17 = document.getElementById("barChart_channel_churn").getContext("2d");
                    window.myBar17 = new Chart(ctx17, {
                        type: 'bar',
                        data: barChartData17,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Channel Churn Report'
                            }, scales: {
                                xAxes: [{
                                        stacked: true,
                                        gridLines: {
                                            display: false
                                        }
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true,
                                    }]
                            }
                        }
                    });
                    
                    var ctx18 = document.getElementById("barChart_shipout_sim").getContext("2d");
                    window.myBar18 = new Chart(ctx18, {
                        type: 'bar',
                        data: barChartData18,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly SIM CARD Shipout'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
                                        stacked: true
                                    }]
                            }
                        }
                    });
                    
                    var ctx19 = document.getElementById("barChart_shipout_voc").getContext("2d");
                    window.myBar19 = new Chart(ctx19, {
                        type: 'bar',
                        data: barChartData19,
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
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString();
                                        var temp_arr = value.split('.');
                                        if (temp_arr.length == 2) {
                                            value = temp_arr[0].split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                            value += '.' + temp_arr[1];
                                        } else {
                                            value = value.toString();
                                            value = value.split(/(?=(?:...)*$)/);
                                            value = value.join(',');
                                        }
                                        return value;
                                    }
                                } // end callbacks:
                            },
                            title: {
                                display: true,
                                text: 'Monthly Voucher Shipout'
                            }, scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        stacked: true
                                    }],
                                yAxes: [{
                                        gridLines: {
                                            display: false
                                        }, ticks: {
                                            display: false
                                        },
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
                    var arg_type = '1';
                    if (excelbutton) {
                        arg_type = '2';
                    }
                    if (chartID == 'info_ivr_month') {
                        $.post(getIVR, {year: ivr_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData.datasets = [];
                            $.each(data, function (index, value) {
                                var colors = ["#0000FF", "#FF0000", "#00FF00", "#f2b6b6", "#dff0d9", "#dff0d8"];
                                var colorName = colorNames[barChartData.datasets.length % colorNames.length];
                                //                                var dsColor = window.chartColors[colorName];
                                var dsColor = colors[barChartData.datasets.length % colorNames.length];
                                barChartData.datasets.push({
                                    label: index,
                                    fill: false,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar.update();
                            document.getElementById('legend').innerHTML = myBar.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#ivr_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_churn_month') {

                        $.post(getCHURN, {year: churn_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData2.datasets = [];
                            $.each(data, function (index, value) {
                                $.each(value, function (index2, value2) {
                                    var colors = ["#f2b6b6", "#dff0d9"];
                                    var colorName = colorNames[barChartData2.datasets.length % colorNames.length];
                                    var dsColor = window.chartColors[colorName];
                                    barChartData2.datasets.push({
                                        label: index2,
                                        backgroundColor: colors[barChartData2.datasets.length % colorNames.length],
                                        borderColor: colors[barChartData2.datasets.length % colorNames.length],
                                        borderWidth: 1,
                                        data: value2
                                    });
                                });
                            });
                            window.myBar2.update();
                            document.getElementById('legend2').innerHTML = myBar2.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#churn_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_churn2') {

                        $.post(getCHURN2, {year: churn2_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData12.datasets = [];
                            $.each(data, function (index, value) {
                                var colors = ["#dff0d9", "#f2b6b6"];
                                var colorName = colorNames[barChartData12.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData12.datasets.push({
                                    label: index,
                                    backgroundColor: colors[barChartData12.datasets.length % colorNames.length],
                                    borderColor: colors[barChartData12.datasets.length % colorNames.length],
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar12.update();
                            document.getElementById('legend12').innerHTML = myBar12.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#churn2_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_subs_month') {

                        $.post(getSubsriber, {year: subs2_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData13.datasets = [];
                            $.each(data, function (index, value) {
                                var colors = ["#dff0d9", "#f2b6b6"];
                                var colorName = colorNames[barChartData13.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData13.datasets.push({
                                    label: index,
                                    backgroundColor: colors[barChartData13.datasets.length % colorNames.length],
                                    borderColor: colors[barChartData13.datasets.length % colorNames.length],
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar13.update();
                            document.getElementById('legend13').innerHTML = myBar13.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#subs2_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_prod_month') {
                        $.post(getProductive, {year: prod_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData3.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData3.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                if (index === 'no service')
                                    dsColor = '#000000';
                                barChartData3.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar3.update();
                            document.getElementById('legend3').innerHTML = myBar3.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#prod_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_sum_month') {
                        $.post(getSumService, {year: sum_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData4.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData4.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData4.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar4.update();
                            // Find the scale in the chart instance
                            //var axis = myBar4.scales.A;
                            //var max = axis.max;
                            //var min = axis.min;
                            //myBar4.options.scales.yAxes[1].ticks.min = min;
                            //myBar4.options.scales.yAxes[1].ticks.max = max;
                            //window.myBar4.update();
                            document.getElementById('legend4').innerHTML = myBar4.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#sum_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_internet_payloads') {
                        $.post(getPayload, {year: internet_payload_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData5.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData5.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData5.datasets.push({
                                    label: index,
                                    //yAxisID: 'A',
                                    backgroundColor: "#dff0d8",
                                    borderColor: "#dff0d9",
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar5.update();
                            document.getElementById('legend5').innerHTML = myBar5.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#internet_payload_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_internet_payloads_peruser') {
                        $.post(getPayloadPeruser, {year: payload_peruser_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData6.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData6.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData6.datasets.push({
                                    label: index,
                                    backgroundColor: "#dff0d8",
                                    borderColor: "#dff0d9",
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar6.update();
                            document.getElementById('legend6').innerHTML = myBar6.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#payload_peruser_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_internet_vs') {
                        $.post(getInternetVsNon, {year: vs_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData7.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData7.datasets.length % colorNames.length];
                                var colors = ["#f2b6b6", "#dff0d9"];
                                var dsColor = window.chartColors[colorName];
                                barChartData7.datasets.push({
                                    label: index,
                                    backgroundColor: colors[barChartData7.datasets.length % colorNames.length],
                                    borderColor: colors[barChartData7.datasets.length % colorNames.length],
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar7.update();
                            document.getElementById('legend7').innerHTML = myBar7.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#vs_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_voc_topup') {
                        $.post(getVouchersTopUp, {year: voc_topup_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData8.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData8.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData8.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            var tmp = barChartData8.datasets[3];
                            var tmp2 = barChartData8.datasets[2]
                            barChartData8.datasets[3] = barChartData8.datasets[4];
                            barChartData8.datasets[2] = barChartData8.datasets[1];
                            barChartData8.datasets[1] = tmp;
                            barChartData8.datasets[4] = tmp2;
                            window.myBar8.update();
                            document.getElementById('legend8').innerHTML = myBar8.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#voc_topup_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_unique_subs_topup') {
                        $.post(getSubsriberTopUp, {year: unique_subs_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData15.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData15.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData15.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar15.update();
                            document.getElementById('legend15').innerHTML = myBar15.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#unique_subs_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_voc_topup300') {
                        $.post(getVouchers300TopUp, {year: voc_topup300_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData14.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData14.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData14.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar14.update();
                            document.getElementById('legend14').innerHTML = myBar14.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#voc_topup300_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_evoc_topup') {
                        $.post(geteVouchersTopUp, {year: evoc_topup_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData9.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData9.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData9.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar9.update();
                            document.getElementById('legend9').innerHTML = myBar9.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#evoc_topup_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_subs_topup') {
                        $.post(getMSISDNTopUp, {year: subs_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData10.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData10.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData10.datasets.push({
                                    label: index,
                                    backgroundColor: "#dff0d8",
                                    borderColor: "#dff0d9",
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar10.update();
                            document.getElementById('legend10').innerHTML = myBar10.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#subs_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_detail_churn_month') {
                        $.post(getChurnDetail, {year: detail_churn_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData11.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData11.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                var colors = ["#f2b6b6", "#dff0d9"];
                                barChartData11.datasets.push({
                                    label: index,
                                    backgroundColor: colors[barChartData11.datasets.length % colorNames.length],
                                    borderColor: colors[barChartData11.datasets.length % colorNames.length],
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar11.update();
                            document.getElementById('legend11').innerHTML = myBar11.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_channel') {
                        $.post(getChannel, {year: channel_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData16.datasets = [];
                            var idx = 1;
                            var channel_name = "";
                            $.each(data, function (index, value) {
                                channel_name = index;
                                $.each(value, function (index2, value2) {
                                    var colorName = colorNames[barChartData16.datasets.length % colorNames.length];
                                    var colors = ["#db4141", "#f2c9c9", "#ce9a29", "#f7e8c8", "#f9f939", "#fcfcbd", "#77bf35", "#cff7aa",
                                        "#73e2ca", "#d5efe9", "#598ce0", "#d5dfef", "#ef4aec", "#f2d2f1"];
                                    var dsColor = window.chartColors[colorName];
                                    barChartData16.datasets.push({
                                        label: channel_name + " " + index2,
                                        stack: 'Stack ' + idx,
                                        backgroundColor: colors[barChartData16.datasets.length % colors.length],
                                        borderColor: colors[barChartData16.datasets.length % colors.length],
                                        borderWidth: 1,
                                        data: value2
                                    });
                                });
                                idx++;
                            });
                            window.myBar16.update();
                            document.getElementById('legend16').innerHTML = myBar16.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#channel_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    } else if (chartID == 'info_channel_churn') {
                        $.post(getChannelChurn, {year: channel_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData17.datasets = [];
                            var idx = 1;
                            var channel_name = "";
                            $.each(data, function (index, value) {
                                channel_name = index;
                                $.each(value, function (index2, value2) {
                                    var colorName = colorNames[barChartData17.datasets.length % colorNames.length];
                                    var colors = ["#db4141", "#f2c9c9", "#ce9a29", "#f7e8c8", "#f9f939", "#fcfcbd", "#77bf35", "#cff7aa",
                                        "#73e2ca", "#d5efe9", "#598ce0", "#d5dfef", "#ef4aec", "#f2d2f1"];
                                    var dsColor = window.chartColors[colorName];
                                    barChartData17.datasets.push({
                                        label: channel_name + " " + index2,
                                        stack: 'Stack ' + idx,
                                        backgroundColor: colors[barChartData17.datasets.length % colors.length],
                                        borderColor: colors[barChartData17.datasets.length % colors.length],
                                        borderWidth: 1,
                                        data: value2
                                    });
                                });
                                idx++;
                            });
                            window.myBar17.update();
                            document.getElementById('legend17').innerHTML = myBar17.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#channel_churn_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    }else if (chartID == 'info_shipout_sim') {
                        $.post(getShipoutSim, {year: shipout_sim_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData18.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData18.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData18.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar18.update();
                            // Find the scale in the chart instance
                            //var axis = myBar4.scales.A;
                            //var max = axis.max;
                            //var min = axis.min;
                            //myBar4.options.scales.yAxes[1].ticks.min = min;
                            //myBar4.options.scales.yAxes[1].ticks.max = max;
                            //window.myBar4.update();
                            document.getElementById('legend18').innerHTML = myBar18.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#shipout_sim_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    }else if (chartID == 'info_shipout_voc') {
                        $.post(getShipoutVoc, {year: shipout_voc_year, type: arg_type}, function (data) {

                        }).done(function (data) {
                            barChartData19.datasets = [];
                            $.each(data, function (index, value) {
                                var colorName = colorNames[barChartData19.datasets.length % colorNames.length];
                                var dsColor = window.chartColors[colorName];
                                barChartData19.datasets.push({
                                    label: index,
                                    backgroundColor: color(dsColor).alpha(0.5).rgbString(),
                                    borderColor: dsColor,
                                    borderWidth: 1,
                                    data: value
                                });
                            });
                            window.myBar19.update();
                            // Find the scale in the chart instance
                            //var axis = myBar4.scales.A;
                            //var max = axis.max;
                            //var min = axis.min;
                            //myBar4.options.scales.yAxes[1].ticks.min = min;
                            //myBar4.options.scales.yAxes[1].ticks.max = max;
                            //window.myBar4.update();
                            document.getElementById('legend19').innerHTML = myBar19.generateLegend();
                            if (scroll) {
                                window.scrollBy(0, 200);
                            } else {
                                scroll = true;
                            }
                            if (excelbutton) {
                                window.location.href = "<?php echo url() ?>" + '/public/data_chart.xlsx';
                                $("#shipout_voc_year").val(default_year);
                                excelbutton = false;
                            }
                        });
                    }
                    $('.loading-animation-global').hide();
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

            <!--excel js-->
            <script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
            <script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
            <script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
            <script>
                Date.prototype.toDateInputValue = (function () {
                    var local = new Date(this);
                    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                    return local.toJSON().slice(0, 10);
                });
                var exportExcelLink = '';
                var inventoryDataBackup = '';
                var table = '';
                var postDashboard = '<?php echo Route('postDashboard') ?>';
                var postShipoutDashboard = '<?php echo Route('postShipoutDashboard') ?>';
                var postShipinDashboard = '<?php echo Route('postShipinDashboard') ?>';
                var postUsageDashboard = '<?php echo Route('postUsageDashboard') ?>';


                var postUserResetFilter = '<?php echo Route('postUserResetFilter') ?>';
                var postUserFilterActive = '<?php echo Route('postUserFilterActive') ?>';
                var postUserFilterv300 = '<?php echo Route('postUserFilterv300') ?>';
                var postUserFilterv100 = '<?php echo Route('postUserFilterv100') ?>';
                var postUserFilterService = '<?php echo Route('postUserFilterService') ?>';

                $('#filter_act').on('change', function (e) {
                    var state = document.getElementById('filter_act').value;
                    $.post(postUserFilterActive, {argstate: state}, function (data) {

                    });
                });
                $('#filter_v300').on('change', function (e) {
                    var state = document.getElementById('filter_v300').value;
                    $.post(postUserFilterv300, {argstate: state}, function (data) {

                    });
                });
                $('#filter_v100').on('change', function (e) {
                    var state = document.getElementById('filter_v100').value;
                    $.post(postUserFilterv100, {argstate: state}, function (data) {

                    });
                });
                $('#filter_service').on('change', function (e) {
                    var state = document.getElementById('filter_service').value;
                    $.post(postUserFilterService, {argstate: state}, function (data) {

                    });
                });

                $('#shipout_year').on('change', function (e) {
                    var argyear = document.getElementById('shipout_year').value;
                    var table_container = document.getElementById('h4container');
                    table_container.innerHTML = '<h4>Shipout Reporting ' + argyear + '</h4>';
                });

                $('#shipout_channel').on('change', function (e) {
                    var argtype = document.getElementById('shipout_channel').value;
                    var table_container = document.getElementById('h5container');
                    table_container.innerHTML = '<h5>' + argtype + '</h5>';
                });
                $('#shipin_year').on('change', function (e) {
                    var argyear = document.getElementById('shipin_year').value;
                    var table_container = document.getElementById('h4container2');
                    table_container.innerHTML = '<h4>Shipin Reporting ' + argyear + '</h4>';
                });

                $('#shipin_channel').on('change', function (e) {
                    var argtype = document.getElementById('shipin_channel').value;
                    var table_container = document.getElementById('h5container2');
                    table_container.innerHTML = '<h5>' + argtype + '</h5>';
                });
                $('#usage_year').on('change', function (e) {
                    var argyear = document.getElementById('usage_year').value;
                    var table_container = document.getElementById('h4container3');
                    table_container.innerHTML = '<h4>Usage Reporting ' + argyear + '</h4>';
                });

                $('#usage_channel').on('change', function (e) {
                    var argtype = document.getElementById('usage_channel').value;
                    var table_container = document.getElementById('h5container3');
                    table_container.innerHTML = '<h5>' + argtype + '</h5>';
                });

                $('#btn_ceksn').on('click', function (e) {
                    var str = document.getElementById('sim2_quartal').value;
                    var argyear = document.getElementById('sim2_year').value;
                    var argwh = document.getElementById('sim2_wh').value;
                    var table_container = document.getElementById('sim2_table_container');
                    var month = ['', '', ''];
                    if (str == '') {
                        alert("Please enter valid quartal!")
                    } else {
                        if (str == '1') {
                            month = ['January', 'February', 'March'];
                        } else if (str == '2') {
                            month = ['April', 'May', 'June'];
                        } else if (str == '3') {
                            month = ['July', 'August', 'September'];
                        } else if (str == '4') {
                            month = ['October', 'November', 'December'];
                        }
                        var text_html = '<th>SubAgent</th>';
                        month.forEach(function myFunction(item) {
                            text_html += '<th>' + item + ' Shipout</th><th>';
                            text_html += item + ' Active</th><th>';
                            text_html += item + ' APF Return</th>';
                        });
                        table_container.innerHTML = text_html;
                        document.getElementById("loading-animation1").style.display = "block";
                        $.post(postDashboard, {qt: str, year: argyear, wh: argwh}, function (data) {

                        }).done(function () {
                            refreshTable();
                            document.getElementById("loading-animation1").style.display = "none";
                            window.scrollBy(0, 200);
                        });
                    }
                });
                $('#btn_set_table').on('click', function (e) {
                    var argyear = document.getElementById('shipout_year').value;
                    var argchannel = document.getElementById('shipout_channel').value;
                    //                    var argtype = document.getElementById('shipout_type').value;
                    var text_html = '';
                    var table_container = document.getElementById('shipout_table_container');
                    document.getElementById("loading-animation2").style.display = "block";
                    $.post(postShipoutDashboard, {year: argyear, channel: argchannel}, function (data) {

                    }).done(function (data) {
                        $.each(data, function (key, val) {
                            var total = 0;
                            var header = '';
                            text_html += "<tr>";
                            if (key == '1')
                                header = 'SIM 3G';
                            else if (key == '4')
                                header = 'SIM 4G';
                            else if (key.toUpperCase() == 'KR0250')
                                header = 'eV300';
                            else if (key.toUpperCase() == 'KR0150')
                                header = 'eV100';
                            else if (key.toUpperCase() == 'KR0450')
                                header = 'eV50';
                            else if (key.toUpperCase() == 'KR0350')
                                header = 'pV100';
                            else if (key.toUpperCase() == 'KR1850')
                                header = 'pV300';
                            text_html += "<td>" + header + "</td>";
                            val.forEach(function setPerData(item) {
                                text_html += "<td>" + new Intl.NumberFormat().format(item) + "</td>";
                                total += parseInt(item);
                            });
                            text_html += "<td>" + new Intl.NumberFormat().format(total) + "</td>";
                            text_html += "</tr>";
                            table_container.innerHTML = text_html;
                        });
                        document.getElementById("loading-animation2").style.display = "none";
                    });
                });
                $('#btn_set_table2').on('click', function (e) {
                    var argyear = document.getElementById('shipin_year').value;
                    //                    var argchannel = document.getElementById('shipin_channel').value;
                    //                    var argtype = document.getElementById('shipout_type').value;
                    var text_html = '';
                    var table_container = document.getElementById('shipin_table_container');
                    document.getElementById("loading-animation10").style.display = "block";
                    $.post(postShipinDashboard, {year: argyear}, function (data) {

                    }).done(function (data) {
                        $.each(data, function (key, val) {
                            var total = 0;
                            var header = '';
                            text_html += "<tr>";
                            if (key == '1')
                                header = 'SIM 3G';
                            else if (key == '4')
                                header = 'SIM 4G';
                            else if (key.toUpperCase() == 'KR0250')
                                header = 'eV300';
                            else if (key.toUpperCase() == 'KR0150')
                                header = 'eV100';
                            else if (key.toUpperCase() == 'KR0450')
                                header = 'eV50';
                            else if (key.toUpperCase() == 'KR0350')
                                header = 'pV100';
                            else if (key.toUpperCase() == 'KR1850')
                                header = 'pV300';
                            text_html += "<td>" + header + "</td>";
                            val.forEach(function setPerData(item) {
                                text_html += "<td>" + new Intl.NumberFormat().format(item) + "</td>";
                                total += parseInt(item);
                            });
                            text_html += "<td>" + new Intl.NumberFormat().format(total) + "</td>";
                            text_html += "</tr>";
                            table_container.innerHTML = text_html;
                        });
                        document.getElementById("loading-animation10").style.display = "none";
                    });
                });
                $('#btn_set_table3').on('click', function (e) {
                    var argyear = document.getElementById('usage_year').value;
                    //                    var argchannel = document.getElementById('shipin_channel').value;
                    //                    var argtype = document.getElementById('shipout_type').value;
                    var text_html = '';
                    var table_container = document.getElementById('usage_table_container');
                    document.getElementById("loading-animation11").style.display = "block";
                    $.post(postUsageDashboard, {year: argyear}, function (data) {

                    }).done(function (data) {
                        $.each(data, function (key, val) {
                            var total = 0;
                            var header = '';
                            text_html += "<tr>";
                            if (key == '1')
                                header = 'SIM 3G';
                            else if (key == '4')
                                header = 'SIM 4G';
                            else if (key.toUpperCase() == 'KR0250')
                                header = 'eV300';
                            else if (key.toUpperCase() == 'KR0150')
                                header = 'eV100';
                            else if (key.toUpperCase() == 'KR0450')
                                header = 'eV50';
                            else if (key.toUpperCase() == 'KR0350')
                                header = 'pV100';
                            else if (key.toUpperCase() == 'KR1850')
                                header = 'pV300';
                            text_html += "<td>" + header + "</td>";
                            val.forEach(function setPerData(item) {
                                text_html += "<td>" + new Intl.NumberFormat().format(item) + "</td>";
                                total += parseInt(item);
                            });
                            text_html += "<td>" + new Intl.NumberFormat().format(total) + "</td>";
                            text_html += "</tr>";
                            table_container.innerHTML = text_html;
                        });
                        document.getElementById("loading-animation11").style.display = "none";
                    });
                });
                var refreshTable = function () {
                    if ($.fn.dataTable.isDataTable('#example')) {
                        table.fnDestroy();
                    }
                    inventoryDataBackup = '<?php echo Route('inventoryDataBackupDashboard') ?>';
                    table = $('#example').dataTable({
                        "draw": 10,
                        "bDestroy": true,
                        "processing": true,
                        "serverSide": true,
                        "ajax": inventoryDataBackup
                    });
                };

                var resetFilter = function (elem) {
                    var used_filter = elem.dataset.id;
                    $("[id^='" + used_filter + "']").val("1");
                    $.post(postUserResetFilter, {}, function (data) {

                    });
                };
                var exportExcel = function (elem) {
                    var loading_number = elem.dataset.id;
                    var id_concate = elem.dataset.nama;
                    var wh = "";
                    var subagent = "";
                    var year = $("#" + id_concate + "_year").val();
                    if ($("#" + id_concate + "_subagent"))
                        subagent = $("#" + id_concate + "_subagent").val();
                    if ($("#" + id_concate + "_wh"))
                        wh = $("#" + id_concate + "_wh").val();
                    document.getElementById("loading-animation" + loading_number).style.display = "block";
                    if (id_concate == 'sim2')
                        exportExcelLink = '<?php echo Route('exportExcelDashboard') ?>';
                    else if (id_concate == 'shipout')
                        exportExcelLink = '<?php echo Route('exportExcelShipoutDashboard') ?>';
                    else if (id_concate == 'shipin')
                        exportExcelLink = '<?php echo Route('exportExcelShipinDashboard') ?>';
                    else if (id_concate == 'weekly')
                        exportExcelLink = '<?php echo Route('exportExcelWeeklyDashboard') ?>';
                    else if (id_concate == 'user')
                        exportExcelLink = '<?php echo Route('exportExcelUserDashboard') ?>';
                    else if (id_concate == 'usage')
                        exportExcelLink = '<?php echo Route('exportExcelUsageDashboard') ?>';
                    else if (id_concate == 'subagent')
                        exportExcelLink = '<?php echo Route('exportExcelSubAgentDashboard') ?>';

                    $.post(exportExcelLink, {argyear: year, argsubagent: subagent, argwh: wh}, function (data) {

                    }).done(function (data) {
                        document.getElementById("loading-animation" + loading_number).style.display = "none";
                        window.location.href = "<?php echo url() ?>" + '/public' + data;
                    });
                };
                var exportExcel2 = function (elem) {
                    var loading_number = elem.dataset.id;
                    var id_concate = elem.dataset.nama;
                    var argfrom_year = $('#sim1_from_year').val();
                    var argto_year = $('#sim1_to_year').val();

                    document.getElementById("loading-animation" + loading_number).style.display = "block";
                    exportExcelLink = '<?php echo Route('exportExcelSIM1Dashboard') ?>';

                    $.post(exportExcelLink, {from_year: argfrom_year, to_year: argto_year}, function (data) {

                    }).done(function (data) {
                        document.getElementById("loading-animation" + loading_number).style.display = "none";
                        window.location.href = "<?php echo url() ?>" + '/public' + data;
                    });
                };
                $(document).ready(function () {
                    var argyear = document.getElementById('shipout_year').value;
                    var table_container = document.getElementById('h4container');
                    table_container.innerHTML = '<h4>Shipout Reporting ' + argyear + '</h4>';

                    //                    var argtype = document.getElementById('shipout_type').value;
                    //                    var table_container2 = document.getElementById('h5container');
                    //                    table_container2.innerHTML = '<h5>' + argtype + '</h5>';
                    $('ul.excel-report li').click(function (e) {
                        $(".chosen-select").chosen("destroy");
                        $(".chosen-select").chosen({width: '100%'});
                        $(this).trigger("chosen:updated");
                    });
                    $(".chosen-select").chosen();
                    $('#weekly_year').val(new Date().toDateInputValue());
                    $('#sim1_from_year').val(new Date().toDateInputValue());
                    $('#sim1_to_year').val(new Date().toDateInputValue());
                });
            </script>
            @stop
