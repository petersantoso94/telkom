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
        <h4>Inventory type:</h4>
    </div>
    <div class="row">
        <div class="col-xs-1">
            <input type="radio" id="allinv" name="filtertype" value="all" checked><label for="filtertype">All</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="siminv" name="filtertype" value="sim"><label for="filtertype">Sim 3G</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="4siminv" name="filtertype" value="sim"><label for="filtertype">Sim 4G</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="vocinv" name="filtertype" value="voc"><label for="filtertype">e-Voucher</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="pvocinv" name="filtertype" value="voc"><label for="filtertype">ph-Voucher</label>
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
        <div class="col-xs-3">
            <button type="button" onclick="exportExcel(this)"><span class="glyphicon glyphicon-save"></span></button>Export excel
        </div>
        <div class="loader" id="loading-animation" style="display:none;"></div>
    </div>
    <div class="row">
        <h4>Last Status:</h4>
    </div>
    <div class="row">
        <div class="col-xs-1">
            <input type="radio" id="allinvstat" name="filterstat" value="all" checked><label for="filterstat">All</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="shipinstat" name="filterstat" value="sim"><label for="filterstat">Available</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="shipoutstat" name="filterstat" value="voc"><label for="filterstat">Shipout</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="retstat" name="filterstat" value="voc"><label for="filterstat">Return</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="whstat" name="filterstat" value="voc"><label for="filterstat">Warehouse</label>
        </div>
        <div class="col-xs-3">
            <input type="radio" id="consstat" name="filterstat" value="voc"><label for="filterstat">Consignment</label>
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
                    exportExcelLink = '<?php echo Route('exportExcel') ?>'+concat;
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
                $('#siminv').on('click', function (e) {
                    if (typeof concat.split(',,,')[1] !== 'undefined') {
                        var temp = concat.split(',,,')[1];
                        concat = '/1,,,' + temp;
                    } else {
                        concat = '/1';
                    }
                    drawTable();
                });
                $('#4siminv').on('click', function (e) {
                    if (typeof concat.split(',,,')[1] !== 'undefined') {
                        var temp = concat.split(',,,')[1];
                        concat = '/4,,,' + temp;
                    } else {
                        concat = '/4';
                    }
                    drawTable();
                });
                $('#vocinv').on('click', function (e) {
                    if (typeof concat.split(',,,')[1] !== 'undefined') {
                        var temp = concat.split(',,,')[1];
                        concat = '/2,,,' + temp;
                    } else {
                        concat = '/2';
                    }
                    drawTable();
                });
                $('#pvocinv').on('click', function (e) {
                    if (typeof concat.split(',,,')[1] !== 'undefined') {
                        var temp = concat.split(',,,')[1];
                        concat = '/3,,,' + temp;
                    } else {
                        concat = '/3';
                    }
                    drawTable();
                });
                $('#allinv').on('click', function (e) {
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
                });
                $('#shipinstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    concat += ',,,0';
                    drawTable();
                });
                $('#shipoutstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    concat += ',,,2';
                    drawTable();
                });
                $('#retstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    concat += ',,,1';
                    drawTable();
                });
                $('#whstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    concat += ',,,3';
                    drawTable();
                });
                $('#consstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    concat += ',,,4';
                    drawTable();
                });
                $('#allinvstat').on('click', function (e) {
                    concat = concat.split(',,,')[0];
                    drawTable();
                });
                $(document).ready(function () {
                    $(".chosen-select").chosen();
                });
</script>
@stop
