@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-change'>
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <div class="col-xs-12">
                <h4>Change Agent</h4>
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
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Shipout to: </label>
                </div>
                <div class="col-sm-5" style="margin-top: 5px;">
                    <select class="chosen-select" style="" name="OldName" id='old-name'>
                        @foreach(DB::table('m_historymovement')->select('SubAgent')->distinct()->get() as $agent)
                        @if($agent->SubAgent != '')
                        <option value="{{$agent->SubAgent}}">
                            {{$agent->SubAgent}}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <input type="hidden" name='jenis' value='agent'>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">New Name: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="NewName" id='new-name'>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-8">
                <button type="button" id='btn-submit' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Rename</button>
            </div>
        </div>
    </div>
</form>
<form method="POST" accept-charset="UTF-8" enctype="multipart/form-data" id='form-change2'>
    <input type="hidden" name='jenis' value='warehouse'>
    <div class="white-pane__bordered margbot20">
        <div class="row">
            <div class="col-xs-12">
                <h4>Change Warehouse</h4>
            </div>
        </div>
        <div class="row">
            <?php if (isset($numberw)) { ?>
                <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Successfully updating {{$numberw}} data.
                </div>
            <?php } ?>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Warehouse: </label>
                </div>
                <div class="col-sm-5" style="margin-top: 5px;">
                    <select class="chosen-select" style="" name="OldName2" id='old-name2'>
                        @foreach(DB::table('m_historymovement')->select('Warehouse')->distinct()->get() as $agent)
                        @if($agent->Warehouse != '')
                        <option value="{{$agent->Warehouse}}">
                            {{$agent->Warehouse}}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">New Name: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="NewName2" id='new-name2'>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="col-xs-8">
                <button type="button" id='btn-submit2' class="button btnblue btn-wide wide-h" style="background-color: #424242; color: white;">Rename</button>
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
$('#btn-submit').on('click', function (e) {
    var oldname = document.getElementById("old-name").value;
    var newname = document.getElementById("new-name").value;
    if (confirm("Do you want to rename this '" + oldname + "' into '" + newname + "'") == true) {
        document.getElementById("form-change").submit();
    }
});
$('#btn-submit2').on('click', function (e) {
    var oldname = document.getElementById("old-name2").value;
    var newname = document.getElementById("new-name2").value;
    if (confirm("Do you want to rename this '" + oldname + "' into '" + newname + "'") == true) {
        document.getElementById("form-change2").submit();
    }
});
$(document).ready(function () {
    $(".chosen-select").chosen()
});
</script>
@stop
