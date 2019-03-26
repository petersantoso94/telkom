$(document).ready(function () {
    $("#searchInventory").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getResultSearch, {id: $("#searchInventory").val()}).done(function (data) {
                $("#selectInventory").html(data);
            });
        }
    });
    $("#searchInventoryUpdate").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getResultSearchUpdate, {id: $("#searchInventoryUpdate").val()}).done(function (data) {
                $("#selectInventoryUpdate").html(data);
            });
        }
    });
    $("#form-insert").submit(function (event) {
        event.preventDefault();
    });
    $("#form-update").submit(function (event) {
//        event.preventDefault();
    });
    $("#tipeDefault,#tipeNoDefault,#tipeDefaultUpdate,#tipeNoDefaultUpdate").change(function () {
        if ($('#tipeDefault').attr('checked') == 'checked') {
            $('#tipeDefault').removeAttr('checked');
            $('#tipeNoDefault').attr('checked', 'checked');
            $('#value').attr("readonly", false);
            $('#value').attr("style", "background-color:#fff");
        } else {
            $('#tipeNoDefault').removeAttr('checked');
            $('#tipeDefault').attr('checked', 'checked');
            $('#value').val(1);
            $('#value').attr("readonly", true);
            $('#value').attr("style", "background-color:#ddd");
        }
        if ($('#tipeDefaultUpdate').attr('checked') == 'checked') {
            $('#tipeDefaultUpdate').removeAttr('checked');
            $('#tipeNoDefaultUpdate').attr('checked', 'checked');
            $('#valueUpdate').attr("readonly", false);
            $('#valueUpdate').attr("style", "background-color:#fff");
        } else {
            $('#tipeNoDefaultUpdate').removeAttr('checked');
            $('#tipeDefaultUpdate').attr('checked', 'checked');
            $('#valueUpdate').val(1);
            $('#valueUpdate').attr("readonly", true);
            $('#valueUpdate').attr("style", "background-color:#ddd");
        }
    });

    window.updateAttach = function (element) {
        var data = $(element).data('all');
        $('#inventoryInternalIDUpdate').html('');
        $('#inventoryInternalIDUpdate').html('<option value="' + searchData("InventoryInternalID", data) + '" selected>' + searchData("InventoryName", data) + '</option>');
        $('#uomInternalIDUpdate').val(searchData("UomInternalID", data));
        $('#priceUpdate').val(addPeriod(searchData("Price", data), ","));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#valueUpdate').val(addPeriod(searchData("Value", data), ','));
        $('#idUpdate').val(searchData("InternalID", data));
        $('#updateName').html($(element).data('name'));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
        if (searchData("Default", data) == 1) {
            $('#valueUpdate').attr("readonly", true);
            $('#valueUpdate').attr("style", "background-color:#ddd");
            document.getElementById("tipeNoDefaultUpdate").checked = false;
            document.getElementById("tipeDefaultUpdate").checked = true;
            $('#tipeNoDefaultUpdate').removeAttr('checked');
            $('#tipeDefaultUpdate').attr('checked', 'checked');
        } else {
            $('#valueUpdate').attr("readonly", false);
            $('#valueUpdate').attr("style", "background-color:#fff");
            document.getElementById("tipeDefaultUpdate").checked = false;
            document.getElementById("tipeNoDefaultUpdate").checked = true;
            $('#tipeDefaultUpdate').removeAttr('checked');
            $('#tipeNoDefaultUpdate').attr('checked', 'checked');
//            alert(searchData("InventoryInternalID", data));
//            alert(searchData("UomInternalID", data));
            $.ajax({
                type: "POST",
                url: cekgantivalue,
                data: {inventoryID: searchData("InventoryInternalID", data), uomID: searchData("UomInternalID", data)},
                success: function (hasil) {
                    if (hasil == 'false') {
                        $('#valueUpdate').attr("readonly", true);
                        $('#valueUpdate').attr("style", "background-color:#ddd");
                    }
                },
                error: function (hasil) {
                    alert("error");
                }
            });
        }
    }
    window.deleteAttach = function (element) {
        $('#idDelete').val($(element).data('internal'));
        $('#deleteName').html($(element).data('name'));
    };
    $("#btn-tambah").click(function () {
        $('#value').val(removePeriod($('#value').val(), ','));
    });
    $("#btn-ubah").click(function () {
        $('#valueUpdate').val(removePeriod($('#valueUpdate').val(), ','));
    });

    var config = {
        '.chosen-select': {}
    };

    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }

}
);
//function gagal() {
//    if ($('#spanErrorID')) {
//        $('#spanErrorID').remove();
//    }
//    $('#currencyID').parent('li').append('<span class="help-block form-error" id="spanErrorID">Currency ID has already been taken</span>');
//    $('#currencyID').parent('li').removeClass('has-success');
//    $('#currencyID').parent('li').addClass('has-error');
//    $('#currencyID').css("border-color", "rgb(169, 68, 66)");
//}
//function sukses() {
//    $('#spanErrorID').remove();
//    $('#currencyID').parent('li').removeClass('has-error');
//    $('#currencyID').parent('li').addClass('has-success');
//    $('#currencyID').css("border-color", "");
//}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#InternalID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'InternalID')) {
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
//$("#currencyID").blur(function () {
//    var dataID = a;
//    validationID(dataID, 0, b);
//});
$("#btn-submit").click(function () {
    $('#value').val(removePeriod($('#value').val(), ','));
});
$("#btn-update").click(function () {
    $('#valueUpdate').val(removePeriod($('#valueUpdate').val(), ','));
});
