$(document).ready(function () {
    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#groupInternalIDUpdate').val(searchData("GroupInternalID", data));
        $('#varietyNameUpdate').val(searchData("VarietyName", data));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
        $('#groupInternalIDUpdate').trigger("chosen:updated");
    });
    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
        $('#deleteName').html($(this).data('name'));
    });

    $('#example').dataTable({
        columnDefs: [{
                targets: [0],
                orderData: [0, 1]
            }, {
                targets: [1],
                orderData: [1, 0]
            }, {
                targets: [2],
                orderData: [2, 0]
            }]
    });
});
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#varietyID').parent('li').append('<span class="help-block form-error" id="spanErrorID">Variety ID has already been taken</span>');
    $('#varietyID').parent('li').removeClass('has-success');
    $('#varietyID').parent('li').addClass('has-error');
    $('#varietyID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#varietyID').parent('li').removeClass('has-error');
    $('#varietyID').parent('li').addClass('has-success');
    $('#varietyID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#varietyID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'varietyID')) {
        gagal();
        if (status == 1) {
            return false; // Will stop the submission of the form
        }
    } else {
        sukses();
        if (status == 1) {
            return true; //form submit
        }
    }
}
$.validate({
    form: '#form-update'
});
var config = {
    '.chosen-select': {}
};
for (var selector in config) {
    $(selector).chosen({
        search_contains: true
    });
}
$.validate({
    form: '#form-insert',
    onError: function () {
        var dataID = a;
        validationID(dataID, 1, b);
    },
    onSuccess: function () {
        var dataID = a;
        return validationID(dataID, 1, b);
    }
});
$("#varietyID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});