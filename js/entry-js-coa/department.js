$(document).ready(function () {
    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#nameUpdate').val(searchData("DepartmentName", data));
        document.getElementById("tipe" + searchData("Default", data) + "Default").checked = true;
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
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
                targets: [4],
                orderData: [4, 0]
            }]
    });
});
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#departmentID').parent('div').append('<span class="help-block form-error" id="spanErrorID">Department ID has already been taken</span>');
    $('#departmentID').parent('div').removeClass('has-success');
    $('#departmentID').parent('div').addClass('has-error');
    $('#departmentID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#departmentID').parent('div').removeClass('has-error');
    $('#departmentID').parent('div').addClass('has-success');
    $('#departmentID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#departmentID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'DepartmentID')) {
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
$("#departmentID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});