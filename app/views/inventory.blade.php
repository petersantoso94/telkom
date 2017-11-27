@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<div class="white-pane__bordered margbot20">
    <div class="row">
        <div class="col-xs-4">
            Inventory Type: 
            <select data-placeholder="Choose a form series number..." class="form-group-lg form-control" style="width: 100%" id="invtype">
                <option selected="" value="all">All</option>
                <option value="sim3">SIM 3G</option>
                <option value="sim4">SIM 4G</option>
                <option value="evoc">E-VOUCHER</option>
                <option value="pvoc">PH-VOUCHER</option>
            </select>
        </div>
        <div class="col-xs-4">
            Status: 
            <select data-placeholder="Choose a form series number..." class="form-group-lg form-control" style="width: 100%" id="invstatus">
                <option selected="" value="all">All</option>
                <option value="in">AVAILABLE</option>
                <option value="out">SHIPOUT</option>
                <option value="ret">RETURN</option>
                <option value="wh">WAREHOUSE</option>
                <option value="con">CONSIGNMENT</option>
            </select>
        </div>
        <div class="col-xs-4">
            Form Series: 
            <select data-placeholder="Choose a form series number..." class="chosen-select" style="width: 100%" name="seriesNumber" id="series">
                <option></option>
                @foreach(DB::table('m_historymovement')->select('ShipoutNumber')->distinct()->get() as $sn)
                @if($sn->ShipoutNumber != '')
                <option value="{{$sn->ShipoutNumber}}">
                    {{$sn->ShipoutNumber}}
                </option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-xs-3" style="padding-top: 10px;">
            <button type="button" onclick="exportExcel(this)"><span class="glyphicon glyphicon-export"></span></button> Export excel
        </div>
    </div>
</div>
<div class="white-pane__bordered margbot20">
    <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Type</th>
                <th>Last Status</th>
                <th>Shipout to</th>
                <th>Form Series</th>
                <th>Last Warehouse</th>
                <th>Date</th>
                <th>MSISDN</th>
                <!--<th>Actions</th>-->
            </tr>
        </thead>
    </table>
</div>
@stop

@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
                var table = '';
                var inventoryDataBackup = '<?php echo Route('inventoryDataBackup') ?>';
                var concat = '/all';
                var exportExcelLink = '<?php echo Route('exportExcel') ?>';
                var ajax1 = '<?php echo Route('getFS') ?>';
                var postFS = '<?php echo Route('postFormSeries') ?>';

                table = $('#example').dataTable({
                    "draw": 10,
                    "processing": true,
                    "bDestroy": true,
                    "serverSide": true,
                    "ajax": inventoryDataBackup + concat
                });

                var exportExcel = function () {
                    document.getElementById("loading-animation").style.display = "block";
                    exportExcelLink = '<?php echo Route('exportExcel') ?>' + concat;
                    console.log(exportExcelLink);
                    $.get(exportExcelLink, function (data) {

                    }).done(function () {
                        document.getElementById("loading-animation").style.display = "none";
                        window.location.href = '<?php echo url() . '/telkom_inventory.xls' ?>';
                    });
                };

                var drawTable = function () {
                    inventoryDataBackup = '<?php echo Route('inventoryDataBackup') ?>' + concat;
                    if ($.fn.dataTable.isDataTable('#example')) {
                        table.fnDestroy();
                    }
                    table = $('#example').dataTable({
                        "draw": 10,
                        "bDestroy": true,
                        "processing": true,
                        "serverSide": true,
                        "ajax": inventoryDataBackup
                    });
                };
                $('#series').on('change', function (e) {
                    var temp3 = document.getElementById('series').value;
                    if (!temp3)
                        temp3 = '';
                    $.post(postFS, {fs: temp3}, function (data) {

                    }).done(function () {
                        drawTable();
                    });
                });
                $('#invtype').on('change', function (e) {
                    var temp_type = $(this).val();
                    if (temp_type == 'all') {
                        $(".chosen-select").chosen("destroy");
                        $('#series').append('<option></option>');
                        if (typeof concat.split(',,,')[1] !== 'undefined') {
                            var temp = concat.split(',,,')[1];
                            concat = '/all,,,' + temp;
                        } else {
                            concat = '/all';
                        }
                        $('#series')
                                .find('option')
                                .remove();
                        $.get(ajax1, function (data) {
                            $("#series").append('<option></option>');
                            $.each(data, function (key, val) {
                                $("#series").append('<option value="' + val.ShipoutNumber + '">' + val.ShipoutNumber + '</option>');
                            });
                        }).done(function () {
                            $(".chosen-select").chosen()
                            $(this).trigger("chosen:updated");
                            drawTable();
                        });
                    } else if (temp_type == 'sim3') {
                        if (typeof concat.split(',,,')[1] !== 'undefined') {
                            var temp = concat.split(',,,')[1];
                            concat = '/1,,,' + temp;
                        } else {
                            concat = '/1';
                        }
                        drawTable();
                    } else if (temp_type == 'sim4') {
                        if (typeof concat.split(',,,')[1] !== 'undefined') {
                            var temp = concat.split(',,,')[1];
                            concat = '/4,,,' + temp;
                        } else {
                            concat = '/4';
                        }
                        drawTable();
                    } else if (temp_type == 'evoc') {
                        if (typeof concat.split(',,,')[1] !== 'undefined') {
                            var temp = concat.split(',,,')[1];
                            concat = '/2,,,' + temp;
                        } else {
                            concat = '/2';
                        }
                        drawTable();
                    } else if (temp_type == 'pvoc') {
                        if (typeof concat.split(',,,')[1] !== 'undefined') {
                            var temp = concat.split(',,,')[1];
                            concat = '/3,,,' + temp;
                        } else {
                            concat = '/3';
                        }
                        drawTable();
                    }
                });
                $('#invstatus').on('change', function (e) {
                    var temp_type = $(this).val();
                    if (temp_type == 'all') {
                        concat = concat.split(',,,')[0];
                        drawTable();
                    } else if (temp_type == 'in') {
                        concat = concat.split(',,,')[0];
                        concat += ',,,0';
                        drawTable();
                    } else if (temp_type == 'out') {
                        concat = concat.split(',,,')[0];
                        concat += ',,,2';
                        drawTable();
                    } else if (temp_type == 'ret') {
                        concat = concat.split(',,,')[0];
                        concat += ',,,1';
                        drawTable();
                    } else if (temp_type == 'wh') {
                        concat = concat.split(',,,')[0];
                        concat += ',,,3';
                        drawTable();
                    } else if (temp_type == 'con') {
                        concat = concat.split(',,,')[0];
                        concat += ',,,4';
                        drawTable();
                    }
                });
                $(document).ready(function () {
                    $(".chosen-select").chosen();
                });
</script>
@stop
