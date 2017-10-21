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
            <h4>Input New Shipin Inventory</h4>
        </div>
    </div>
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
    <form method="POST" action="{{route('showInsertInventory')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        <div class="row margtop20">
            <div class="col-xs-8">
                <div class="form-group">
                    <div class="col-md-9">
                        <input type="file" name="sample_file" class="vis-hide" style="height:0px; overflow: hidden" id="input-pict" data-validation="required" required>
                        <button type="button" class="button btndef btn-mini no-shadow" id="btn-insert-image"><span class="glyphicon glyphicon-picture cgrey"></span> insert file</button>
                        <span id='pict-name'></span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row margbot20">
            <div class="col-xs-18">
                Notes:<br>
                1. File format must be in xlsx, xls (not CSV) <br>
                2. Header must be these strings ( 'NUMBER_CHKSUM' , 'SERIAL_NUMBER' , 'MSISDN' ), 
                UPPER or lower case doesn't matter <br>
                3. For SIM card, MSISDN is required. No MSISDN will be counted as voucher.
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Shippin Date: </label>
                </div>
                <div class="col-sm-5">
                    <input type="date" class="input-stretch" id='shipindate' name="eventDate" data-validation="required" required>
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Provider: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="provider" value="Taiwan Star">
                </div>
            </div>
        </div>
        <div class="row margtop20">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="fw300" style="margin-top: 7px;">Warehouse: </label>
                </div>
                <div class="col-sm-5">
                    <input type="text" class="input-stretch" name="warehouse" value="TELIN TAIWAN">
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
            <div class="col-xs-8">
                <input type="submit" class="button btnblue btn-wide wide-h">
            </div>
        </div>
    </form>
</div>
@stop
@section('js-content')
<script>
    Date.prototype.toDateInputValue = (function () {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0, 10);
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
</script>
@stop
