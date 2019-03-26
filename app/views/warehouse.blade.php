@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <div class="col-xs-12">
                <h4>Change Warehouse Inventory</h4>
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
    <div class="white-pane__bordered margbot20">
        <h4>Available inventory to move:</h4>
        <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Last Status</th>
                    <th>Date</th>
                    <th>MSISDN</th>
                    <!--<th>Actions</th>-->
                </tr>
            </thead>
        </table>
    </div>
    <div class="white-pane__bordered margbot20">
        <h4>Inventory that's not available to move: </h4>
        <table id="example2" class="display table-rwd table-inventory" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Type</th>
                    <th>Last Status</th>
                    <th>Date</th>
                    <th>MSISDN</th>
                    <!--<th>Actions</th>-->
                </tr>
            </thead>
        </table>
    </div>
    <div class="white-pane__bordered margbot20">
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Move to: </label>
                </div>
                <div class="col-sm-2" style="margin-top: 5px;">
                    <select class="chosen-select" style="" name="moveto" id="warehouse-id">
                        @foreach(DB::table('m_historymovement')->select('Warehouse')->distinct()->get() as $agent)
                        @if($agent->Warehouse != '')
                        <option value="{{$agent->Warehouse}}">
                            {{$agent->Warehouse}}
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
                    <label class="fw300" style="margin-top: 7px;">Remark: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="remark">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Move Date: </label>
                </div>
                <div class="col-sm-5">
                    <input type="date" class="input-stretch" id='shipindate' name="eventDate" data-validation="required" required>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-8">
                <input type="submit" class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">
            </div>
        </div>
    </div>
</form>
@stop
@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/chosen.jquery.min.js')}}"></script>
<script>
                        Date.prototype.toDateInputValue = (function () {
                            var local = new Date(this);
                            local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                            return local.toJSON().slice(0, 10);
                        });
                        var table = '';
                        var table2 = '';
                        var newWhName = '';
                        var inventoryDataBackup = '';
                        var inventoryDataBackup2 = '';
                        var postNewWh = '<?php echo Route('postNewWh') ?>';
                        $('#btn_ceksn').on('click', function (e) {
                            if ($.fn.dataTable.isDataTable('#example')) {
                                table.fnDestroy();
                                table2.fnDestroy();
                            }
                            inventoryDataBackup = '<?php echo Route('inventoryDataBackupWare') ?>' + '/' + document.getElementById('shipoutstart').value + ',,,' + document.getElementById('shipoutend').value + ',,,1';
                            inventoryDataBackup2 = '<?php echo Route('inventoryDataBackupWare') ?>' + '/' + document.getElementById('shipoutstart').value + ',,,' + document.getElementById('shipoutend').value + ',,,0';
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
                        });
                        $(document).ready(function () {
                            $('#shipindate').val(new Date().toDateInputValue());
                            $(".chosen-select").chosen()
                        });
                        window.newSubagent = function (element) {
                            var person = prompt("Please enter New Warehouse name:", "please insert warehouse name");
                            if (person == null || person == "") {
                                txt = "User cancelled the prompt.";
                            } else {
                                newWhName = person;
                                confirmNewAgent();
                            }
                        };
                        var confirmNewAgent = function () {
                            if (confirm("Do you want to safe this New Warehouse: '" + newWhName + "' ?") == true) {
                                $.post(postNewWh, {wh: newWhName}, function (data) {

                                }).done(function () {
                                    $(".chosen-select").chosen("destroy");
                                    $("#warehouse-id").append('<option value="' + newWhName + '">' + newWhName + '</option>');
                                    $(".chosen-select").chosen()
                                    $(this).trigger("chosen:updated");
                                });
                            }
                        };
</script>
@stop
