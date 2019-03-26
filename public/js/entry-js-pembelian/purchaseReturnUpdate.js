var baris = 1;
$(document).ready(function () {
    var config = {'.chosen-select': {}};
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $.validate({form: '#form-updatePurchase', onSuccess: function () {
            var zero = 0;
            $(".returnPurchase").each(function (i) {
                if (removePeriod($(this).val(), ',') > 0) {
                    zero = 1
                }
            });
            if (zero == 0) {
                alert('Purchase Return must have at least one inventory with return value more than zero.');
                return false
            } else {
                return true
            }
        }});
    hitungTotal()
});
$("#discountGlobal").keyup(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAddUpdate($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$("#discountGlobal").change(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAddUpdate($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$("#discountGlobal").blur(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAddUpdate($("#discountGlobalMax").val(), ','))
    }
    hitungTotal()
});
$(".quantityPurchase").blur(function () {
    hitungTotal()
});
function hitungTotal() {
    var total = 0;
    $(".quantityPurchase").each(function (i) {
        var id = $(this).attr("id");
        var idLoop = id.split("-");
        var subtotal = parseFloat(removePeriod(document.getElementById("subtotalPurchase-" + idLoop[1]).innerHTML, ','));
        var quantity = parseFloat(removePeriod(document.getElementById("qtyPurchase-" + idLoop[1]).innerHTML, ','));
        var hargaPerBarang = 0;
        if (subtotal == 0 && quantity == 0) {
            hargaPerBarang = 0;
        }
        else {
            hargaPerBarang = subtotal / quantity;
        }
        total += hargaPerBarang * removePeriod($(this).val(), ',');
    });
    document.getElementById("total").innerHTML = addPeriodPurchaseAddUpdate(total, ',');
    var grandTotal = total - removePeriod($("#discountGlobal").val(), ',');
    document.getElementById("grandTotal").innerHTML = addPeriodPurchaseAddUpdate(grandTotal, ',');
    var tax = 0;
    if ($("#taxPurchase").val() == 0) {
        tax = 0;
        document.getElementById("tax").innerHTML = addPeriodPurchaseAddUpdate(tax, ',')
    } else {
        tax = grandTotal * 0.1;
        document.getElementById("tax").innerHTML = addPeriodPurchaseAddUpdate(tax, ',')
    }
    document.getElementById("grandTotalAfterTax").innerHTML = addPeriodPurchaseAddUpdate(grandTotal + tax, ',');
    $("#grandTotalValue").val(removePeriod($("#grandTotalAfterTax").html(), ','))
}
function addPeriodPurchaseAddUpdate(nStr, add) {
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