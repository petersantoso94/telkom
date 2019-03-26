var baris = 1;
$(document).ready(function () {
    $('#date').datepicker();
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#date').val(tanggalHariIni);
    var config = {'.chosen-select': {}};
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $.validate({form: '#form-insertSales', onSuccess: function () {
            var zero = 0;
            $(".returnSales").each(function (i) {
                if (removePeriod($(this).val(), ',') > 0) {
                    zero = 1
                }
            });
            if (zero == 0) {
                alert('Sales Return must have at least one inventory with return value more than zero.');
                return false
            } else {
                return true
            }
        }})
});
$("#discountGlobal").keyup(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$("#discountGlobal").change(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$("#discountGlobal").blur(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$(".quantitySales").blur(function () {
    hitungTotal()
});
function hitungTotal() {
    var total = 0;
    $(".quantitySales").each(function (i) {
        var id = $(this).attr("id");
        var idLoop = id.split("-");
        var subtotal = parseFloat(removePeriod(document.getElementById("subtotalSales-" + idLoop[1]).innerHTML, ','));
        var quantity = parseFloat(removePeriod(document.getElementById("qtySales-" + idLoop[1]).innerHTML, ','));
        var hargaPerBarang = subtotal / quantity;
        total += hargaPerBarang * removePeriod($(this).val(), ',');
    });
    document.getElementById("total").innerHTML = addPeriodSalesAdd(total, ',');
    var grandTotal = total - removePeriod($("#discountGlobal").val(), ',');
    document.getElementById("grandTotal").innerHTML = addPeriodSalesAdd(grandTotal, ',');
    var tax = 0;
    if ($("#taxSales").val() == 0) {
        tax = 0;
        document.getElementById("tax").innerHTML = addPeriodSalesAdd(tax, ',')
    } else {
        tax = grandTotal * 0.1;
        document.getElementById("tax").innerHTML = addPeriodSalesAdd(tax, ',')
    }
    document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSalesAdd(grandTotal + tax, ',');
    $("#grandTotalValue").val(removePeriod($("#grandTotalAfterTax").html(), ','))
}
function addPeriodSalesAdd(nStr, add) {
    nStr += '';
    x = nStr.split(add);
    x1 = x[0];
    x2 = x.length > 1 ? add + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + add + '$2')
    }
    return x1 + x2
}