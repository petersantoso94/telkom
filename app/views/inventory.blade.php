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
            <input type="radio" id="siminv" name="filtertype" value="sim"><label for="filtertype">Sim Card</label>
        </div>
        <div class="col-xs-2">
            <input type="radio" id="vocinv" name="filtertype" value="voc"><label for="filtertype">Voucher</label>
        </div>
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
    </div>
</div>
<div class="white-pane__bordered margbot20">
    <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Type</th>
                <th>Last Status</th>
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
<script>
var table = '';
var inventoryDataBackup = '<?php echo Route('inventoryDataBackup') ?>';
var concat = '/all';

table = $('#example').dataTable({
    "draw": 10,
    "processing": true,
    "bDestroy": true,
    "serverSide": true,
    "ajax": inventoryDataBackup + concat
});

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
$('#siminv').on('click', function (e) {
    if (typeof concat.split(',,,')[1] !== 'undefined') {
        var temp = concat.split(',,,')[1];
        concat = '/1,,,' +temp;
    } else {
        concat = '/1';
    }
    drawTable();
});
$('#vocinv').on('click', function (e) {
    if (typeof concat.split(',,,')[1] !== 'undefined') {
        var temp = concat.split(',,,')[1];
        concat = '/2,,,' +temp;
    } else {
        concat = '/2';
    }
    drawTable();
});
$('#allinv').on('click', function (e) {
    if (typeof concat.split(',,,')[1] !== 'undefined') {
        var temp = concat.split(',,,')[1];
        concat = '/all,,,' +temp;
    } else {
        concat = '/all';
    }
    drawTable();
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
$('#allinvstat').on('click', function (e) {
    concat = concat.split(',,,')[0];
    drawTable();
});
</script>
@stop
