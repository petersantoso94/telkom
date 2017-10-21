$(document).ready(function () {
    //=================function=====================
    function changeTotal(id) {
        var price = document.getElementById(id).value;
        price = removePeriod(price, ',');
        var output = price * removePeriod($("#" + id + '-qty').val(), ',');
        //pengenapan price 2 angka belakang koma.
        output = Math.round(output * 100) / 100;
        output = addPeriodSales(output, ',');
        if (output.indexOf(".") == -1) {
            output = output + '.00';
        } else if (output.split('.')[1].length == 1) {
            output = output + '0';
        }
        document.getElementById(id + '-qty-hitung').innerHTML = output;
        hitungTotal();
    }
    function addPeriodSales(nStr, add) {
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

    function hitungTotal() {
        var total = 0;
        $(".subtotal").each(function (i) {
            var jumlah = document.getElementById($(this).attr('id')).innerHTML;
            total += parseFloat(removePeriod(jumlah, ','));
        });
        total = Math.round(total * 100) / 100;
        document.getElementById("grandTotal").innerHTML = addPeriodSales(total, ',');
        var tamp = total;
        $("#grandTotalValue").val(tamp);

        return total;
    }

    function autoChangeTotal() {
        //untuk cari total
        myFunctionduit();
        //currency berubah
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
                $("#rate").css('background-color', '#eee');
            } else {
                $("#rate").prop('readonly', false);
                $("#rate").css('background-color', '');
            }
            hitungTotal();
        });
        //add period tanpa remove
        function addPeriodSales(nStr, add)
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
        //change subtotal function
        function changeTotal(id) {
            var price = document.getElementById(id).value;
            price = removePeriod(price, ',');
            var output = price * removePeriod($("#" + id + '-qty').val(), ',');
            //pengenapan price 2 angka belakang koma.
            output = Math.round(output * 100) / 100;
            output = addPeriodSales(output, ',');
            if (output.indexOf(".") == -1) {
                output = output + '.00';
            } else if (output.split('.')[1].length == 1) {
                output = output + '0';
            }
            document.getElementById(id + '-qty-hitung').innerHTML = output;
            hitungTotal();
        }
        //kurs berubah
        $("#rate").keyup(function () {
            var rate = $("#rate").val();
            $(".price").each(function (i) {
                changeTotal($(this).attr('id'))
            });
            hitungTotal();
        });
        $(".price").keyup(function (e) {
            if ($(this).val() == '') {
            } else {
                changeTotal($(this).attr('id'));
            }
            hitungTotal();
        });
        $(".price").change(function (e) {
            if ($(this).val() == '') {
                $(this).val('0');
            }
            changeTotal($(this).attr('id'));
            hitungTotal();
        });
        $(".qty").keyup(function (e) {
            if ($(this).val() == '') {
            } else {
                changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4));
            }
            hitungTotal();
        });
        $(".qty").change(function (e) {
            if ($(this).val() == '') {
                $(this).val('0');
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4));
            hitungTotal();
        });
        $(".qty").blur(function (e) {
            if ($(this).val() == 0) {
                $(this).val('1');
            }
            changeTotal($(this).attr('id').substring(0, $(this).attr('id').length - 4));
            hitungTotal();
        });
        hitungTotal();
    }
    //====================================function====================================
    $("#btn-addRow").removeAttr("disabled");

    var dataUom;
    //get uom
    $.post(getUomThisInventory, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
        $.post(getHPPValueInventoryMemoIn, {date: $("#date").val(), inventory: $("#inventory-0").val(), uom: $("#uom-0").val()})
                .done(function (data) {
                    $("#price-0").val(addPeriod(data.trim(), ","));
                    changeTotal("price-0");
                    hitungTotal();
                });
    });

    //uom inventory
    $(".inventory").change(function () {
        var split = $(this).attr('id').split('-');
        var inventoryInternalID = $(this).val();
        $.post(getUomThisInventory, {id: $(this).val()}).done(function (data) {
            $("#uom-" + split[1]).html(data);
            $.post(getHPPValueInventoryMemoIn, {date: $("#date").val(), inventory: inventoryInternalID, uom: $("#uom-" + split[1]).val()})
                    .done(function (data) {
                        $("#price-" + split[1]).val(addPeriod(data.trim(), ","));
                        changeTotal("price-" + split[1]);
                        hitungTotal();
                    });
        });
    });

    $(".uom").change(function () {
        var tamp = $(this).attr("id");
        var id = tamp.split("-");
        $.post(getHPPValueInventoryMemoIn, {date: $("#date").val(), inventory: $("#inventory-" + id[1]).val(), uom: $(this).val()})
                .done(function (data) {
                    $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                    changeTotal("price-" + id[1]);
                    hitungTotal();
                });
    });
});