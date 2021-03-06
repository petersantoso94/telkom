var baris = 1;
$(".autoTab").focus(function (e) {
    $(this).val("");
});
$(".autoTab").keyup(function (e) {
    var tamp = $(this).val();
    var length = document.getElementById($(this).attr('id')).maxLength;
    if (tamp.length == length)
    {
        var inputs = $(this).closest('form').find(':input');
        inputs.eq(inputs.index(this) + 1).focus();
    }
    $('#numberTax').val($('#numberTax1').val() + "." + $('#numberTax2').val() + "-" + $('#numberTax3').val() + "." + $('#numberTax4').val());
});
//document ready biasa
$(document).ready(function () {
    var config = {
        '.chosen-select': {}
    };
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $.validate({
        form: '#form-updateSalesAdd',
        onSuccess: function () {
            var zero = 0;
            $(".addSales").each(function (i) {
                if (removePeriod($(this).val(), ',') > 0) {
                    zero = 1;
                }
            });
            if (zero == 0) {
                alert('Sales must have at least one inventory with quantity value more than zero.');
                return false;
            } else {
                return true;
            }
        }
    });
    hitungTotal();
});

$("#discountGlobal").keyup(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAddUpdate($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").change(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAddUpdate($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").blur(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAddUpdate($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#dp").keyup(function (e) {
    if ($(this).val() == '') {
    }
    hitungTotal();
});
$("#dp").change(function (e) {
    if ($(this).val() == '') {
    }
    hitungTotal();
});
$("#dp").blur(function (e) {
    if ($(this).val() == '') {
    }
    hitungTotal();
});
$(".quantitySales").blur(function () {
    hitungTotal();
});

//perhitungan total
function hitungTotal() {
    var total = 0;
    $(".quantitySales").each(function (i) {
        var id = $(this).attr("id");
        var idLoop = id.split("-");
//        var subtotal = parseFloat(removePeriod(document.getElementById("subtotalSales-" + idLoop[1]).innerHTML, ','));
//        var quantity = parseFloat(removePeriod(document.getElementById("qtySales-" + idLoop[1]).innerHTML, ','));
        var subtotal = parseFloat($("#sub-total-" + idLoop[1]).val());
        var quantity = parseFloat(removePeriod(document.getElementById("qty-order-" + idLoop[1]).innerHTML, ','));

        var hargaPerBarang = 0;
        if (subtotal == 0 && quantity == 0) {
            hargaPerBarang = 0;
        }
        else {
            hargaPerBarang = subtotal / quantity;
        }
        total += hargaPerBarang * removePeriod($(this).val(), ',');
    });
    var dp = removePeriod($("#dp").val(), ',');
    document.getElementById("total").innerHTML = addPeriodSalesAddUpdate(total.toFixed(2), ',');
    var grandTotal = total - removePeriod($("#discountGlobal").val(), ',') - dp;
    document.getElementById("grandTotal").innerHTML = addPeriodSalesAddUpdate(grandTotal.toFixed(2), ',');
    var tax = 0;
    if ($("#taxSales").val() == 0) {
        tax = 0;
        document.getElementById("tax").innerHTML = addPeriodSalesAddUpdate(tax.toFixed(2), ',');
    } else {
        tax = grandTotal * 0.1;
        document.getElementById("tax").innerHTML = addPeriodSalesAddUpdate(tax.toFixed(2), ',');
    }
    document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSalesAddUpdate((grandTotal + tax).toFixed(2), ',');
    $("#grandTotalValue").val(removePeriod($("#grandTotalAfterTax").html(), ','));
}

function addPeriodSalesAddUpdate(nStr, add)
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