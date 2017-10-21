var emailLama = '';
$(document).ready(function () {
    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#nameUpdate').val(searchData("UserName", data));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#phoneUpdate').val(searchData("Phone", data));
        $('#emailUpdate').val(searchData("Email", data));
        emailLama = searchData("Email", data);
        $('#idUpdate').val(searchData("InternalID", data));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
        $("#com" + searchData("CompanyInternalID", data)).attr('selected', 'selected');
        $("#war" + searchData("WarehouseInternalID", data)).attr('selected', 'selected');
        $('#companyUpdate').trigger("chosen:updated");
        $('#warehouseUpdate').trigger("chosen:updated");
    });
    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
    });
    $(".btn-active").click(function () {
        $('#idActive').val($(this).data('internal'));
    });
    $(".btn-non-active").click(function () {
        $('#idNonActive').val($(this).data('internal'));
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

    $('#startDateReport').datepicker();
    $('#endDateReport').datepicker();
    $("#startDateReport").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#endDateReport").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#endDateReport, #startDateReport').change(function () {
        if ($('#startDateReport').val() == '') {
            $('#startDateReport').val($('#endDateReport').val());
        } else if ($('#endDateReport').val() == '') {
            $('#endDateReport').val($('#startDateReport').val());
        } else if (dateCheckHigher($('#startDateReport').val(), $('#endDateReport').val()) == 'start') {
            $('#endDateReport').val($('#startDateReport').val());
        }
    });
});
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#userID').parent('div').append('<span class="help-block form-error" id="spanErrorID">User ID has already been taken</span>');
    $('#userID').parent('div').removeClass('has-success');
    $('#userID').parent('div').addClass('has-error');
    $('#userID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#userID').parent('div').removeClass('has-error');
    $('#userID').parent('div').addClass('has-success');
    $('#userID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#userID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'UserID')) {
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
function gagalEmail(update) {
    if ($('#spanErrorIDEmail' + update)) {
        $('#spanErrorIDEmail' + update).remove();
    }
    $('#email' + update).parent('div').append('<span class="help-block form-error" id="spanErrorIDEmail' + update + '">Email has already been taken</span>');
    $('#email' + update).parent('div').removeClass('has-success');
    $('#email' + update).parent('div').addClass('has-error');
    $('#email' + update).css("border-color", "rgb(169, 68, 66)");
}
function suksesEmail(update) {
    $('#spanErrorIDEmail' + update).remove();
    $('#email' + update).parent('div').removeClass('has-error');
    $('#email' + update).parent('div').addClass('has-success');
    $('#email' + update).css("border-color", "");
}
function validationIDEmail(dataID, status, r, update) {
    dataID = decryptDataID(dataID, r);
    var search = $('#email' + update).val();
    if (search == '') {
        return false;
    } else if (update == 'Update' && emailLama.toUpperCase() == search.toUpperCase()) {
        suksesEmail(update);
        if (status == 1) {
            return true; //form submit
        }
    } else if (searchID(search, dataID, 'Email')) {
        gagalEmail(update);
        if (status == 1) {
            return false; // Will stop the submission of the form
        }
    } else {
        suksesEmail(update);
        if (status == 1) {
            return true; //form submit
        }
    }
}
$.validate({
    form: '#form-update',
    onError: function () {
        var dataIDEmail = c;
        validationIDEmail(dataIDEmail, 1, b, 'Update');
    },
    onSuccess: function () {
        var dataIDEmail = c;
        return validationIDEmail(dataIDEmail, 1, b, 'Update')
    }
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
    modules: 'security',
    form: '#form-insert',
    onError: function () {
        var dataID = a;
        var dataIDEmail = c;
        validationID(dataID, 1, b);
        validationIDEmail(dataIDEmail, 1, b, '');
    },
    onSuccess: function () {
        var dataID = a;
        var dataIDEmail = c;
        if (validationID(dataID, 1, b) == true && validationIDEmail(dataIDEmail, 1, b, '') == true) {
            return true;
        } else {
            return false;
        }
    }
});
$("#userID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});
$("#email").blur(function () {
    var dataIDEmail = c;
    validationIDEmail(dataIDEmail, 0, b, '');
});
$("#emailUpdate").blur(function () {
    var dataIDEmail = c;
    validationIDEmail(dataIDEmail, 1, b, 'Update');
});

$("#btn-report-transaction").click(function () {
    if ($('#startDateReport').val() == '' && $('#endDateReport').val() == '') {
        var tanggal = new Date();
        var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
        $('#startDateReport').val(tanggalText);
        $('#endDateReport').val($('#startDateReport').val())
    } else if ($('#startDateReport').val() == '') {
        $('#startDateReport').val($('#endDateReport').val())
    } else if ($('#endDateReport').val() == '') {
        $('#endDateReport').val($('#startDateReport').val())
    } else if (dateCheckHigher($('#startDateReport').val(), $('#endDateReport').val()) == 'start') {
        $('#endDateReport').val($('#startDateReport').val())
    }
});