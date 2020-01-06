@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<form method="POST" action="{{route('showInventoryShipout')}}" accept-charset="UTF-8" enctype="multipart/form-data" id="form-so">
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
                <div class="col-sm-4">
                    <input type="text" class="input-stretch" id='msi'>
                </div>
                <div class="col-sm-2"><button type="button" class="button btn-wide wide-h" id="btn_cekmsi" style="background-color: #424242; color: white;">Set</button></div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Serial number: </label>
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="input-small form-control" id='shipoutstart' name='shipoutstart' data-validation="required" required> 
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-small form-control" id='shipoutend' name='shipoutend' data-validation="required" required>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">NT$</span>
                        <input type="text" class="form-control" id="price-inv" value="0">
                        <span class="input-group-addon">.00</span>
                    </div>
                </div>
                <div class="col-sm-2"><button type="button" class="button btn-wide wide-h" id="btn_ceksn" style="background-color: #424242; color: white;">Add</button></div>
                <div class="col-sm-2"><button type="button" class="button btn-wide wide-h" id="btn_reset" style="background-color: #424242; color: white;">Reset</button></div>
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
                    <th>Shipout Price</th>
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
                    <th>Shipout Price</th>
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
                    <th>Shipout Price</th>
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
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">發票 number: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" id='fabiao' name="fabiaoNumber">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-xs-2">
                    <label class="fw300" style="margin-top: 7px;">Shipout to: </label>
                </div>
                <?php // dd(DB::table('m_historymovement')->selectRaw('DISTINCT(SUBSTRING_INDEX(SubAgent, " ", 1)) as "Agent"')->get()); ?>
                <div class="col-xs-3" style="margin-top: 5px;">
                    <select data-placeholder="Choose a destination..." class="chosen-select" style="width: 100%" name="shipout" id="shipoutto">
                        <option></option>
                        @foreach(DB::table('m_historymovement')->selectRaw('DISTINCT(SUBSTRING_INDEX(SubAgent, " ", 1)) as "Agent"')->get() as $agent)
                        @if($agent->Agent != '' && $agent->Agent != '-')
                        <?php
                        $subagent = $agent->Agent;
                        if (strtolower($subagent) === "asia") {
                            $subagent = "ASIA LIFE";
                        } else {
                            $subagent = strtoupper($subagent);
                        }
                        ?>
                        <option value="{{$subagent}}">
                            {{$subagent}}
                        </option>
                        @endif
                        @endforeach
                        <option value="SUSIN">
                            SUSIN
                        </option>
                        <!--                        <option value="TOKO">TOKO</option>
                                                <option value="ASPROF">ASPROF</option>
                                                <option value="ASPROT">ASPROT</option>
                                                <option value="DIRECT">DIRECT</option>
                                                <option value="INDEX">INDEX</option>
                                                <option value="PRE-EMPTIVE">PRE-EMPTIVE</option>
                                                <option value="COLUMBIA">COLUMBIA</option>
                                                <option value="ASIA LIFE">ASIA LIFE</option>-->
                    </select>
                </div>
                <div class="col-xs-3" style="margin-left: 10px;">
                    <div class="checkbox">
                        <input type="checkbox" id="cons-stat">as consignment
                    </div>
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
                        <option value="SUSIN">
                            SUSIN
                        </option>
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
                    <label class="fw300" style="margin-top: 7px;">Remark: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="remark" id="remark">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-6">
                <button type="button" class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;" id="btn-form-so">submit</button>
            </div>
            <div class="col-xs-1">
                <button type="button" onclick="printPrev(this)" disabled="" id="btn-print-pdf-so"><span class="glyphicon glyphicon-print"></span></button>
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
                    var table3 = '';
                    var inventoryDataBackup = '';
                    var inventoryDataBackup2 = '';
                    var inventoryDataBackup3 = '';
                    var getSN = '';
                    var getForm = '';
                    var notin = '';
                    var postMissing = '<?php echo Route('postMissing') ?>';
                    var postConsStat = '<?php echo Route('postConsStat') ?>';
                    var postNewAgent = '<?php echo Route('postNewAgent') ?>';
                    var postAvail = '<?php echo Route('postAvail') ?>';
                    var addInv = '<?php echo Route('addInv') ?>';
                    var delInv = '<?php echo Route('delInv') ?>';
                    var ajax1 = '<?php echo Route('getSubAgent') ?>';
                    var getPDF = '<?php echo Route('getPDFShipout') ?>';
                    var newAgentName = '';

                    $('#cons-stat').on('click', function (e) {
                        refreshFormSN();
                        var atLeastOneIsChecked = $('#cons-stat:checkbox:checked').length > 0;
                        if (atLeastOneIsChecked) {
                            $.post(postConsStat, {cs: '1'}, function (data) {
                                console.log(data);
                            });
                        } else {
                            $.post(postConsStat, {cs: '0'}, function (data) {
                                console.log(data);
                            });
                        }
                    });

                    $('#btn-form-so').on('click', function (e) {
                        var atLeastOneIsChecked = $('#cons-stat:checkbox:checked').length > 0;
                        if (atLeastOneIsChecked) {
                            if (confirm("Do you want to submit this as Consignment?") == true) {
                                $.post(postConsStat, {cs: '1'}, function (data) {

                                }).done(function () {
                                    document.getElementById("form-so").submit();
                                });
                            }
                        } else {
                            if (confirm("Do you want to submit this as Shipout?") == true) {
                                $.post(postConsStat, {cs: '0'}, function (data) {

                                }).done(function () {
                                    document.getElementById("form-so").submit();
                                });
                            }
                        }
                    });

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
                        var atLeastOneIsChecked = $('#cons-stat:checkbox:checked').length > 0;
                        var cse = '0';
                        if (atLeastOneIsChecked) {
                            cse = '1';
                        }
                        var shipout_date = document.getElementById('shipindate').value;
                        var shipout_SN = document.getElementById('formSN').value;
                        var shipout_to = document.getElementById('shipoutto').value;
                        var shipout_subagent = document.getElementById('subagent').value;
                        var shipout_start = document.getElementById('shipoutstart').value;
                        var shipout_end = document.getElementById('shipoutend').value;
                        var fabiaonum = document.getElementById('fabiao').value;
                        var remark_ = document.getElementById('remark').value;
                        $.post(getPDF,
                                {date: shipout_date, sn: shipout_SN, to: shipout_to, subagent: shipout_subagent
                                    , start: shipout_start, end: shipout_end, cs: cse, fabiao: fabiaonum, remark: remark_}
                        , function (data) {

                        }).done(function () {
                            window.open(getPDF);
                        });
                    };

                    window.newSubagent = function (element) {
                        if ($('#shipoutto').val() != '') {
                            var person = prompt("Please enter New Subagent name:", "please insert sub-agent name only..");
                            if (person == null || person == "") {
                                txt = "User cancelled the prompt.";
                            } else {
                                newAgentName = $('#shipoutto').val() + " " + person;
                                confirmNewAgent();
                            }
                        } else {
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

                    $("#price-inv").keyup(function () {
                        setTimeout(function () {
                            if ($("#price-inv").val().trim().length === 0) {
                                $("#price-inv").val(0);
                            }
                        }, 1000);
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

                    $('#btn_reset').on('click', function (e) {
                        $.post(delInv, function (data) {
                            console.log(data);
                        });
                        if ($.fn.dataTable.isDataTable('#example')) {
                            table.fnDestroy();
                            table2.fnDestroy();
                            table3.fnDestroy();
                            $('#example')
                                    .find('tbody')
                                    .remove();
                            $('#example2')
                                    .find('tbody')
                                    .remove();
                            $('#example3')
                                    .find('tbody')
                                    .remove();
                        }
                    });

                    $('#btn_ceksn').on('click', function (e) {
                        var str = document.getElementById('shipoutstart').value;
                        var ended = document.getElementById('shipoutend').value;
                        var priced = document.getElementById('price-inv').value;

                        if (str == '' || ended == '') {
                            alert("Please enter valid start and end Serial Number!")
                        } else {
                            $.post(addInv, {start: str, end: ended,
                                price: priced}, function (data) {
                                console.log(data);
                            }).done(function () {
                                refreshTable();
                                $('#btn-print-pdf-so').removeAttr('disabled');
                            });
                        }
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
                        var atLeastOneIsChecked = $('#cons-stat:checkbox:checked').length > 0;
                        if (atLeastOneIsChecked) {
                            stringtamp = $('#shipindate').val() + '/CO/' + shipoutto;
                        } else {
                            stringtamp = $('#shipindate').val() + '/SO/' + shipoutto;
                        }
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
