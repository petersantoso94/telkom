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

    //====================function=========================
    function changeTotal(id) {
        var price = document.getElementById(id).value;
        price = removePeriod(price, ',');
        var diskon = $("#" + id + '-discount').val();
        var diskonNominal = removePeriod($("#" + id + '-discountNominal').val(), ',') * removePeriod($("#" + id + '-qty').val(), ',');
        var output = price * removePeriod($("#" + id + '-qty').val(), ',');
        diskon = diskon * output / 100;
        diskon = Math.round(diskon * 100) / 100;
        output = Math.round(output * 100) / 100;
        output = output - diskon - diskonNominal;
        output = Math.round(output * 100) / 100;
        output = addPeriodSales(output, ',');
        if (output.indexOf(".") == -1) {
            output = output + '.00';
        } else if (output.split('.')[1].length == 1) {
            output = output + '0';
        }
        document.getElementById(id + '-qty-hitung').innerHTML = output;
        hitungTotal()
    }
    function autoChangeTotal() {
        myFunctionduit();
        $("#currencyHeader").change(function () {
            var currencyHeader = $("#currencyHeader").val().split('---;---');
            $("#rate").val(addPeriod(currencyHeader[2], ','));
            var rate = $("#rate").val();
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            var currency = $("#currencyHeader").val().split('---;---');
            if (currency[3] == '1') {
                $("#rate").prop('readonly', true);
                $("#rate").css('background-color', '#eee')
            } else {
                $("#rate").prop('readonly', false);
                $("#rate").css('background-color', '')
            }
            hitungTotal()
        });
        function addPeriodSales(nStr, add) {
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
        function changeTotal(id) {
            var price = document.getElementById(id).value;
            price = removePeriod(price, ',');
            var diskon = $("#" + id + '-discount').val();
            var diskonNominal = removePeriod($("#" + id + '-discountNominal').val(), ',') * removePeriod($("#" + id + '-qty').val(), ',');
            var output = price * removePeriod($("#" + id + '-qty').val(), ',');
            diskon = diskon * output / 100;
            diskon = Math.round(diskon * 100) / 100;
            output = Math.round(output * 100) / 100;
            output = output - diskon - diskonNominal;
            output = Math.round(output * 100) / 100;
            output = addPeriodSales(output, ',');
            if (output.indexOf(".") == -1) {
                output = output + '.00';
            } else if (output.split('.')[1].length == 1) {
                output = output + '0';
            }
            document.getElementById(id + '-qty-hitung').innerHTML = output;
            hitungTotal()
        }
        $("#rate").keyup(function () {
            var rate = $("#rate").val();
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            hitungTotal()
        });
        $(".price").keyup(function (e) {
            if ($(this).val() == '') {
            } else {
                changeTotal($(this).attr('id'))
            }
            hitungTotal()
        });
        $(".price").change(function (e) {
            if ($(this).val() == '') {
            }
            changeTotal($(this).attr('id'));
            hitungTotal()
        });
        $(".qty").keyup(function (e) {
            if ($(this).val() == '') {
            } else {
                changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4))
            }
            hitungTotal()
        });
        $(".qty").change(function (e) {
            if ($(this).val() == '') {
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4));
            hitungTotal()
        });
        $(".qty").blur(function (e) {
            if ($(this).val() == 0) {
                $(this).val('1')
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4));
            hitungTotal()
        });
        $(".discount").keyup(function (e) {
            if ($(this).val() > 100) {
                $(this).val('100')
            }
            if ($(this).val() < 0) {
                $(this).val('0')
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 9));
            hitungTotal()
        });
        $(".discount").change(function (e) {
            if ($(this).val() == '') {
            }
            if ($(this).val() > 100) {
                $(this).val('100')
            }
            if ($(this).val() < 0) {
                $(this).val('0')
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 9));
            hitungTotal()
        });
        $(".discount").blur(function (e) {
            if ($(this).val() == 0) {
                $(this).val('0')
            }
            if ($(this).val() > 100) {
                $(this).val('100')
            }
            if ($(this).val() < 0) {
                $(this).val('0')
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 9));
            hitungTotal()
        });
        $(".discountNominal").keyup(function (e) {
            if ($(this).val() == '') {
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 16));
            hitungTotal()
        });
        $(".discountNominal").change(function (e) {
            if ($(this).val() == '') {
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 16));
            hitungTotal()
        });
        $(".discountNominal").blur(function (e) {
            if ($(this).val() == '') {
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 16));
            hitungTotal()
        });
        $("#discountGlobal").keyup(function (e) {
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            hitungTotal()
        });
        $("#discountGlobal").change(function (e) {
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            hitungTotal()
        });
        $("#discountGlobal").blur(function (e) {
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            hitungTotal()
        });
        hitungTotal()
    }

    function addPeriodSales(nStr, add) {
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
    function hitungTotal() {
        var total = 0;
        $(".subtotal").each(function (i) {
            var jumlah = document.getElementById($(this).attr('id')).innerHTML;
            total += parseFloat(removePeriod(jumlah, ','))
        });
        total = Math.round(total * 100) / 100;
        var diskon = removePeriod($("#discountGlobal").val(), ',');
        document.getElementById("total").innerHTML = addPeriodSales(total.toFixed(2), ',');

        if ($("#paymentCredit").val() == 1) {
            tampTotal = (total - diskon).toFixed(2);
        }

        document.getElementById("grandTotal").innerHTML = (addPeriodSales((total - diskon).toFixed(2), ','));
        var tamp = total - diskon;
        if ($("#taxSales").val() == 1) {
            var tax = tamp / 10;
            tax = Math.round(tax * 100) / 100;
            document.getElementById("tax").innerHTML = addPeriodSales(tax.toFixed(2), ',');
            var grandTotal = tamp + tax;
            grandTotal = Math.round(grandTotal * 100) / 100;
            document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSales(grandTotal.toFixed(2), ',');
            $("#grandTotalValue").val(grandTotal)
        } else {
            $("#grandTotalValue").val(tamp);
            document.getElementById("tax").innerHTML = '0';
            document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSales(tamp.toFixed(2), ',');
        }
        return total
    }
    //===================/function=========================

    autoChangeTotal();
    hitungTotal();
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
        form: '#form-insertSalesAdd',
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
});

$("#date").change(function () {
    var cariText = '';
    $('#salesID').load(cariS, {
        "date": $("#date").val()
    });
});

$("#discountGlobal").keyup(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").change(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','));
    }
    hitungTotal();
});
$("#discountGlobal").blur(function (e) {
    if ($(this).val() == '') {
    }
    if (parseFloat(removePeriod($(this).val(), ',')) > $("#discountGlobalMax").val()) {
        alert("Discount can not be higher than " + $("#discountGlobalMax").val());
        $(this).val(addPeriodSalesAdd($("#discountGlobalMax").val(), ','));
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

//perhitungan total


function addPeriodSalesAdd(nStr, add)
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