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
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-ivr-purchase'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($success)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting Churn data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset Churn Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_churn'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-ivr' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-reset-productive'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($successp)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting productive data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset Productive Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_prod'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-reset-prod' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-reset-ivr'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($successi)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting IVR data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset IVR Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_ivr'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-reset-ivr' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-reset-act'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($successa)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting Aqcuisition Date.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset Aqcuisition Date Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_act'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-reset-act' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-reset-top'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($successt)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting Top Up data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset Top Up Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_top'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-reset-top' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-reset-sip'>
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($successsip)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully resetting SIP data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset SIP Reporting</h4>
                    </div>
                </div>
                <input type="hidden" name='jenis' value='reset_top'>
                <div class="row margtop20">
                    <div class="col-xs-8">
                        <button type="button" id='btn-submit-reset-sip' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Submit</button>
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
$('#btn-submit-ivr').on('click', function (e) {
    document.getElementById("form-ivr-purchase").submit();
});
$('#btn-submit-reset-prod').on('click', function (e) {
    document.getElementById("form-reset-productive").submit();
});
$('#btn-submit-reset-ivr').on('click', function (e) {
    document.getElementById("form-reset-ivr").submit();
});
$('#btn-submit-reset-act').on('click', function (e) {
    document.getElementById("form-reset-act").submit();
});
$('#btn-submit-reset-top').on('click', function (e) {
    document.getElementById("form-reset-top").submit();
});
$('#btn-submit-reset-sip').on('click', function (e) {
    document.getElementById("form-reset-sip").submit();
});
</script>
@stop
