@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<div class='row'>
    <h1>Add New User</h1>
    <div class='col-xs-8'>
        <form method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
            <div class="white-pane__bordered margbot20">
                <div class="row">
                    <?php if (isset($success)) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Successfully Add User.
                        </div>
                    <?php } ?>
                    <?php if (isset($error)) { ?>
                        <div class="alert alert-error alert-dismissible" role="alert" style="width: 98%; margin: 1%">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{$error}}
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Add User</h4>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Username</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Username" name='username' required="">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name='password' required="">
                </div>
                <div class="form-group">
                    <label for="exampleSelect1">Role</label>
                    <select class="form-control" id="exampleSelect1" name='position' required="">
                        <option value='1'>Administrator</option>
                        <option value='2'>Guess</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="iplock">IP Lock</label>
                    <select class="form-control" id="iplock" name='iplock' required="">
                        <option value='1'>No Lock</option>
                        <option value='2'>Ip Address</option>
                        <option value='3'>LAN Only</option>
                    </select>
                </div>
                <div class="form-group" id="ipadd" style='display: none;'>
                    <label for="exampleInputPassword1">Ip Address</label>
                    <input type="text" class="form-control" placeholder="Ip Address 192.168.1.1" name='ipadd'>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
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
$('#iplock').on('change', function (e) {
    if ($('#iplock').val() === '2') {
        $('#ipadd').show();
    } else {
        $('#ipadd').hide();
    }
});
</script>
@stop
