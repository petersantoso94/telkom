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
    $('.appd').find('a.chosen-single').each(function () {
        $(this).addClass('chosenapp');
        var added = $(this).after().addClass('chosenapp');
        added++;
        var end = $('td.appd:last').children().find('select').addClass('chosenapp');
        end++;
    });
    $('#date').datepicker();
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#date').val(tanggalHariIni);
    autoChangeTotal();
    hitungTotal();
    var currency = $("#currencyHeader").val().split('---;---');
    if (currency[3] == '1') {
        $("#rate").prop('readonly', true);
        $("#rate").css('background-color', '#eee');
    } else {
        $("#rate").prop('readonly', false);
        $("#rate").css('background-color', '');
    }

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
    $("#btn-save").click(function () {
        var countIn = 0;
        var countOut = 0;
        $(".type").each(function () {
            var id = $(this).attr("id").split("-");
            if (id[1] != 0) {
                if ($("#type-" + id[1]).val() == "OUT") {
                    countOut++;
                } else {
                    countIn++;
                }
            }
        });
        if (countIn != 0 && countOut != 0) {
            $("#form-insert").submit();
        } else {
            alert("Inventory Out or In must exist!");
        }
    });

    /*
     var dataUom;
     //get uom
     $.post(getUomThisInventory, {id: $('#inventory-0').val()}).done(function (data2) {
     $("#uom-0").html(data2);
     dataUom = data2;
     $.post(getHPPValueInventoryTransformation, {date: $("#date").val(), inventory: $("#inventory-0").val(), uom: $("#uom-0").val()})
     .done(function (data) {
     $("#price-0").val(addPeriod(data.trim(), ","));
     changeTotal("price-0");
     hitungTotal();
     });
     });
     */

    //uom inventory
    $(".inventory").change(function () {
        var split = $(this).attr('id').split('-');
        var inventoryInternalID = $(this).val();
        $.post(getUomThisInventory, {id: $(this).val()}).done(function (data) {
            $("#uom-" + split[1]).html(data);
            $.post(getHPPValueInventoryTransformation, {date: $("#date").val(), inventory: inventoryInternalID, uom: $("#uom-" + split[1]).val()})
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
        $.post(getHPPValueInventoryTransformation, {date: $("#date").val(), inventory: $("#inventory-" + id[1]).val(), uom: $(this).val()})
                .done(function (data) {
                    $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                    changeTotal("price-" + id[1]);
                    hitungTotal();
                });
    });

    var baris = 1;
    //Insert row waktu button add
    $("#btn-addRow").click(function () {
        var cur = $('#currencyHeader').val().split('---;---');
        $('#table-transformation tr:last').after('<tr id="row' + baris + '">' +
                '<td class="">' +
                '<input type="hidden" class="inventory" style="width: 100px" id="inventory-' + baris + '" style="" name="inventory[]" value="' + $('#inventory-0').val() + '">' +
                $("#inventory-0 option[value='" + $("#inventory-0").val() + "']").text() +
                '</td>' +
                '<td>' +
                '<select id="uom-' + baris + '" name="uom[]" class="input-theme uom">' + $('#uom-0').html() + '</select>' +
                '</td>' +
                '<td class="text-right" width="10%">' +
                '<input type="text" class="maxWidth qty right" name="qty[]" maxlength="11" min="1" value="1" id="price-' + baris + '-qty">' +
                '</td>' +
                '<td class="text-right" width="10%">' +
                '<input type="text" class="maxWidth price right numajaDesimal" name="price[]" maxlength="" value="0.00" id="price-' + baris + '">' +
                '</td>' +
                '<td width="10%" class="right subtotal" id="price-' + baris + '-qty-hitung">' +
                '0.00' +
                '</td>' +
                '<td width="10%" class="center" >' +
                '<input type="text" class="type" readonly="" value="" name="Type[]" id="type-' + baris + '"> ' +
                '</td>' +
                '<td width="5%">' +
                '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' +
                '</td>' +
                '</tr>'
                );
        $('#uom-' + baris).val($('#uom-0').val());
        $('#price-' + baris + '-qty').val($('#price-0-qty').val());
        $('#price-' + baris).val($('#price-0').val());
        $('#price-' + baris + '-qty-hitung').text($('#price-0-qty-hitung').text());
        $('#type-' + baris).val($('#type-0').val());
        //button delete row
        $(".btn-deleteRow").click(function () {
            if ($('#' + $(this).attr('data')).length > 0) {
                document.getElementById($(this).attr('data')).remove();
            }
            hitungTotal();
        });
        $(".numajaDesimal").keypress(function (e) {
            if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 46))
                return true;
            else
                return false;
        });

        //uom inventory
        $(".inventory").change(function () {
            var split = $(this).attr('id').split('-');
            var inventoryInternalID = $(this).val();
            $.post(getUomThisInventory, {id: $(this).val()}).done(function (data) {
                $("#uom-" + split[1]).html(data);
                $.post(getHPPValueInventoryTransformation, {date: $("#date").val(), inventory: inventoryInternalID, uom: $("#uom-" + split[1]).val()})
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
            $.post(getHPPValueInventoryTransformation, {date: $("#date").val(), inventory: $("#inventory-" + id[1]).val(), uom: $(this).val()})
                    .done(function (data) {
                        $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                        changeTotal("price-" + id[1]);
                        hitungTotal();
                    });
        });

        //select untuk element baru
        var config = {
            '.chosen-select': {}};
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
            end++;
        });
        $("#searchInventory").val("");
        $("#searchInventory").attr("tabindex",-1).focus();
        baris++;

        //function utk element baru
        autoChangeTotal();
    });

//semua yang merupakan auto change
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

//perhitungan total debet dan kredit
    function hitungTotal() {
        var totalIn = 0;
        var totalOut = 0;
        $(".subtotal").each(function (i) {
            var jumlahIn = 0;
            var jumlahOut = 0;
            var diSplit = $(this).attr('id').split("-");
            if ($("#type-" + diSplit[1]).val() == "OUT")
                jumlahOut = document.getElementById($(this).attr('id')).innerHTML;
            else
                jumlahIn = document.getElementById($(this).attr('id')).innerHTML;

            totalIn += parseFloat(removePeriod(jumlahIn, ','));
            totalOut += parseFloat(removePeriod(jumlahOut, ','));
        });
        totalIn = Math.round(totalIn * 100) / 100;
        totalOut = Math.round(totalOut * 100) / 100;
        document.getElementById("grandTotalIn").innerHTML = addPeriodSales(totalIn, ',');
        document.getElementById("grandTotalOut").innerHTML = addPeriodSales(totalOut, ',');
        var tampIn = totalIn;
        var tampOut = totalOut;
        $("#grandTotalValueIn").val(tampIn);
        $("#grandTotalValueOut").val(tampOut);
        var total = totalIn + totalOut;
        return total;
    }
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
});