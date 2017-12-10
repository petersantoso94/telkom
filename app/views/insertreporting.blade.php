@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-ivr-purchase'>
    <div class="white-pane__bordered margbot20">
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
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-8">
                <div class="form-group">
                    <div class="col-md-12">
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
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-apf'>
    <input type="hidden" name='jenis' value='apf'>
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <div class="col-xs-12">
                <h4>APF Return</h4>
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
            <div class="col-xs-8">
                <div class="form-group">
                    <div class="col-md-12">
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
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-activation'>
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <?php if (isset($numberac)) { ?>
                <?php if ($numberac > 0) { ?>
                    <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Successfully inserting {{$numberac}} data.
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h4>Activation Date</h4>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-8">
                <div class="form-group">
                    <div class="col-md-12">
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
@stop
@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
$('#btn-insert-image').on('click', function (e) {
    $('#input-pict').click();

});
$('#input-pict').on('change', function (e) {
    $('#pict-name').html($('#input-pict').val().split('\\').pop());
});
$('#btn-insert-image2').on('click', function (e) {
    $('#input-pict2').click();

});
$('#input-pict2').on('change', function (e) {
    $('#pict-name2').html($('#input-pict2').val().split('\\').pop());
});
$('#btn-insert-image3').on('click', function (e) {
    $('#input-pict3').click();

});
$('#input-pict3').on('change', function (e) {
    $('#pict-name3').html($('#input-pict3').val().split('\\').pop());
});
$('#btn-submit-apf').on('click', function (e) {
    document.getElementById("form-apf").submit();
});
$('#btn-submit-ivr').on('click', function (e) {
    document.getElementById("form-ivr-purchase").submit();
});
$('#btn-submit-act').on('click', function (e) {
    document.getElementById("form-activation").submit();
});
</script>
@stop
