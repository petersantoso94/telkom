@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<div class="white-pane__bordered margbot20">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#uncat" data-toggle="tab" aria-expanded="true">Uncatagorized Inventory</a></li>
                <li class=""><a href="#sip" data-toggle="tab" aria-expanded="true">Anomalies SIP</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="uncat">
                    <div class="row">
                        <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>MSISDN</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                <!--<th>Actions</th>-->
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="sip">
                    <div class="row">
                        <table id="example2" class="display table-rwd table-inventory" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>MSISDN</th>
                                    <th>Remark</th>
                                    <!--<th>Action</th>-->
                                <!--<th>Actions</th>-->
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
var table = '';
var table2 = '';
var inventoryDataBackupUncat = '<?php echo Route('inventoryDataBackupUncat') ?>';
var inventoryDataBackupAnomalies = '<?php echo Route('inventoryDataBackupAnomalies') ?>';
var postShipin = '<?php echo Route('postShipin') ?>';
var postRemark = '<?php echo Route('postRemark') ?>';
var newRemark = '';

window.goShipin = function (element) {
    notin = $(element).data('internal');
    msi = $(element).data('msisdn');
    if (confirm("Do you want to shipin this inventory (" + notin + ")?") == true) {
        $.post(postShipin, {sn: notin, msisdn: msi}, function (data) {

        }).done(function () {
            drawTable();
        });
    }
};
window.editRemark = function (element) {
    notin = $(element).data('internal');
    var person = prompt("Please enter New remark:", "please insert remark..");
    if (person == null || person == "") {
        txt = "User cancelled the prompt.";
    } else {
        newRemark = person;
        confirmNewRemark();
    }
}
var confirmNewRemark = function () {
    if (confirm("Do you want to update remark in this inventory (" + notin + ")?") == true) {
        $.post(postRemark, {sn: notin, new_remark: newRemark}, function (data) {

        }).done(function () {
            drawTable();
        });
    }
};
table = $('#example').dataTable({
    "draw": 10,
    "processing": true,
    "bDestroy": true,
    "serverSide": true,
    "ajax": inventoryDataBackupUncat
});
table2 = $('#example2').dataTable({
    "draw": 10,
    "processing": true,
    "bDestroy": true,
    "serverSide": true,
    "ajax": inventoryDataBackupAnomalies
});

var drawTable = function () {
    if ($.fn.dataTable.isDataTable('#example')) {
        table.fnDestroy();
    }
    table = $('#example').dataTable({
        "draw": 10,
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "ajax": inventoryDataBackupUncat
    });
};

</script>
@stop
