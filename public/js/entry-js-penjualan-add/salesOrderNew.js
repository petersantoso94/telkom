$(document).ready(function () {

    var tampTotal;
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

        if ($("#paymentCredit").attr('checked') == 'checked') {
            tampTotal = (total - diskon).toFixed(2);
            $.post(checkRecieveable, {total: tampTotal, coa6: $("#coa6").val()}).done(function (data) {
                var tamp = data.split('---;---');
                if (tamp[0] == "1") {
                    $("#alert_piutang").removeClass("none");
                    $("#hutang").html(addPeriod(tamp[2], ","));
                    $("#credit_limit").html(addPeriod(tamp[1], ","));
                } else {
                    $("#alert_piutang").addClass("none");
                }
            });
        }

        document.getElementById("grandTotal").innerHTML = (addPeriodSales((total - diskon).toFixed(2), ','));
        var tamp = total - diskon;
        if (document.getElementById("vat").checked == true) {
            var tax = tamp / 10;
            tax = Math.round(tax * 100) / 100;
            document.getElementById("tax").innerHTML = addPeriodSales(tax.toFixed(2), ',');
            var grandTotal = tamp + tax;
            grandTotal = Math.round(grandTotal * 100) / 100;
            document.getElementById("grandTotalAfterTax").innerHTML = addPeriodSales(grandTotal.toFixed(2), ',');
            $("#grandTotalValue").val(grandTotal)
        } else {
            $("#grandTotalValue").val(tamp);
            document.getElementById("tax").innerHTML = '';
            document.getElementById("grandTotalAfterTax").innerHTML = ''
        }
        return total
    }

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

    var baris = countBaris;
    /*
     var dataUom;
     //get uom
     $.post(getUomThisInventorySalesOrder, {id: $('#inventory-0').val()}).done(function (data2) {
     $("#uom-0").html(data2);
     dataUom = data2;
     
     //get price uom
     $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-0").val(), UomID: $("#uom-0").val(), Qty: $("#price-" + id[1]+'-qty').val(), urutan: 0}).done(function (data) {
     var arrData = data.split("---;---");
     if (arrData[0] != 0) {
     $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
     changeTotal($("#price-" + arrData[1]).attr("id"));
     }
     hitungTotal();
     });
     });
     */

    $("#coa6").change(function () {
        hitungTotal();
        $.post(checkRecieveable, {total: tampTotal, coa6: $("#coa6").val()}).done(function (data) {
            var tamp = data.split('---;---');
            if (tamp[0] == "1") {
                $("#alert_piutang").removeClass("none");
                $("#hutang").html(addPeriod(tamp[2], ","));
                $("#credit_limit").html(addPeriod(tamp[1], ","));
            } else {
                $("#alert_piutang").addClass("none");
            }
        });
    });
    $("#paymentCredit").change(function () {
        hitungTotal();
        $.post(checkRecieveable, {total: tampTotal, coa6: $("#coa6").val()}).done(function (data) {
//            alert(data);
            var tamp = data.split('---;---');
            if (tamp[0] == "1") {
                $("#alert_piutang").removeClass("none");
                $("#hutang").html(addPeriod(tamp[2], ","));
                $("#credit_limit").html(addPeriod(tamp[1], ","));
            } else {
                $("#alert_piutang").addClass("none");
            }
        });
    });
    $("#paymentCash").change(function () {
        $("#alert_piutang").addClass("none");
    });

    $(".inventory").change(function () {
        var id = $(this).attr("id").split("-");
        var tampVar = $(this).find('option:selected').attr('id');
        if (tampVar.indexOf("inventory") >= 0) {
            //get uom
            $.post(getUomThisInventory, {id: $('#inventory-' + id[1]).val()}).done(function (data2) {
                $("#uom-" + id[1]).html(data2);
                //get price range
                $.post(getPriceRangeSO, {InventoryInternalID: $("#inventory-" + id[1]).val()}).done(function (price) {
                    document.getElementById("inventory-" + id[1]).setAttribute("data-content", price);
                    document.getElementById("inventory-" + id[1]).title = "Price Range";
                    $("#inventory-" + id[1]).popover({trigger: 'hover'})
                    //get stock
                    $.post(getStockInventorySO, {InventoryInternalID: $("#inventory-" + id[1]).val()}).done(function (stock) {
                        var split = stock.split("---;---");
                        $("#price-" + id[1] + "-stock").html(split[0]);
                        document.getElementById("price-" + id[1] + "-stock").setAttribute("data-content", "Inventory stock remains : " + split[0] + " <br/> Similar Product : <br/>" + split[1]);
                        document.getElementById("price-" + id[1] + "-stock").title = "Information Inventory";
                        $("#price-" + id[1] + "-stock").popover({trigger: 'hover'})

                        //get price uom
                        $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                            var arrData = data.split("---;---");
                            if (arrData[0] != 0) {
                                $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                                changeTotal($("#price-" + arrData[1]).attr("id"));
                            }
                            hitungTotal();
                        });
                    });
                });
            });
        } else if (tampVar.indexOf("parcel") >= 0) {
//            alert('das');
            //found parcel
            $("#price-" + id[1] + "-stock").html("-");
            $("#uom-" + id[1]).attr("readonly", true);
            $("#uom-" + id[1]).html("<option value='0'>-</option>");
            $("#uom-" + id[1]).trigger("chosen:updated");
            var value = $(this).val().split("---;---");
            //get price parcel
            $.post(getPriceThisParcel, {id: value[0]}).done(function (data) {
                $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                changeTotal($("#price-" + id[1]).attr("id"));
            });
        }

    });

    $(".uom").change(function () {
        var id = $(this).attr("id").split("-");
        var tampVar = $("#inventory-" + id[1]).val();
        if (tampVar.indexOf("inventory") >= 0) {
            //get price uom
            $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                var arrData = data.split("---;---");
                if (arrData[0] != 0) {
                    $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                    changeTotal($("#price-" + arrData[1]).attr("id"));
                }
                hitungTotal();
            });
        }
    });
    $(".qty").keyup(function () {
        var id = $(this).attr("id").split("-");
        var tampVar = $("#inventory-" + id[1]).val();
        if (tampVar.indexOf("inventory") >= 0) {
            //get price uom
            $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                var arrData = data.split("---;---");
                if (arrData[0] != 0) {
                    $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                    changeTotal($("#price-" + arrData[1]).attr("id"));
                }
                hitungTotal();
            });
        }
    });

    $("#btn-addRow").click(function () {
        var cur = $('#currencyHeader').val().split('---;---');
        $('#table-salesOrder tr:last').after('<tr id="row' + baris + '">' +
                '<td class="chosen-uom" >' +
                '<div id="price-range-' + baris + '">' +
                '<input type="hidden" class="inventory" style="width: 100px" id="inventory-' + baris + '" style="" name="inventory[]" value="' + $('#inventory-0').val() + '">' +
                $("#inventory-0 option[value='" + $("#inventory-0").val() + "']").text() +
                '</div></td>' +
                '<td>' +
                '<select id="uom-' + baris + '" name="uom[]" class="input-theme uom">' + $('#uom-0').html() + '</select>' +
                '</td>' +
                '<td class="text-right">' +
                '<span class="maxWidth stock" disabled=""  id="price-' + baris + '-stock">' +
                '</td>' +
                '<td class="text-right">' +
                '<input type="text" class="maxWidth qty right input-theme" name="qty[]" maxlength="11" min="1" value="1" id="price-' + baris + '-qty">' +
                '</td>' +
                '<td>' +
                '<input type="text" class="maxWidth price right numajaDesimal input-theme" name="price[]" maxlength="" value="0.00" id="price-' +
                baris + '">' + '</td>'
                + '<td class="text-right">' +
                '<input type="text" class="maxWidth discount right input-theme numajaDesimal" name="discount[]" min="0" max="100" id="price-' + baris + '-discount" value="0.00">' +
                '</td>' +
                '<td class="text-right">' +
                '<input type="text" class="maxWidth discountNominal right numajaDesimal input-theme" name="discountNominal[]" id="price-' + baris + '-discountNominal" value="0.00">' +
                '</td>' +
                '<td class="right subtotal" id="price-' + baris + '-qty-hitung">' + '0.00' + '</td>' + '<td>' +
                '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' +
                '</tr>');
        $('#uom-' + baris).val($('#uom-0').val());
        var tampVar = $("#inventory-0").find('option:selected').attr('id');
        $("#price-" + baris + "-stock").html($("#price-0-stock").html());
        if (tampVar.indexOf("inventory") >= 0) {
            //tooltip
            $("#price-" + baris + "-stock").attr("data-html", true);
            $("#price-" + baris + "-stock").attr("data-placement", "bottom");
            document.getElementById("price-" + baris + "-stock").setAttribute("data-content", $("#price-0-stock").attr("data-content"));
            document.getElementById("price-" + baris + "-stock").title = "Information Inventory";
            $("#price-" + baris + "-stock").popover({trigger: 'hover'});
            //tooltip

            //tooltip price range
            $("#price-range-" + baris).attr("data-html", true);
            $("#price-range-" + baris).attr("data-placement", "bottom");
            document.getElementById("price-range-" + baris).setAttribute("data-content", $("#inventory-0").attr("data-content"));
            document.getElementById("price-range-" + baris).title = "Price Range";
            $("#price-range-" + baris).popover({trigger: 'hover'});
            //tooltip price range
        }

        $('#price-' + baris + '-qty').val($('#price-0-qty').val());
        $('#price-' + baris).val($('#price-0').val());
        $('#price-' + baris + '-discount').val($('#price-0-discount').val());
        $('#price-' + baris + '-discountNominal').val($('#price-0-discountNominal').val());
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
        });

        //get price uom
