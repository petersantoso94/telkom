$(document).ready(function () {
    var countTipeInventory = 0;
    if ($('#tipeInventory').is(':checked')) {
        $('.coaInsert').each(function (i) {
            if ($(this).attr('id') != undefined) {
                if ($(this).attr('id').split('------')[1] == '1') {
                    $(this).attr('style', 'display: block;');
                    if (countTipeInventory == 0) {
                        $(this).attr('selected', true);
                        countTipeInventory = 1;
                    }
                } else {
                    $(this).attr('style', 'display: none;');
                    $(this).removeAttr('selected');
                }
            }
        });
    } else {
        $('.coaInsert').each(function (i) {
            if ($(this).attr('id') != undefined) {
                if ($(this).attr('id').split('------')[1] == '1') {
                    $(this).attr('style', 'display: none;');
                    $(this).removeAttr('selected');
                } else {
                    $(this).attr('style', 'display: block;');
                    if (countTipeInventory == 0) {
                        $(this).attr('selected', true);
                        countTipeInventory = 1;
                    }
                }
            }
        });
    }
    $('#coa').trigger("chosen:updated");

    $(".radioInsert").change(function () {
        var countTipeInventory = 0;
        if ($('#tipeInventory').is(':checked')) {
            $('.coaInsert').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    } else {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    }
                }
            });
        } else {
            $('.coaInsert').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    } else {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    }
                }
            });
        }
        $('#coa').trigger("chosen:updated");
    });

    $(".radioUpdate").change(function () {
        var countTipeInventory = 0;
        if ($('#tipeInventoryUpdate').is(':checked')) {
            $('.coaUpdate').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    } else {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    }
                }
            });
        } else {
            $('.coaUpdate').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    } else {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    }
                }
            });
        }
        $('#coaUpdate').trigger("chosen:updated");
    });

    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#nameUpdate').val(searchData("InventoryTypeName", data));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
        $('#tipeInventoryUpdate').removeAttr('checked');
        $('#tipeCostUpdate').removeAttr('checked');
        $('#tipeServiceUpdate').removeAttr('checked');
        if (searchData("Flag", data) == 1) {
            $('#tipeInventoryUpdate').prop('checked', true);
        } else if (searchData("Flag", data) == 2) {
            $('#tipeCostUpdate').prop('checked', true);
        } else if (searchData("Flag", data) == 3) {
            $('#tipeServiceUpdate').prop('checked', true);
        }
        var countTipeInventory = 0;
        if ($('#tipeInventoryUpdate').is(':checked')) {
            $('.coaUpdate').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    } else {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    }
                }
            });
        } else {
            $('.coaUpdate').each(function (i) {
                if ($(this).attr('id') != undefined) {
                    if ($(this).attr('id').split('------')[1] == '1') {
                        $(this).attr('style', 'display: none;');
                        $(this).removeAttr('selected');
                    } else {
                        $(this).attr('style', 'display: block;');
                        if (countTipeInventory == 0) {
                            $(this).attr('selected', true);
                            countTipeInventory = 1;
                        }
                    }
                }
            });
        }
        $('#coaUpdate').trigger("chosen:updated");
        var persediaan = 0;
        if (searchData("Flag", data) == 1) {
            persediaan = 1;
        }
        $("#coa" + searchData("coaID", data) + '------' + persediaan).attr('selected', 'selected');
        $('#coaUpdate').trigger("chosen:updated");
    });
    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
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
    $('#inventoryTypeID').parent('div').append('<span class="help-block form-error" id="spanErrorID">Inventory Type ID has already been taken</span>');
    $('#inventoryTypeID').parent('div').removeClass('has-success');
    $('#inventoryTypeID').parent('div').addClass('has-error');
    $('#inventoryTypeID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#inventoryTypeID').parent('div').removeClass('has-error');
    $('#inventoryTypeID').parent('div').addClass('has-success');
    $('#inventoryTypeID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#inventoryTypeID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'InventoryTypeID')) {
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
$("#inventoryTypeID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});