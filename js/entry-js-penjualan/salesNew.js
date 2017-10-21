var baris = 1;
$(".autoTab").focus(function (e) {
    $(this).val("")
});
$(".autoTab").keyup(function (e) {
    var tamp = $(this).val();
    var length = document.getElementById($(this).attr('id')).maxLength;
    if (tamp.length == length) {
        var inputs = $(this).closest('form').find(':input');
        inputs.eq(inputs.index(this) + 1).focus()
    }
    $('#numberTax').val($('#numberTax1').val() + "." + $('#numberTax2').val() + "-" + $('#numberTax3').val() + "." + $('#numberTax4').val())
});
$("#btn-addRow").click(function () {
    var cur = $('#currencyHeader').val().split('---;---');
    $('#table-sales tr:last').after('<tr id="row' + baris + '">' + '<td class="chosen-transaction">' + textSelect + '</td>' + '<td class="text-right">' + '<input type="text" class="maxWidth qty right input-theme" name="qty[]" maxlength="11" min="1" value="1" id="price-' + baris + '-qty">' + '</td>' + '<td class="text-right">' + '<input type="text" class="maxWidth price right numajaDesimal input-theme" name="price[]" maxlength="" value="0.00" id="price-' + baris + '">' + '</td>' + '<td class="text-right">' + '<input type="text" class="maxWidth discount right input-theme numajaDesimal" name="discount[]" min="0" max="100" id="price-' + baris + '-discount" value="0.00">' + '</td>' + '<td class="text-right">' + '<input type="text" class="maxWidth discountNominal right input-theme numajaDesimal" name="discountNominal[]" id="price-' + baris + '-discountNominal" value="0.00">' + '</td>' + '<td class="right subtotal" id="price-' + baris + '-qty-hitung">' + '0.00' + '</td>' + '<td>' + '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' + '</tr>');
    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
        hitungTotal()
    });
    $(".numajaDesimal").keypress(function (e) {
        if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 46))
            return true;
        else
            return false
    });
    var config = {'.chosen-select': {}};
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $('.appd').find('a.chosen-single').each(function () {
        $(this).addClass('chosenapp');
        var added = $(this).after().addClass('chosenapp');
        added++;
        var end = $('td.appd:last').children().find('select').addClass('chosenapp');
        end++
    });
    baris++;
    autoChangeTotal()
});
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
        var diskonNominal = removePeriod($("#" + id + '-discountNominal').val(), ',') * removePeriod($("#" + id + '-qty').val(), ',');
        var diskon = $("#" + id + '-discount').val();
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
    $("#dp").keyup(function (e) {
        $(".price").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        hitungTotal()
    });
    $("#dp").change(function (e) {
        $(".price").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        hitungTotal()
    });
    $("#dp").blur(function (e) {
        $(".price").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        hitungTotal()
    });
    hitungTotal()
}
function hitungTotal() {
    var total = 0;
    $(".subtotal").each(function (i) {
        var jumlah = document.getElementById($(this).attr('id')).innerHTML;
        total += parseFloat(removePeriod(jumlah, ','))
    });
    total = Math.round(total * 100) / 100;
    var diskon = removePeriod($("#discountGlobal").val(), ',');
    var dp = removePeriod($("#dp").val(), ',');
    document.getElementById("total").innerHTML = addPeriodSales(total.toFixed(2), ',');
    document.getElementById("grandTotal").innerHTML = (addPeriodSales((total - diskon - dp).toFixed(2), ','));
    var tamp = total - diskon - dp;
    if (document.getElementById("vat").checked == true) {
        var tax = tamp / 10;
        tax = Math.round(tax * 100) / 100;
        document.getElementById("tax").innerHTML = addPeriodSales(tax.toFixed(2), ',');
        var grandTotal = tamp + tax;
        grandTotal = Math.round(grandTotal * 100) / 100;
        document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSales(grandTotal.toFixed(2), ',');
        $("#grandTotalValue").val(grandTotal.toFixed(2))
    } else {
        $("#grandTotalValue").val(tamp.toFixed(2));
        document.getElementById("tax").innerHTML = '';
        document.getElementById("grandTotalAfterTax").innerHTML = ''
    }
    return total
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
$(document).ready(function () {
    var config = {'.chosen-select': {}};
    for (var selector in config) {
        $(selector).chosen({
            search_contains: true
        });
    }
    $('.appd').find('a.chosen-single').each(function () {
        $(this).addClass('chosenapp');
        var added = $(this).after().addClass('chosenapp');
        added++;
        var end = $('td.appd:last').children().find('select').addClass('chosenapp');
        end++
    });
    $('#date').datepicker();
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#date').val(tanggalHariIni);
    autoChangeTotal();
    hitungTotal();
    $("#vat").change(function (e) {
        hitungTotal()
    });
    $("#paymentCash,#paymentCredit").change(function () {
        if ($('#paymentCash').attr('checked') == 'checked') {
            $('#paymentCash').removeAttr('checked');
            $('#paymentCredit').attr('checked', 'checked');
            $('#longTerm').prop('disabled', false);
            $('#slip').prop('disabled', true);
            $('#slip').trigger("chosen:updated")
        } else {
            $('#paymentCredit').removeAttr('checked');
            $('#paymentCash').attr('checked', 'checked');
            $('#longTerm').val(0);
            $('#longTerm').prop('disabled', true);
            $('#slip').prop('disabled', false);
            $('#slip').trigger("chosen:updated")
        }
    });
    $("#longTerm").blur(function () {
        if ($('#longTerm').val() == '' || $('#longTerm').val() < 0) {
            $('#longTerm').val(0)
        }
    });
    var currency = $("#currencyHeader").val().split('---;---');
    if (currency[3] == '1') {
        $("#rate").prop('readonly', true);
        $("#rate").css('background-color', '#eee')
    } else {
        $("#rate").prop('readonly', false);
        $("#rate").css('background-color', '')
    }
});
$.validate();
$("#date").change(function () {
    var cariText = '';
    $('#salesID').load(cariS, {"date": $("#date").val()})
});
$(function () {
    $('#vat').change(function () {
        $('.hidevat').toggle(this.checked)
    }).change()
});