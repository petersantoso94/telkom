function validationID(status) {
    var search = $('#barcodeCode').val();
    if (search == '') {
        return false;
    } else {
        $.post(checkBarcodeCodeInventory, {id: search, internalID: $("#id-update").val()}).done(function (data) {
            if (data == 0) {
                sukses();
                if (status == 1) {
                    return true; //form submit
                }
            } else if (data == 1) {
                gagal();
                if (status == 1) {
                    return false; // Will stop the submission of the form
                }
            } else {
                kosong();
                if (status == 1) {
                    return false; // Will stop the submission of the form
                }
            }
        });
    }
}
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#barcodeCode').parent('div').append('<span class="help-block form-error" id="spanErrorID">Barcode Code has already been taken</span>');
    $('#barcodeCode').parent('div').removeClass('has-success');
    $('#barcodeCode').parent('div').addClass('has-error');
    $('#barcodeCode').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#barcodeCode').parent('div').removeClass('has-error');
    $('#barcodeCode').parent('div').addClass('has-success');
    $('#barcodeCode').css("border-color", "");
}
function kosong() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#barcodeCode').parent('div').append('<span class="help-block form-error" id="spanErrorID">This field is required</span>');
    $('#barcodeCode').parent('div').removeClass('has-success');
    $('#barcodeCode').parent('div').addClass('has-error');
    $('#barcodeCode').css("border-color", "rgb(169, 68, 66)");
}
//============================function===================================

$(document).ready(function () {

    $("#searchInventory").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getSearchResultInventorySimilarity, {id: $("#searchInventory").val()}).done(function (data) {
                $("#selectInventory").html(data);
            });
        }
    });

    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
    });

    $("#btn-addRow").click(function () {
        $('#table-inventory tr:last').after('<tr id="row' + baris + '">' +
                '<td class="chosen-uom">' +
                '<input type="hidden" class="inventory" style="width: 100px" id="inventory-' + baris + '" style="" name="inventory[]" value="' + $('#inventory-0').val() + '">' +
                $("#inventory-0 option[value='" + $("#inventory-0").val() + "']").text() +
                '</td>' +
                '<td><button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' +
                '</tr>');

        $(".btn-deleteRow").click(function () {
            if ($('#' + $(this).attr('data')).length > 0) {
                document.getElementById($(this).attr('data')).remove()
            }
        });
        baris++;
    });
});

$("#varietyInternalID, #brandInternalID, #categoryType, #finishingInternalID, #materialInternalID").change(function () {
    var varietyID = $("#varietyInternalID").val();
    var brandID = $("#brandInternalID").val();
    var category = $("#categoryType").val();
    var finishingID = $("#finishingInternalID").val();
    var materialID = $("#materialInternalID").val();
    var typeDetailID = $("#typeDetailInternalID").val();
    var typeDetailID2 = $("#typeDetail2").val();

    $.post(getNameInventory, {varietyID: varietyID, brandID: brandID, category: category, finishingID: finishingID, materialID: materialID, typeDetailID: typeDetailID, typeDetailID2: typeDetailID2}).done(function (data) {
        $("#name").val(data);
    });
    $.post(getPrintText, {varietyID: varietyID, brandID: brandID, category: category, finishingID: finishingID, materialID: materialID, typeDetailID: typeDetailID, typeDetailID2: typeDetailID2}).done(function (data) {
        $("#textPrint").val(data);
    });
});

$("#typeDetail2").blur(function () {
    var varietyID = $("#varietyInternalID").val();
    var brandID = $("#brandInternalID").val();
    var category = $("#categoryType").val();
    var finishingID = $("#finishingInternalID").val();
    var materialID = $("#materialInternalID").val();
    var typeDetailID = $("#typeDetailInternalID").val();
    var typeDetailID2 = $("#typeDetail2").val();

    $.post(getNameInventory, {varietyID: varietyID, brandID: brandID, category: category, finishingID: finishingID, materialID: materialID, typeDetailID: typeDetailID, typeDetailID2: typeDetailID2}).done(function (data) {
        $("#name").val(data);
    });
    $.post(getPrintText, {varietyID: varietyID, brandID: brandID, category: category, finishingID: finishingID, materialID: materialID, typeDetailID: typeDetailID, typeDetailID2: typeDetailID2}).done(function (data) {
        $("#textPrint").val(data);
    });
});

$("#barcodeCode").blur(function () {
    validationID(0);
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
    form: '#form-update',
    onError: function () {
        validationID(1);
    },
    onSuccess: function () {
        return validationID(1);
    }
});
