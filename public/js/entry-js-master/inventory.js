var matched, browser;

jQuery.uaMatch = function( ua ) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
        /(msie) ([\w.]+)/.exec( ua ) ||
        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
        [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

matched = jQuery.uaMatch( navigator.userAgent );
browser = {};

if ( matched.browser ) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if ( browser.chrome ) {
    browser.webkit = true;
} else if ( browser.webkit ) {
    browser.safari = true;
}

jQuery.browser = browser;
window.updateAttach = function (element) {
    var data = $(element).data('all');
    $('#nameUpdate').val(searchData("InventoryName", data));
    $('#maxStockUpdate').val(addPeriodInventory(searchData("MaxStock", data), ","));
    $('#minStockUpdate').val(addPeriodInventory(searchData("MinStock", data), ","));
    $('#uomUpdate').val(searchData("UoM", data));
    $('#remarkUpdate').val(searchData("Remark", data));
    $('#idUpdate').val(searchData("InternalID", data));
    document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
    if (searchData("UserModified", data) == "0") {
        document.getElementById('modifiedDetail').innerHTML = '-';
    } else {
        document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
    }

    $("#type" + searchData("InventoryTypeInternalID", data)).attr('selected', 'selected');
    $('#typeUpdate').trigger("chosen:updated");
};
window.deleteAttach = function (element) {
    $('#idDelete').val($(element).data('internal'));
};
window.stockAttach = function (element) {
    $('#idStock').val($(element).data('internal'));
};
$('.btn-export').on('click', function () {
    if ($.browser.chrome) {
        for (var i = 0; i < Math.ceil(countInventory / 10000); i++) {
            chrome.tabs.create({
                url: exportExcel + "?skip=" + (i * 10000) + "&take=" + 10000
            });
        }
    } else {
        for (var i = 0; i < Math.ceil(countInventory / 10000); i++) {
            window.open(exportExcel + "?skip=" + (i * 10000) + "&take=" + 10000, '_blank');
        }
    }
});
function addPeriodInventory(nStr, add)
{
    nStr += '';
    x = nStr.split(add);
    x1 = x[0];
    x2 = x.length > 1 ? add + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + add + '$2');
    }
    return x1 + x2;
}
$(document).ready(function () {
    $("#maxStockUpdate,#minStockUpdate").blur(function () {
        if (removePeriod($("#maxStockUpdate").val(), ',') < removePeriod($("#minStockUpdate").val(), ',')) {
            $("#minStockUpdate").val($("#maxStockUpdate").val());
        }
    });
    $("#max,#min").blur(function () {
        if (removePeriod($("#max").val(), ',') < removePeriod($("#min").val(), ',')) {
            $("#min").val($("#max").val());
        }
    });
    function addPeriodInventory(nStr, add)
    {
        nStr += '';
        x = nStr.split(add);
        x1 = x[0];
        x2 = x.length > 1 ? add + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + add + '$2');
        }
        return x1 + x2;
    }
    $('#startDate').datepicker();
    $('#endDate').datepicker();
    $('#startDateStock').datepicker();
    $('#endDateStock').datepicker();
    $('#date').datepicker();
    $('#date1').datepicker();
    $("#startDate").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#endDate").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#startDateStock").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#endDateStock").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#date1").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#endDate, #startDate').change(function () {
        if ($('#startDate').val() == '') {
            $('#startDate').val($('#endDate').val());
        } else if ($('#endDate').val() == '') {
            $('#endDate').val($('#startDate').val());
        } else if (dateCheckHigher($('#startDate').val(), $('#endDate').val()) == 'start') {
            $('#endDate').val($('#startDate').val());
        }
    });
    $('#endDateStock, #startDateStock').change(function () {
        if ($('#startDateStock').val() == '') {
            $('#startDateStock').val($('#endDateStock').val());
        } else if ($('#endDateStock').val() == '') {
            $('#endDateStock').val($('#startDateStock').val());
        } else if (dateCheckHigher($('#startDateStock').val(), $('#endDateStock').val()) == 'start') {
            $('#endDateStock').val($('#startDateStock').val());
        }
    });
    $("#btn-detail-report").click(function () {
        if ($('#startDate').val() == '' && $('#endDate').val() == '') {
            var tanggal = new Date();
            var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
            $('#startDate').val(tanggalText);
            $('#endDate').val($('#startDate').val());
        } else if ($('#startDate').val() == '') {
            $('#startDate').val($('#endDate').val());
        } else if ($('#endDate').val() == '') {
            $('#endDate').val($('#startDate').val());
        } else if (dateCheckHigher($('#startDate').val(), $('#endDate').val()) == 'start') {
            $('#endDate').val($('#startDate').val());
        }
    });
    $("#btn-stock-report").click(function () {
        if ($('#startDateStock').val() == '' && $('#endDateStock').val() == '') {
            var tanggal = new Date();
            var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
            $('#startDateStock').val(tanggalText);
            $('#endDateStock').val($('#startDateStock').val());
        } else if ($('#startDateStock').val() == '') {
            $('#startDateStock').val($('#endDateStock').val());
        } else if ($('#endDateStock').val() == '') {
            $('#endDateStock').val($('#startDateStock').val());
        } else if (dateCheckHigher($('#startDateStock').val(), $('#endDateStock').val()) == 'start') {
            $('#endDateStock').val($('#startDateStock').val());
        }
    });
    $("#btn-summary-report").click(function () {
        if ($('#date').val() == '') {
            var tanggal = new Date();
            var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
            $('#date').val(tanggalText);
        }
    });
    $("#btn-buffer-stock-report").click(function () {
        if ($('#date1').val() == '') {
            var tanggal = new Date();
            var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
            $('#date1').val(tanggalText);
        }
    });

    $('#example').dataTable({
        "draw": 10,
        "processing": true,
        "serverSide": true,
        "ajax": inventoryDataBackup
    });

    $("#inventoryID").keypress(function (e) {
        if (e.keyCode == '34' || e.keyCode == '39') {
            e.preventDefault();
        }
    });
});
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#inventoryID').parent('div').append('<span class="help-block form-error" id="spanErrorID">Inventory ID has already been taken</span>');
    $('#inventoryID').parent('div').removeClass('has-success');
    $('#inventoryID').parent('div').addClass('has-error');
    $('#inventoryID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#inventoryID').parent('div').removeClass('has-error');
    $('#inventoryID').parent('div').addClass('has-success');
    $('#inventoryID').css("border-color", "");
}
function validationID(status) {
    var search = $('#inventoryID').val();
    if (search == '') {
        return false;
    } else {
   
        $.post(checkInventoryID, {id: search}).done(function (data) {
            if (data.trim() == "0") {
                sukses();
                if (status == 1) {
                    return true; //form submit
                }
            } else {
                gagal();
                if (status == 1) {
                    return false; // Will stop the submission of the form
                }
            }
        });
        
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
        validationID(1);
    },
    onSuccess: function () {
        return validationID(1);
    }
});
$("#inventoryID").blur(function () {
    validationID(0);
});