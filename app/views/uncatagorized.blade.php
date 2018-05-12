@extends('template.header-footer')

@section('title')
{{$page}}
@stop

@section('title-view')
{{$page}}
@stop

@section('main-section')
<div class="white-pane__bordered margbot20">
    <table id="example" class="display table-rwd table-inventory" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>MSISDN</th>
                <th>Remark</th>
                <th>Action</th>
            <!--<th>Actions</th>-->
            </tr>
        </thead>
    </table>
</div>
@stop

@section('js-content')
<script type="text/javascript" src="{{Asset('lib/bootstrap/js/jquery.dataTables.min.js')}}"></script>
<script src="{{Asset('jquery-validation/form-validator/jquery.form-validator.js')}}"></script>
<script type="text/javascript" src="{{Asset('js/chosen.jquery.min.js')}}"></script>
<script>
                    var table = '';
                    var inventoryDataBackupUncat = '<?php echo Route('inventoryDataBackupUncat') ?>';
                    var postShipin = '<?php echo Route('postShipin') ?>';
                    
                    window.goShipin = function (element) {
                        notin = $(element).data('internal');
                        if (confirm("Do you want to shipin this inventory (" + notin + ")?") == true) {
                            $.post(postShipin, {sn: notin}, function (data) {

                            }).done(function () {
                                drawTable();
                            });
                        }
                    };
                    table = $('#example').dataTable({
                        "draw": 10,
                        "processing": true,
                        "bDestroy": true,
                        "serverSide": true,
                        "ajax": inventoryDataBackupUncat
                    });

                    var drawTable = function () {
                        if ($.fn.dataTable.isDataTable('#example')) {
                            table.fnDestroy();
                        }
                        table = $('#example').dataTable({
                            "draw": 10,
                            "bDestroy": true,
                            "processing": true,
                            "serverSide": true,
                            "ajax": inventoryDataBackupUncat
                        });
                    };
                    
</script>
@stop
