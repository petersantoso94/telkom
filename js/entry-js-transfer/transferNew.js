$(document).ready(function () {

    //uom inventory
    $(".inventory").change(function () {
        var split = $(this).attr('id').split('-');
        $.post(getUomThisInventory, {id: $(this).val()}).done(function (data) {
            $("#uom-" + split[1]).html(data);
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
    $('#date').datepicker();
    $("#date").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#date').val(tanggalHariIni)
    $.validate();
    $("#date").change(function () {
        var cariText = '';
        $('#transferID').load(a, {"date": $("#date").val()})
    });

    var dataUom;
//get uom
    $.post(getUomThisInventory, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
    });

    var baris = 1;
    $("#btn-addRow").click(function () {
        $('#table-transfer tr:last').after('<tr id="row' + baris + '">' +
                '<td class="chosen-uom">' +
                '<input type="hidden" class="inventory" style="width: 100px" id="inventory-' + baris + '" style="" name="inventory[]" value="' + $('#inventory-0').val() + '">' +
                $("#inventory-0 option[value='" + $("#inventory-0").val() + "']").text() +
                '</td>' +
                '<td>' +
                '<select id="uom-' + baris + '" name="uom[]" class="input-theme uom">' + $('#uom-0').html() + '</select>' +
                '</td>' +
                '<td class="text-right" width="10%">' + '<input type="number" class="maxWidth qty right" name="qty[]" maxlength="11" min="1" value="1" id="price-' + baris + '-qty">' + '</td>' +
                '<td width="5%">' + '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' +
                '</tr>');
        $('#uom-' + baris).val($('#uom-0').val());
        $('#price-' + baris + '-qty').val($('#price-0-qty').val());
        $(".btn-deleteRow").click(function () {
            if ($('#' + $(this).attr('data')).length > 0) {
                document.getElementById($(this).attr('data')).remove()
            }
//            hitungTotal()
        });
        $(".numajaDesimal").keypress(function (e) {
            if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 46))
                return true;
            else
                return false
        });
        //uom inventory
        $(".inventory").change(function () {
            var split = $(this).attr('id').split('-');
            $.post(getUomThisInventory, {id: $(this).val()}).done(function (data) {
                $("#uom-" + split[1]).html(data);
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
        $("#searchInventory").val("");
        $("#searchInventory").attr("tabindex",-1).focus();
        baris++
    });
});