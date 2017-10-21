$(document).ready(function () {
    //=================function=====================
    function changeTotal(id) {
        var price = document.getElementById(id).value;
        price = removePeriod(price, ',');
        var output = price * removePeriod($("#" + id + '-qty').val(), ',');
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
            var output = price * removePeriod($("#" + id + '-qty').val(), ',');
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
        $(".price").keyup(function (e) {
            if ($(this).val() == '') {
            } else {
                changeTotal($(this).attr('id'))
            }
            hitungTotal()
        });
        $(".price").change(function (e) {
            if ($(this).val() == '') {
                $(this).val('0')
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
                $(this).val('0')
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
        hitungTotal()
    }
    function hitungTotal() {
        var total = 0;
        $(".subtotal").each(function (i) {
            var jumlah = document.getElementById($(this).attr('id')).innerHTML;
            total += parseFloat(removePeriod(jumlah, ','))
        });
        total = Math.round(total * 100) / 100;
        document.getElementById("grandTotal").innerHTML = addPeriodSales(total.toFixed(2), ',');
        var tamp = total;
        $("#grandTotalValue").val(tamp);
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
    //====================================function====================================
    $("#btn-addRow").removeAttr("disabled");

    var dataUom;
    //get uom
    $.post(getUomThisInventoryParcel, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
        $.post(getHPPValueInventoryParcel, {date: tanggalHariIni, inventory: $("#inventory-0").val(), uom: $("#uom-0").val()})
                .done(function (data) {
                    $("#price-0").val(addPeriod(data.trim(), ","));
                    changeTotal("price-0");
                    hitungTotal();
                });
    });
});