$(document).ready(function () {
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

//=========================function====================================
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#materialID').parent('div').append('<span class="help-block form-error" id="spanErrorID">Material ID has already been taken</span>');
    $('#materialID').parent('div').removeClass('has-success');
    $('#materialID').parent('div').addClass('has-error');
    $('#materialID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#materialID').parent('div').removeClass('has-error');
    $('#materialID').parent('div').addClass('has-success');
    $('#materialID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#materialID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'materialID')) {
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
//========================/function====================================

$(".btn-edit").click(function () {
    var data = $(this).data('all');
    data = decryptDataID(data, b);
    $('#materialNameUpdate').val(searchData("MaterialName", data));
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
});

$("#materialID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
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

$.validate({
    form: '#form-update'
});
