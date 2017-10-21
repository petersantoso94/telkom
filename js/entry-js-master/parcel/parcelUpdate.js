$(document).ready(function () {
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
        function gagalBarcode() {
            if ($('#spanErrorID')) {
                $('#spanErrorID').remove();
            }
            $('#barcodeCode').parent('li').append('<span class="help-block form-error" id="spanErrorID">Barcode code has already been taken</span>');
            $('#barcodeCode').parent('li').removeClass('has-success');
            $('#barcodeCode').parent('li').addClass('has-error');
            $('#barcodeCode').css("border-color", "rgb(169, 68, 66)");
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
        $("#barcodeCode").blur(function () {
            if (barcodeLama != $("#barcodeCode").val()) {
                $.post(checkBarcodeCode, {BarcodeCode: $("#barcodeCode").val()}).done(function (data) {
                    if (data == 0) {
                        sukses();
                    } else {
                        gagalBarcode();
                    }
                });
            }
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
        document.getElementById("grandTotal").innerHTML = addPeriodSales(total, ',');
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

    //uom inventory
    $(".inventory").change(function () {
        var split = $(this).attr('id').split('-');
        var inventoryInternalID = $(this).val();
        $.post(getUomThisInventoryParcel, {id: $(this).val()}).done(function (data) {
            $("#uom-" + split[1]).html(data);
            if (split[1] == 0) {
                $.post(getHPPValueInventoryParcel, {date: $("#headerDate").val(), inventory: inventoryInternalID, uom: $("#uom-" + split[1]).val()})
                        .done(function (data) {
                            $("#price-" + split[1]).val(addPeriod(data.trim(), ","));
                            changeTotal("price-" + split[1]);
                            hitungTotal();
                        });
            }
        });
    });

    $(".uom").change(function () {
        var tamp = $(this).attr("id");
        var id = tamp.split("-");
        $.post(getHPPValueInventoryParcel, {date: $("#date").val(), inventory: $("#inventory-" + id[1]).val(), uom: $(this).val()})
                .done(function (data) {
                    $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                    changeTotal("price-" + id[1]);
                    hitungTotal();
                });
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
    autoChangeTotal();
    hitungTotal();
    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
        hitungTotal()
    });
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
    $(".price").each(function (i) {
        changeTotal($(this).attr('id'))
    });

    function gagal() {
        if ($('#spanErrorID')) {
            $('#spanErrorID').remove();
        }
        $('#parcelID').parent('li').append('<span class="help-block form-error" id="spanErrorID">Parcel ID has already been taken</span>');
        $('#parcelID').parent('li').removeClass('has-success');
        $('#parcelID').parent('li').addClass('has-error');
        $('#parcelID').css("border-color", "rgb(169, 68, 66)");
    }
    function sukses() {
        $('#spanErrorID').remove();
        $('#parcelID').parent('li').removeClass('has-error');
        $('#parcelID').parent('li').addClass('has-success');
        $('#parcelID').css("border-color", "");
    }

//    $.validate({
//        form: '#form-update',
//        onError: function () {
//            $.post(checkParcelIDUpdate, {ParcelID: $("#parcelID").val()}).done(function (data) {
//                if (data == 0) {
//                    sukses();
//                    return true();
//                } else {
//                    gagal();
//                    return false();
//                }
//            });
//        },
//        onSuccess: function () {
//            $.post(checkParcelIDUpdate, {ParcelID: $("#parcelID").val()}).done(function (data) {
//                if (data == 0) {
//                    sukses();
//                    return true();
//                } else {
//                    gagal();
//                    return false();
//                }
//            });
//        }
//    });
    $("#btn-save").click(function () {
        if (barcodeLama == $("#barcodeCode").val()) {
            $("#form-update").submit();
        } else {
            $.post(checkBarcodeCode, {BarcodeCode: $("#barcodeCode").val()}).done(function (data) {
                if (data == 0) {
                    sukses();
                    $("#form-update").submit();
                } else {
                    gagalBarcode();
                }
            });
        }
    });

    var dataUom;
//get uom
    $.post(getUomThisInventoryParcel, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
    });
    $("#btn-addRow").click(function () {
        $('#table-parcel tr:last').after('<tr id="row' + baris + '">' +
                '<td class="chosen-uom">' +
                '<input type="hidden" class="inventory" style="width: 100px" id="inventory-' + baris + '" style="" name="inventory[]" value="' + $('#inventory-0').val() + '">' +
                $("#inventory-0 option[value='" + $("#inventory-0").val() + "']").text() +
                '</td>' +
                '<td>' +
                '<select id="uom-' + baris + '" name="uom[]" class="input-theme uom">' + $('#uom-0').html() + '</select>' +
                '</td>' +
                '<td class="text-right" width="10%">' + '<input type="text" class="maxWidth qty right" name="qty[]" maxlength="11" min="1" value="1" id="price-' + baris + '-qty">' + '</td>' +
                '<td class="text-right" width="10%">' + '<input type="text" class="maxWidth price right numajaDesimal" name="price[]" maxlength="" value="0.00" id="price-' + baris + '">' + '</td>' +
                '<td width="10%" class="right subtotal" id="price-' + baris + '-qty-hitung">' + '0.00' + '</td>' +
                '<td width="5%">' + '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' +
                '</tr>');
        $('#uom-' + baris).val($('#uom-0').val());
        $('#price-' + baris + '-qty').val($('#price-0-qty').val());
        $('#price-' + baris).val($('#price-0').val());
        $('#price-' + baris + '-qty-hitung').text($('#price-0-qty-hitung').text());
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
        $(".numajaDesimal").blur(function (e) {
            if ($(this).val() != '') {
                var value = removePeriod($(this).val(), ',');
                var hasil = parseFloat(value).toFixed(2);
                $(this).val(addPeriod(hasil, ','));
            }
            if (e.keyCode == 9) {
                $(this).select();
            }
        });
        //uom inventory
        $(".inventory").change(function () {
            var split = $(this).attr('id').split('-');
            $.post(getUomThisInventoryParcel, {id: $(this).val()}).done(function (data) {
                $("#uom-" + split[1]).html(data);
            });
        });
        $(".uom").change(function () {
            var tamp = $(this).attr("id");
            var id = tamp.split("-");
            $.post(getHPPValueInventoryParcel, {date: $("#date").val(), inventory: $("#inventory-" + id[1]).val(), uom: $(this).val()})
                    .done(function (data) {
                        $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                        changeTotal("price-" + id[1]);
                        hitungTotal();
                    });
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
});

$("#btn-new").click(function () {
    window.location.href = parcelNew;
});