//        $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + baris).val(), UomID: $("#uom-" + baris).val(), Qty: $("#price-" + id[1]+'-qty').val(), urutan: baris}).done(function (data) {
//            var arrData = data.split("---;---");
//            if (arrData[0] != 0) {
//                $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
//                changeTotal($("#price-" + arrData[1]).attr("id"));
//            }
//            hitungTotal();
//        });

        $(".inventory").change(function () {
            var id = $(this).attr("id").split("-");
            var tampVar = $(this).find('option:selected').attr('id');
            if (tampVar.indexOf("inventory") >= 0) {
                //get uom
                $.post(getUomThisInventory, {id: $('#inventory-' + id[1]).val()}).done(function (data2) {
                    $("#uom-" + id[1]).html(data2);
                    //get price range
                    $.post(getPriceRangeSO, {InventoryInternalID: $("#inventory-" + id[1]).val()}).done(function (price) {
                        document.getElementById("inventory-" + id[1]).setAttribute("data-content", price);
                        document.getElementById("inventory-" + id[1]).title = "Price Range";
                        $("#inventory-" + id[1]).popover({trigger: 'hover'})
                        //get stock
                        $.post(getStockInventorySO, {InventoryInternalID: $("#inventory-" + id[1]).val()}).done(function (stock) {
                            var split = stock.split("---;---");
                            $("#price-" + id[1] + "-stock").html(split[0]);
                            document.getElementById("price-" + id[1] + "-stock").setAttribute("data-content", "Inventory stock remains : " + split[0] + " <br/> Similar Product : <br/>" + split[1]);
                            document.getElementById("price-" + id[1] + "-stock").title = "Information Inventory";
                            $("#price-" + id[1] + "-stock").popover({trigger: 'hover'})
                            //get price uom
                            $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                                var arrData = data.split("---;---");
                                if (arrData[0] != 0) {
                                    $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                                    changeTotal($("#price-" + arrData[1]).attr("id"));
                                }
                                hitungTotal();
                            });
                        });
                    });
                });
            } else if (tampVar.indexOf("parcel") >= 0) {
                //found parcel
                $("#uom-" + id[1]).attr("readonly", true);
                $("#uom-" + id[1]).html("<option value='0'>-</option>");
                $("#uom-" + id[1]).trigger("chosen:updated");
                var value = $(this).val().split("---;---");
                //get price parcel
                $.post(getPriceThisParcel, {id: value[0]}).done(function (data) {
                    $("#price-" + id[1]).val(addPeriod(data.trim(), ","));
                    changeTotal($("#price-" + id[1]).attr("id"));
                });
            }
        });

        $(".uom").change(function () {
            var id = $(this).attr("id").split("-");

            var tampVar = $("#inventory-" + id[1]).val();
            if (tampVar.indexOf("inventory") >= 0) {
                //get price uom
                $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                    var arrData = data.split("---;---");
                    if (arrData[0] != 0) {
                        $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                        changeTotal($("#price-" + arrData[1]).attr("id"));
                    }
                    hitungTotal();
                });
            }
        });
        $(".qty").keyup(function () {
            var id = $(this).attr("id").split("-");
            var tampVar = $("#inventory-" + id[1]).val();
            if (tampVar.indexOf("inventory") >= 0) {
                //get price uom
                $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-" + id[1]).val(), UomID: $("#uom-" + id[1]).val(), Qty: $("#price-" + id[1] + '-qty').val(), urutan: id[1]}).done(function (data) {
                    var arrData = data.split("---;---");
                    if (arrData[0] != 0) {
                        $("#price-" + arrData[1]).val(addPeriodSales(arrData[0].trim(), ","));
                        changeTotal($("#price-" + arrData[1]).attr("id"));
                    }
                    hitungTotal();
                });
            }
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
        $("#searchInventory").val("");
        $("#searchInventory").attr("tabindex",-1).focus();
        baris++;
        autoChangeTotal()
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
    $.validate();
    $("#date").change(function () {
        var cariText = '';
        $('#salesOrderID').load(cariS, {"date": $("#date").val()})
    });
    $(function () {
        $('#vat').change(function () {
            $('.hidevat').toggle(this.checked)
        }).change()
    });
});