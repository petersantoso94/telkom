$(document).ready(function () {
    $(".btn-edit").click(function () {
        var data = $(this).data('all');
        data = decryptDataID(data, b);
        $('#nameUpdate').val(searchData("COAName", data));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
        $('#header1Update').val(searchData("Header1", data));
        $('#header2Update').val(searchData("Header2", data));
        $('#header3Update').val(searchData("Header3", data));
        $('#hcf1Update').val(searchData("HeaderCashFlow1", data));
        $('#hcf2Update').val(searchData("HeaderCashFlow2", data));
        $('#hcf3Update').val(searchData("HeaderCashFlow3", data));
        $('#balanceUpdate').val(addPeriod(searchData("InitialBalance", data),','));
        if (searchData("Persediaan", data) == 1) {
            $('#persediaanUpdate').prop('checked', true);
        } else {
            $('#persediaanUpdate').prop('checked', false);
        }
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }
    });
    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
        $('#AccID').val($(this).data('accountnumber'));
    });
    //isi dua berubah
    if (!countCOA) {
        var coa1 = $('#coa1').val();
        var values = $("#coa2>option").map(function () {
            return $(this).val();
        });
        var first = 0;
        var firstChoose = "";
        for (var a = 0; a < values.length; a++) {
            var tamp = values[a].split('---;---');
            var tamp2 = coa1.split('---;---');
            var coaParent = tamp2[0];
            var accID = tamp[0].substring(0, coaParent.length);
            if (accID == coaParent || tamp[0] == '0') {
                if (first == 0) {
                    firstChoose = tamp[0];
                    first++;
                }
                $("#" + tamp[0]).removeAttr('style', 'display:none;');
            } else {
                $("#" + tamp[0]).attr('style', 'display:none;');
            }
        }
        if (firstChoose == "0") {
            $("#coa2Empty").attr('selected', 'selected');
        } else {
            $("#" + firstChoose).attr('selected', 'selected');
        }
        $('#coa2').trigger("chosen:updated");
    }
    $(".header1").autocomplete({
        source: availableTags
    });
    $(".header2").autocomplete({
        source: availableTags2
    });
    $(".header3").autocomplete({
        source: availableTags3
    });
    $(".headerCashFlow1").autocomplete({
        source: availableTags4
    });
    $(".headerCashFlow2").autocomplete({
        source: availableTags5
    });
    $(".headerCashFlow3").autocomplete({
        source: availableTags6
    });
    $(".header1update").autocomplete({
        source: availableTags
    });
    $(".header2update").autocomplete({
        source: availableTags2
    });
    $(".header3update").autocomplete({
        source: availableTags3
    });
    $(".headerCashFlow1update").autocomplete({
        source: availableTags4
    });
    $(".headerCashFlow2update").autocomplete({
        source: availableTags5
    });
    $(".headerCashFlow3update").autocomplete({
        source: availableTags6
    });
    $(".header1").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".header2").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".header3").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".headerCashFlow1").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".headerCashFlow2").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".headerCashFlow3").autocomplete("option", "appendTo", ".formCoaInsert");
    $(".header1update").autocomplete("option", "appendTo", ".formCoaUpdate");
    $(".header2update").autocomplete("option", "appendTo", ".formCoaUpdate");
    $(".header3update").autocomplete("option", "appendTo", ".formCoaUpdate");
    $(".headerCashFlow1update").autocomplete("option", "appendTo", ".formCoaUpdate");
    $(".headerCashFlow2update").autocomplete("option", "appendTo", ".formCoaUpdate");
    $(".headerCashFlow3update").autocomplete("option", "appendTo", ".formCoaUpdate");
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
    $('#coa6').parent('div').append('<span class="help-block form-error" id="spanErrorID">Account ID has already been taken</span>');
    $('#coa6').parent('div').removeClass('has-success');
    $('#coa6').parent('div').addClass('has-error');
    $('.chosen-single').css("border-color", "rgb(169, 68, 66)");
    $('.chosen-single').css("color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#coa6').parent('div').removeClass('has-error');
    $('#coa6').parent('div').addClass('has-success');
    $('.chosen-single').css("border-color", "");
    $('.chosen-single').css("color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var coa1 = $('#coa1').val().split('---;---');
    var coa2 = $('#coa2').val().split('---;---');
    var coa3 = $('#coa3').val().split('---;---');
    var coa4 = $('#coa4').val().split('---;---');
    var coa5 = $('#coa5').val().split('---;---');
    var coa6 = $('#coa6').val().split('---;---');
    var search = coa1[2] + '.' + coa2[2] + '.' + coa3[2] + '.' + coa4[2] + '.' + coa5[2] + '.' + coa6[2];
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'accID')) {
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


$("#coa1").change(function (e) {
    //isi dua berubah
    var coa1 = $(this).val();
    var values = $("#coa2>option").map(function () {
        return $(this).val();
    });
    var first = 0;
    var firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        var tamp = values[a].split('---;---');
        var tamp2 = coa1.split('---;---');
        var coaParent = tamp2[0];
        var accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa2Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa2').trigger("chosen:updated");

    //isi 3 berubah
    var coa2 = firstChoose;
    values = $("#coa3>option").map(function () {
        return $(this).val();
    });
    first = 0;
    firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        tamp = values[a].split('---;---');
        coaParent = coa2;
        accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa3Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa3').trigger("chosen:updated");

    //isi 4 berubah
    var coa3 = firstChoose;
    values = $("#coa4>option").map(function () {
        return $(this).val();
    });
    first = 0;
    firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        tamp = values[a].split('---;---');
        coaParent = coa3;
        accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa4Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa4').trigger("chosen:updated");
});
$("#coa2").change(function (e) {
    //isi 3 berubah
    var coa2 = $(this).val();
    var values = $("#coa3>option").map(function () {
        return $(this).val();
    });
    var first = 0;
    var firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        var tamp = values[a].split('---;---');
        var tamp2 = coa2.split('---;---');
        var coaParent = tamp2[0];
        var accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa3Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa3').trigger("chosen:updated");

    //isi 4 berubah
    var coa3 = firstChoose;
    values = $("#coa4>option").map(function () {
        return $(this).val();
    });
    first = 0;
    firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        tamp = values[a].split('---;---');
        coaParent = coa3;
        accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa4Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa4').trigger("chosen:updated");
});
$("#coa3").change(function (e) {
    var coa3 = $(this).val();
    var values = $("#coa4>option").map(function () {
        return $(this).val();
    });
    var first = 0;
    var firstChoose = "";
    for (var a = 0; a < values.length; a++) {
        var tamp = values[a].split('---;---');
        var tamp2 = coa3.split('---;---');
        var coaParent = tamp2[0];
        var accID = tamp[0].substring(0, coaParent.length);
        if (accID == coaParent || tamp[0] == '0') {
            if (first == 0) {
                firstChoose = tamp[0];
                first++;
            }
            $("#" + tamp[0]).removeAttr('style', 'display:none;');
        } else {
            $("#" + tamp[0]).attr('style', 'display:none;');
        }
    }
    if (firstChoose == "0") {
        $("#coa4Empty").attr('selected', 'selected');
    } else {
        $("#" + firstChoose).attr('selected', 'selected');
    }
    $('#coa4').trigger("chosen:updated");
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
$("#btn-insert").click(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});
$("#coa1,#coa2,#coa3,#coa4,#coa5,#coa6").change(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});