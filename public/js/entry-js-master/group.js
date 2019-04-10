$(document).ready(function () {
    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#groupNameUpdate').val(searchData("GroupName", data));
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
                targets: [2],
                orderData: [2, 0]
            }]
    });
});
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#groupID').parent('li').append('<span class="help-block form-error" id="spanErrorID">Group ID has already been taken</span>');
    $('#groupID').parent('li').removeClass('has-success');
    $('#groupID').parent('li').addClass('has-error');
    $('#groupID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#groupID').parent('li').removeClass('has-error');
    $('#groupID').parent('li').addClass('has-success');
    $('#groupID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#groupID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'groupID')) {
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
$("#groupID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});