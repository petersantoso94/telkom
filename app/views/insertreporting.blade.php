@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<div class='row'>
    <div class='col-xs-8'>
        <?php if (isset($response)) { ?>
            <?php if ($response == 'Failed') { ?>
                <div class="alert alert-warning alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Failed inserting data.
                </div>
            <?php } ?>
        <?php } ?>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-ivr-purchase'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($number)) { ?>
                        <?php if ($number > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$number}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if (isset($numberf)) { ?>
                        <?php if ($numberf > 0) { ?>
                            <div class="alert alert-warning alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Failed inserting {{$numberf}} data. Data duplication alert, these inventories are already shipped in.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>IVR Purchase</h4>
                        <p>File format in XLSX</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='ivr'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-ivr' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xs-8">
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-apf'>
            <input type="hidden" name='jenis' value='apf'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <div class="col-xs-12">
                        <h4>APF Return</h4>
                        <p>File format in XLSX, named : APF Returned.xlsx</p>
                    </div>
                </div>
                <div class="row">
                    <?php if (isset($numberapf)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully updating {{$numberapf}} data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict2" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image2"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name2'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-apf' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xs-8">
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-activation'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($numberac)) { ?>
                        <?php if ($numberac > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$numberac}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <?php if (isset($notfound)) { ?>
                        <div class="alert alert-danger alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            MSISDN NOT FOUND : {{$notfound}}
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Acquisition Report</h4>
                        <p>File format in XLSX</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict3" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image3"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name3'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='act'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-act' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-churn'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($numberch)) { ?>
                        <?php if ($numberch > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$numberch}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Churn Date</h4>
                        <p>File format in XSLX</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict4" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image4"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name4'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='churn'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-churn' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-productive'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($numberpr)) { ?>
                        <?php if ($numberpr > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$numberpr}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Productive MSISDN HK</h4>
                        <p>File format in CSV, named yyyymmdd_productive_hk</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict5" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image5"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name5'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='productive'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-productive' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-productive-tst'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($numberprtst)) { ?>
                        <?php if ($numberprtst > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$numberprtst}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Productive MSISDN TST</h4>
                        <p>File format in XLSX, named : MKT-D-0205_紅白動卡_yyyymmdd</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict8" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image8"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name8'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='productive-tst'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-productive-tst' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-topup'>
            <div class="white-pane__bordered margbot20" style="height: 270px;">
                <div class="row">
                    <?php if (isset($numbertop)) { ?>
                        <?php if ($numbertop > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully inserting {{$numbertop}} data.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Recharge Report</h4>
                        <p>File format in XLSX</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict6" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image6"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name6'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='topup'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-topup' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-sip'>
            <div class="white-pane__bordered margbot20"  style="height: 270px;">
                <div class="row">
                    <?php if (isset($numbersip)) { ?>
                        <?php if ($numbersip > 0) { ?>
                            <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                Successfully updating {{$numbersip}} activation store.
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Activated SIP</h4>
                        <p>File format in CSV, named : SIP Data.csv</p>
                    </div>
                </div>
                <div class="row margtop20">
                    <div class="col-xs-28">
                        <div class="form-group">
                            <div class="col-md-28">
                                <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict7" data-validation="required" required>
                                <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image7"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                                <span id='pict-name7'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='act_sip'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-sip' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
var global_file_name = '';
var global_name = '';
$('#btn-insert-image').on('click', function (e) {
    $('#input-pict').click();
});
$('#input-pict').on('change', function (e) {
    global_file_name = ($('#input-pict').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict').val().split('\\').pop()).split('.')[0];
    $('#pict-name').html($('#input-pict').val().split('\\').pop());
});
$('#btn-insert-image2').on('click', function (e) {
    $('#input-pict2').click();
});
$('#input-pict2').on('change', function (e) {
    global_file_name = ($('#input-pict2').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict2').val().split('\\').pop()).split('.')[0];
    $('#pict-name2').html($('#input-pict2').val().split('\\').pop());
});
$('#btn-insert-image3').on('click', function (e) {
    $('#input-pict3').click();
});
$('#input-pict3').on('change', function (e) {
    $('#pict-name3').html($('#input-pict3').val().split('\\').pop());
    global_name = ($('#input-pict3').val().split('\\').pop()).split('.')[0];
    global_file_name = ($('#input-pict3').val().split('\\').pop()).split('.')[1];
});
$('#btn-insert-image4').on('click', function (e) {
    $('#input-pict4').click();
});
$('#input-pict4').on('change', function (e) {
    global_file_name = ($('#input-pict4').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict4').val().split('\\').pop()).split('.')[0];
    $('#pict-name4').html($('#input-pict4').val().split('\\').pop());
});
$('#btn-insert-image5').on('click', function (e) {
    $('#input-pict5').click();
});
$('#input-pict5').on('change', function (e) {
    global_file_name = ($('#input-pict5').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict5').val().split('\\').pop()).split('.')[0];
    $('#pict-name5').html($('#input-pict5').val().split('\\').pop());
});
$('#btn-insert-image6').on('click', function (e) {
    $('#input-pict6').click();
});
$('#input-pict6').on('change', function (e) {
    global_file_name = ($('#input-pict6').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict6').val().split('\\').pop()).split('.')[0];
    $('#pict-name6').html($('#input-pict6').val().split('\\').pop());
});

$('#btn-insert-image7').on('click', function (e) {
    $('#input-pict7').click();
});
$('#input-pict7').on('change', function (e) {
    global_file_name = ($('#input-pict7').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict7').val().split('\\').pop()).split('.')[0];
    $('#pict-name7').html($('#input-pict7').val().split('\\').pop());
});
$('#btn-insert-image8').on('click', function (e) {
    $('#input-pict8').click();
});
$('#input-pict8').on('change', function (e) {
    global_file_name = ($('#input-pict8').val().split('\\').pop()).split('.')[1];
    global_name = ($('#input-pict8').val().split('\\').pop()).split('.')[0];
    $('#pict-name8').html($('#input-pict8').val().split('\\').pop());
});
$('#btn-submit-apf').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('apf'))
            document.getElementById("form-apf").submit();
        else
            alert('Please insert APF file, containing "APF" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-ivr').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('ivr'))
            document.getElementById("form-ivr-purchase").submit();
        else
            alert('Please insert IVR file, containing "IVR" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-act').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('acquisition'))
            document.getElementById("form-activation").submit();
        else
            alert('Please insert acquisition file, containing "acquisition" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-churn').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('churn'))
            document.getElementById("form-churn").submit();
        else
            alert('Please insert Churn file, containing "Churn" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-productive').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        var check_name = global_name.toLowerCase().split(" ")[0];
        if (check_name.length != 8) {
            alert('Please insert Productive file, containing correct date in its name');
        } else {
            if (global_name.toLowerCase().includes('productive') && global_name.toLowerCase().includes('hk'))
                document.getElementById("form-productive").submit();
            else
                alert('Please insert Productive file, containing "Productive HK" in its name');
        }
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-productive-tst').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        var check_name = global_name.toLowerCase().split("_")[2];
        if (check_name.length != 8) {
            alert('Please insert Productive file, containing correct date in its name');
        } else {
            if (global_name.toLowerCase().includes('mkt'))
                document.getElementById("form-productive-tst").submit();
            else
                alert('Please insert Productive file, containing "MKT" in its name');
        }
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-topup').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('recharge'))
            document.getElementById("form-topup").submit();
        else
            alert('Please insert Recharge file, containing "Recharge" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
$('#btn-submit-sip').on('click', function (e) {
    if (global_file_name === 'xls' || global_file_name === 'xlsx' || global_file_name === 'csv') {
        if (global_name.toLowerCase().includes('sip'))
            document.getElementById("form-sip").submit();
        else
            alert('Please insert SIP file, containing "SIP" in its name');
    } else
        alert('Not supported file format, please insert an XLS, XLSX, or CSV file');
});
</script>
@stop
