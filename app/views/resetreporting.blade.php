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
                            Successfully resetting all reporting data.
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Reset ALL Reporting</h4>
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
</script>
@stop
