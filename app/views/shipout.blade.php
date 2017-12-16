@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<form method="POST" action="{{route('showInventoryShipout')}}" accept-charset="UTF-8" enctype="multipart/form-data">
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <div class="col-xs-12">
                <h4>Input Shipping Out Inventory</h4>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">MSISDN: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" id='msi'>
                </div>
                <div class="col-sm-2"><button type="button" class="button btn-wide wide-h" id="btn_cekmsi" style="background-color: #424242; color: white;">Check</button></div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">First serial number: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" id='shipoutstart' name='shipoutstart' data-validation="required" required>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Last serial number: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" id='shipoutend' name='shipoutend' data-validation="required" required>
                </div>
                <div class="col-sm-2"><button type="button" class="button btn-wide wide-h" id="btn_ceksn" style="background-color: #424242; color: white;">Check</button></div>
            </div>
        </div>
        <div class="row">
            <?php if (isset($number)) { ?>
                <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Successfully updating {{$number}} data.
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="white-pane__bordered margbot20 alert-success" style="background: #dff0d8;">
        <h4>Available inventory</h4>
        <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Last Status</th>
                    <th>Last Warehouse</th>
                    <th>Date</th>
                    <th>MSISDN</th>
                    <th>Action</th>
                    <!--<th>Actions</th>-->
                </tr>
            </thead>
        </table>
    </div>
    <div class="white-pane__bordered margbot20 alert-warning">
        <h4>Missing Inventory: </h4>
        <table id="example3" class="display table-rwd table-inventory" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Last Status</th>
                    <th>Last Warehouse</th>
                    <th>Date</th>
                    <th>MSISDN</th>
                    <th>Action</th>
                    <!--<th>Actions</th>-->
                </tr>
            </thead>
        </table>
    </div>
    <div class="white-pane__bordered margbot20 alert-danger">
        <h4>Inventory that had been ship out(ed): </h4>
        <table id="example2" class="display table-rwd table-inventory" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Last Status</th>
                    <th>Last Warehouse</th>
                    <th>Date</th>
                    <th>MSISDN</th>
                    <th>Action</th>
                    <!--<th>Actions</th>-->
                </tr>
            </thead>
        </table>
    </div>
    <div class="white-pane__bordered margbot20">
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Shippout Date: </label>
                </div>
                <div class="col-sm-5">
                    <input type="date" class="input-stretch" id='shipindate' name="eventDate" data-validation="required" required>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Shipout to: </label>
                </div>
                <div class="col-sm-5" style="margin-top: 5px;">
                    <select data-placeholder="Choose a destination..." class="chosen-select" style="width: 100%" name="shipout" id="shipoutto">
                        <option></option>
                        <option value="TOKO">TOKO</option>
                        <option value="ASPROF">ASPROF</option>
                        <option value="ASPROT">ASPROT</option>
                        <option value="DIRECT">DIRECT</option>
                        <option value="INDEX">INDEX</option>
                        <option value="PRE-EMPTIVE">PRE-EMPTIVE</option>
                        <option value="COLUMBIA">COLUMBIA</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Subagent: </label>
                </div>
                <div class="col-sm-4" style="margin-top: 5px;">
                    <select data-placeholder="Choose a subagent..." class="chosen-select2" style="width: 100%" name="subagent" id="subagent">
                        <option></option>
                        @foreach(DB::table('m_historymovement')->select('SubAgent')->distinct()->get() as $agent)
                        @if($agent->SubAgent != '')
                        <option value="{{$agent->SubAgent}}">
                            {{$agent->SubAgent}}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1" style="margin-top: 5px;">
                    <button type="button" onclick="newSubagent(this)"><span class="glyphicon glyphicon-plus"></span></button>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Form Series Number: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" id="formSN" class="input-stretch" name="formSN">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Shipout Price (NT$): </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="price" id="soprice" value="0">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Remark: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="remark">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-6">
                <input type="submit" class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">
            </div>
            <div class="col-xs-1">
                <button type="button" onclick="printPrev(this)"><span class="glyphicon glyphicon-print"></span></button>
            </div>
        </div>
    </div>
</form>
@stop
@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
                    Date.prototype.toDateInputValue = (function () {
                        var local = new Date(this);
                        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                        return local.toJSON().slice(0, 10);
                    });
                    var table = '';
                    var table2 = '';
                    var table3 = '';
                    var inventoryDataBackup = '';
                    var inventoryDataBackup2 = '';
                    var inventoryDataBackup3 = '';
                    var getSN = '';
                    var getForm = '';
                    var notin = '';
                    var postMissing = '<?php echo Route('postMissing') ?>';
                    var postNewAgent = '<?php echo Route('postNewAgent') ?>';
                    var postAvail = '<?php echo Route('postAvail') ?>';
                    var ajax1 = '<?php echo Route('getSubAgent') ?>';
                    var getPDF = '<?php echo Route('getPDFShipout') ?>';
                    var newAgentName = '';

                    window.deleteAttach = function (element) {
                        notin = $(element).data('internal');
                        if (confirm("Do you want to exclude this inventory (" + notin + ") ?") == true) {
                            $.post(postMissing, {sn: notin}, function (data) {

                            }).done(function () {
                                refreshTable();
                            });
                        }
                    };

                    window.availAttach = function (element) {
                        notin = $(element).data('internal');
                        if (confirm("Do you want to include this inventory (" + notin + ") ?") == true) {
                            $.post(postAvail, {sn: notin}, function (data) {

                            }).done(function () {
                                refreshTable();
                            });
                        }
                    };

                    window.printPrev = function (element) {
                        var shipout_date = document.getElementById('shipindate').value;
                        var shipout_SN = document.getElementById('formSN').value;
                        var shipout_to = document.getElementById('shipoutto').value;
                        var shipout_subagent = document.getElementById('subagent').value;
                        var shipout_start = document.getElementById('shipoutstart').value;
                        var shipout_end = document.getElementById('shipoutend').value;
                        var soprice = document.getElementById('soprice').value;
                        $.post(getPDF,
                                {date: shipout_date, sn: shipout_SN, to: shipout_to, subagent: shipout_subagent
                                    , start: shipout_start, end: shipout_end, price: soprice}
                        , function (data) {

                        }).done(function () {
                            window.open(getPDF);
                        });
                    };

                    window.newSubagent = function (element) {
                        if ($('#shipoutto').val() != '') {
                            var person = prompt("Please enter New Subagent name:","please insert sub-agent name only..");
                            if (person == null || person == "") {
                                txt = "User cancelled the prompt.";
                            } else {
                                newAgentName = $('#shipoutto').val()+" "+person;
                                confirmNewAgent();
                            }
                        }else{
                            alert("Please choose Shipoutto first, Thank you!");
                        }
                    };

                    var confirmNewAgent = function () {
                        if (confirm("Do you want to safe this New Subagent: '" + newAgentName + "' ?") == true) {
                            $.post(postNewAgent, {agent: newAgentName}, function (data) {

                            }).done(function () {
                                $(".chosen-select2").chosen("destroy");
                                $("#subagent").append('<option value="' + newAgentName + '">' + newAgentName + '</option>');
                                $(".chosen-select2").chosen()
                                $(this).trigger("chosen:updated");
                            });
                        }
                    };

                    $('#btn_cekmsi').on('click', function (e) {
                        getSN = '<?php echo Route('getSN') ?>' + '/' + document.getElementById('msi').value;
                        $.get(getSN, function (data) {
                            $('#shipoutstart').val(data);
                        });
                    });

                    var refreshTable = function () {
                        if ($.fn.dataTable.isDataTable('#example')) {
                            table.fnDestroy();
                            table2.fnDestroy();
                            table3.fnDestroy();
                        }
                        inventoryDataBackup = '<?php echo Route('inventoryDataBackupOut') ?>' + '/' + document.getElementById('shipoutstart').value + ',,,' + document.getElementById('shipoutend').value + ',,,1';
                        inventoryDataBackup2 = '<?php echo Route('inventoryDataBackupOut') ?>' + '/' + document.getElementById('shipoutstart').value + ',,,' + document.getElementById('shipoutend').value + ',,,0';
                        inventoryDataBackup3 = '<?php echo Route('inventoryDataBackupOut') ?>' + '/' + document.getElementById('shipoutstart').value + ',,,' + document.getElementById('shipoutend').value + ',,,2';
                        table = $('#example').dataTable({
                            "draw": 10,
                            "bDestroy": true,
                            "processing": true,
                            "serverSide": true,
                            "ajax": inventoryDataBackup
                        });
                        table2 = $('#example2').dataTable({
                            "draw": 10,
                            "bDestroy": true,
                            "processing": true,
                            "serverSide": true,
                            "ajax": inventoryDataBackup2
                        });
                        table3 = $('#example3').dataTable({
                            "draw": 10,
                            "bDestroy": true,
                            "processing": true,
                            "serverSide": true,
                            "ajax": inventoryDataBackup3
                        });
                    };

                    $('#btn_ceksn').on('click', function (e) {
                        refreshTable();
                    });

                    $('#shipoutto').on('change', function (e) {
                        $(".chosen-select2").chosen("destroy");
                        $('#subagent')
                                .find('option')
                                .remove();
                        var shipto = document.getElementById('shipoutto').value;
                        $.post(ajax1, {ship: shipto}, function (data) {
                            $.each(data, function (key, val) {
                                $("#subagent").append('<option value="' + val.SubAgent + '">' + val.SubAgent + '</option>');
                            });
                        }).done(function () {
                            $(".chosen-select2").chosen()
                            $(this).trigger("chosen:updated");
                            refreshFormSN();
                        });
                    });
                    $('#subagent').on('change', function (e) {
                        refreshFormSN();
                    });

                    var refreshFormSN = function () {
                        var shipoutto = '';
                        var stringtamp = '';
                        if ($('#shipoutto').val() != '') {
                            shipoutto = $('#shipoutto').val();
                            if (shipoutto.includes('ASPROF'))
                                shipoutto = 'ASF';
                            else if (shipoutto.includes('ASPROT'))
                                shipoutto = 'AST';
                            else
                                shipoutto = shipoutto.substring(0, 3).toUpperCase();
                        }
                        if ($('#subagent').val() != '') {
                            shipoutto = $('#subagent').val().split(' ')[0];
                            if (shipoutto.includes('ASPROF'))
                                shipoutto = 'ASF';
                            else if (shipoutto.includes('ASPROT'))
                                shipoutto = 'AST';
                            else
                                shipoutto = shipoutto.substring(0, 3).toUpperCase();
                        }
                        stringtamp = $('#shipindate').val() + '/SO/' + shipoutto;
                        getForm = '<?php echo Route('getForm') ?>';
                        $.post(getForm, {sn: stringtamp}, function (data) {
                            stringtamp += data;
                        }).done(function () {
                            $('#formSN').val(stringtamp);
                        });
                    };

                    $('#shipindate').on('change', function (e) {
                        refreshFormSN();
                    });
                    $("#newsub").on("change keyup paste", function () {
                        refreshFormSN();
                    })

                    $(document).ready(function () {
                        $('#shipindate').val(new Date().toDateInputValue());
                        $('#formSN').val(new Date().toDateInputValue());
                        $(".chosen-select").chosen()
                        $(".chosen-select2").chosen()
                    });
</script>
@stop
