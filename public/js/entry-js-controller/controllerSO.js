$(document).ready(function () {
    //=================function=====================
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
        output = addPeriod(output, ',');
        if (output.indexOf(".") == -1) {
            output = output + '.00';
        } else if (output.split('.')[1].length == 1) {
            output = output + '0';
        }
        document.getElementById(id + '-qty-hitung').innerHTML = output;
        hitungTotal()
    }
    function addPeriod(nStr, add) {
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
        output = addPeriod(output, ',');
        if (output.indexOf(".") == -1) {
            output = output + '.00';
        } else if (output.split('.')[1].length == 1) {
            output = output + '0';
        }
        document.getElementById(id + '-qty-hitung').innerHTML = output;
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
        document.getElementById("total").innerHTML = addPeriod(total.toFixed(2), ',');
        document.getElementById("grandTotal").innerHTML = (addPeriod((total - diskon).toFixed(2), ','));
        var tamp = total - diskon;
        if (document.getElementById("vat").checked == true) {
            var tax = tamp / 10;
            tax = Math.round(tax * 100) / 100;
            document.getElementById("tax").innerHTML = addPeriod(tax.toFixed(2), ',');
            var grandTotal = tamp + tax;
            grandTotal = Math.round(grandTotal * 100) / 100;
            document.getElementById("grandTotalAfterTax").innerHTML = addPeriod(grandTotal.toFixed(2), ',');
            $("#grandTotalValue").val(grandTotal)
        } else {
            $("#grandTotalValue").val(tamp);
            document.getElementById("tax").innerHTML = '';
            document.getElementById("grandTotalAfterTax").innerHTML = ''
        }
        return total
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
        function addPeriod(nStr, add) {
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
            output = addPeriod(output, ',');
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
    //====================================function====================================
    $("#btn-addRow").removeAttr("disabled");

    var dataUom;
    //get uom
    $.post(getUomThisInventory, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
        var tampVar = $("#inventory-0").find('option:selected').attr('id');
        if (tampVar.indexOf("inventory") >= 0) {
            //get price range
            $.post(getPriceRangeSO, {InventoryInternalID: $("#inventory-0").val()}).done(function (price) {
                document.getElementById("inventory-0").setAttribute("data-content", price);
                document.getElementById("inventory-0").title = "Price Range";
                $("#inventory-0").popover({trigger: 'hover'})
                //get stock
                $.post(getStockInventorySO, {InventoryInternalID: $("#inventory-0").val()}).done(function (stock) {
                    var split = stock.split("---;---");
                    $("#price-0-stock").html(split[0]);
                    document.getElementById("price-0-stock").setAttribute("data-content", "Inventory stock remains : " + split[0] + " <br/> Similar Product : <br/>" + split[1]);
                    document.getElementById("price-0-stock").title = "Information Inventory";
                    $("#price-0-stock").popover({trigger: 'hover'});
                    //get price uom
                    $.post(getPriceRangeThisInventorySO, {InventoryID: $("#inventory-0").val(), UomID: $("#uom-0").val(), Qty: $("#price-0-qty").val(), urutan: 0}).done(function (data) {
                        var arrData = data.split("---;---");
                        if (arrData[0] != 0) {
                            $("#price-" + arrData[1]).val(addPeriod(arrData[0].trim(), ","));
                            changeTotal($("#price-" + arrData[1]).attr("id"));
                        }
                        hitungTotal();
                    });
                });
            });
        } else if (tampVar.indexOf("parcel") >= 0) {
            //found parcel
            $("#price-0-stock").html("-");
            $("#uom-0").attr("readonly", true);
            $("#uom-0").html("<option value='0'>-</option>");
            $("#uom-0").trigger("chosen:updated");
            var value = $("#inventory-0").val().split("---;---");
            //get price parcel
            $.post(getPriceThisParcel, {id: value[0]}).done(function (data) {
                $("#price-0").val(addPeriod(data.trim(), ","));
                changeTotal($("#price-0").attr("id"));
            });
        }
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
});