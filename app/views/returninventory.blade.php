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
        <div class="col-xs-12">
            <h4>Input Return Inventory</h4>
        </div>
    </div>
    <div class="row">
        <?php if (isset($number)) { ?>
            <?php if ($number > 0) { ?>
                <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Successfully inserting {{$number}} data.
                    <?php if (isset($succ)) { ?>
                        These serial number are successfully returned : <br>{{$succ}}
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if (isset($numberf)) { ?>
            <?php if ($numberf > 0) { ?>
                <div class="alert alert-warning alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Failed inserting {{$numberf}} data. <br>
                    <?php if (isset($fail)) { ?>
                        These serial number are not in Database : <br>{{$fail}}
                    <?php } ?>
                    <?php if (isset($noav)) { ?>
                        These serial number are not available (not yet ship out(ed)) : <br>{{$noav}}
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <form method="POST" action="{{route('showReturnInventory')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        <div class="row margtop20">
            <div class="col-xs-6">
                <div class="col-md-9">
                    <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict" data-validation="required" required>
                    <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                    <span id='pict-name'></span>
                </div>
            </div>
            <div class="col-xs-2"><button type="button" class="button btn-wide wide-h" id="btn_ceksn" style="background-color: #424242; color: white;">Check</button></div>
        </div>
        <br>
        <div class="row margbot20">
            <div class="col-xs-18">
                Notes:<br>
                1. File format must be in <b> xlsx only!</b><br>
                2. 1 column needed, with header 'id' before you start writing the <b>serial number</b> or <b>MSISDN</b><br>
                3. Use SerialNumber or MSISDN on each row
                4. If the serial number showing "E+11" or not string, add quotation mark before write the SerialNumber or MSISDN
            </div>
        </div>

        <div class="white-pane__bordered margbot20">
            <h4>Available inventory</h4>
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
            <h4>Not available inventory: </h4>
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
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Return Date: </label>
                </div>
                <div class="col-sm-5">
                    <input type="date" class="input-stretch" id='shipindate' name="eventDate" data-validation="required" required>
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
                    <label class="fw300" style="margin-top: 7px;">Remark: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="remark" id='remark'>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-7">
                <input type="submit" class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;" id='btn-sub' disabled="true">
            </div>
            <div class="col-xs-1">
                <button type="button" onclick="printPrev(this)" disabled="" id="btn-print-pdf-so"><span class="glyphicon glyphicon-print"></span></button>
            </div>
        </div>
    </form>
</div>
@stop
@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/chosen.jquery.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script>
                    Date.prototype.toDateInputValue = (function () {
                        var local = new Date(this);
                        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
                        return local.toJSON().slice(0, 10);
                    });
                    var table = '';
                    var table2 = '';
                    var inventoryDataBackup = '';
                    var inventoryDataBackup2 = '';
                    var semua_sn = '';
                    var getForm = '';
                    var getShipout = '';
                    var shto = '';
                    var topsn = '';
                    var getPDFret = '<?php echo Route('getPDFReturn') ?>';
                    var postSemuaSN = '<?php echo Route('postSemuaSN') ?>';

                    $(function () {
                        oFileIn = document.getElementById('input-pict');
                        if (oFileIn.addEventListener) {
                            oFileIn.addEventListener('change', filePicked, false);
                        }
                    });

                    window.printPrev = function (element) {
                        var shipout_date = document.getElementById('shipindate').value;
                        var shipout_SN = document.getElementById('formSN').value;
                        var remark_inp = document.getElementById('remark').value;
                        $.post(getPDFret,
                                {date: shipout_date, sn: shipout_SN, array_SN: semua_sn, remark:remark_inp}
                        , function (data) {

                        }).done(function (data) {
                            console.log(data);
                            window.open(getPDFret);
                        });
                    };

                    function filePicked(oEvent) {
                        // Get The File From The Input
                        var oFile = oEvent.target.files[0];
                        var sFilename = oFile.name;
                        // Create A File Reader HTML5
                        var reader = new FileReader();

                        // Ready The Event For When A File Gets Selected
                        reader.onload = function (e) {
                            semua_sn = '';
                            var data = e.target.result;
                            var cfb = XLSX.read(data, {type: 'binary'});
                            // Loop Over Each Sheet
                            cfb.SheetNames.forEach(function (sheetName) {
                                // Obtain The Current Row As CSV
                                var sCSV = XLS.utils.make_csv(cfb.Sheets[sheetName]);
                                var oJS = XLS.utils.sheet_to_row_object_array(cfb.Sheets[sheetName]);

//            $("#my_file_output").html(sCSV);
                                oJS.forEach(function (item, index) {
                                    topsn = item.id;
                                    if (semua_sn == '') {
                                        semua_sn += item.id
                                    } else {
                                        semua_sn += ',' + item.id
                                    }
                                });

                                console.log(semua_sn)
                                console.log(oJS[0].id)
                            });
                        };

                        // Tell JS To Start Reading The File.. You could delay this if desired
                        reader.readAsBinaryString(oFile);
                    }

                    $('#btn_ceksn').on('click', function (e) {
                        $('#btn-sub').removeAttr('disabled');
                        if ($.fn.dataTable.isDataTable('#example')) {
                            table.fnDestroy();
                            table2.fnDestroy();
                        }
                        $.post(postSemuaSN, {sn: semua_sn}, function (data) {

                        }).done(function (data) {
                            inventoryDataBackup = '<?php echo Route('inventoryDataBackupReturn') ?>' + '/1';
                            inventoryDataBackup2 = '<?php echo Route('inventoryDataBackupReturn') ?>' + '/0';
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
                            getShipout = '<?php echo Route('getShipout') ?>';
                            $.post(getShipout, {sn: topsn}, function (data) {
                                shto = data;
                            }).done(function () {
                                refreshFormSN();
                                $('#btn-print-pdf-so').removeAttr('disabled');
                            });
                        });
                    });
                    $('#btn-insert-image').on('click', function (e) {
                        $('#input-pict').click();

                    });
                    $('#input-pict').on('change', function (e) {
                        $('#pict-name').html($('#input-pict').val().split('\\').pop());
                    });
                    $(document).ready(function () {
                        $('#shipindate').val(new Date().toDateInputValue());
                    });

                    $('#shipindate').on('change', function () {
                        refreshFormSN();
                    });

                    var refreshFormSN = function () {
                        var shipoutto = '';
                        var stringtamp = '';
                        if (shto != '') {
                            shipoutto = shto;
                            if (shipoutto.includes('ASPROF'))
                                shipoutto = 'ASF';
                            else if (shipoutto.includes('ASPROT'))
                                shipoutto = 'AST';
                            else
                                shipoutto = shipoutto.substring(0, 3).toUpperCase();
                        }
                        stringtamp = $('#shipindate').val() + '/RE/' + shipoutto;
                        getForm = '<?php echo Route('getForm') ?>';
                        $.post(getForm, {sn: stringtamp}, function (data) {
                            stringtamp += data;
                        }).done(function () {
                            $('#formSN').val(stringtamp);
                        });
                    };
</script>
@stop
