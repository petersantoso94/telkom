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
    $('#date').datepicker();
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#date').val(tanggalHariIni);
    var config = {
        '.chosen-select': {}
    };
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $.validate({
        form: '#form-insertPurchase',
        onSuccess: function () {
            var zero = 0;
            $(".Purchase").each(function (i) {
                if (removePeriod($(this).val(), ',') > 0) {
                    zero = 1;
                }
            });
            if (zero == 0) {
                alert('Purchase must have at least one inventory with value more than zero.');
                return false;
            } else {
                return true;
            }
        }
    });
});

$("#date").change(function () {
    var cariText = '';
    $('#purchaseID').load(a, {
        "date": $("#date").val()
    });
});

$("#discountGlobal").keyup(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAdd($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").change(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAdd($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").blur(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodPurchaseAdd($("#discountGlobalMax").val(), ','));
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
$(".quantityPurchase").blur(function () {
    hitungTotal();
});

//perhitungan total
function hitungTotal() {
    var total = 0;
    $(".quantityPurchase").each(function (i) {
        var id = $(this).attr("id");
        var idLoop = id.split("-");
        var subtotal = parseFloat(removePeriod(document.getElementById("subtotalPurchase-" + idLoop[1]).innerHTML, ','));
        var quantity = parseFloat(removePeriod(document.getElementById("qtyPurchase-" + idLoop[1]).innerHTML, ','));
        var hargaPerBarang = subtotal / quantity;
        total += hargaPerBarang * removePeriod($(this).val(), ',');
    });
    var dp = removePeriod($("#dp").val(), ',');
    document.getElementById("total").innerHTML = addPeriodPurchaseAdd(total.toFixed(2), ',');
    var grandTotal = total - removePeriod($("#discountGlobal").val(), ',') - dp;
    document.getElementById("grandTotal").innerHTML = addPeriodPurchaseAdd(grandTotal.toFixed(2), ',');
    var tax = 0;
    if ($("#taxPurchase").val() == 0) {
        tax = 0;
        document.getElementById("tax").innerHTML = addPeriodPurchaseAdd(tax.toFixed(2), ',');
    } else {
        tax = grandTotal * 0.1;
        document.getElementById("tax").innerHTML = addPeriodPurchaseAdd(tax.toFixed(2), ',');
    }
    document.getElementById("grandTotalAfterTax").innerHTML = addPeriodPurchaseAdd((grandTotal + tax).toFixed(2), ',');
    $("#grandTotalValue").val(removePeriod($("#grandTotalAfterTax").html(), ','));
}

function addPeriodPurchaseAdd(nStr, add)
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