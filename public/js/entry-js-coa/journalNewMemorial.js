var baris = 1;
$("#btn-addRow").click(function () {
    var cur = $('#currency').val().split('---;---');
    $('#table-jurnal tr:last').after('<tr id="row' + baris + '">' + '<td class="appd"  width="15%">' + textSelect + '</td>' + '<td class="dcmu" width="10%">' + '<input type="text" class="maxWidth debetJournal right numajaDesimal" name="Debet_value[]" maxlength="200" value="0" id="debet-' + baris + '">' + '</td>' + '<td class="dcmu"  width="10%">' + '<input type="text" class="maxWidth kreditJournal right numajaDesimal" name="Kredit_value[]" maxlength="200" value="0" id="kredit-' + baris + '">' + '</td>' + '<td width="15%" class="autoCurrency left">' + cur[1] + '</td>' + '<td width="15%" class="autoRate right">' + $('#rate').val() + '</td>' + '<td width="10%" class="right" id="debet-' + baris + '-hitung">' + '0' + '</td>' + '<td width="10%" class="right" id="kredit-' + baris + '-hitung">' + '0' + '</td>' + '<td>' + '<textarea name="notes[]" id="notes-' + baris + '" style="resize:none"></textarea>' + '</td>' + '<td width="5%">' + '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' + '</tr>');
    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
        hitungDebetKredit(1)
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
    autoChangeJurnal()
});
function autoChangeJurnal() {
    myFunctionduit();
    function changeJurnalAuto() {
        var total = hitungDebetKredit(0)
    }
    $("#currency").change(function () {
        var currency = $("#currency").val().split('---;---');
        for (var a = 0; a < document.getElementsByClassName('autoCurrency').length; a++) {
            document.getElementsByClassName('autoCurrency')[a].innerHTML = currency[1]
        }
        $("#rate").val(addPeriod(currency[2], ','));
        var currency = $("#currency").val().split('---;---');
        if (currency[3] == '1') {
            $("#rate").prop('readonly', true);
            $("#rate").css('background-color', '#eee')
        } else {
            $("#rate").prop('readonly', false);
            $("#rate").css('background-color', '')
        }
        var rate = $("#rate").val();
        for (var a = 0; a < document.getElementsByClassName('autoRate').length; a++) {
            document.getElementsByClassName('autoRate')[a].innerHTML = addPeriod(rate, ',')
        }
        $(".debetJournal").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        $(".kreditJournal").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        changeJurnalAuto()
    });
    function addPeriodJurnal(nStr, add) {
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
        var input = document.getElementById(id).value;
        input = removePeriod(input, ',');
        var rate = $("#rate").val();
        rate = removePeriod(rate, ',');
        var output = input * rate;
        output = addPeriodJurnal(output, ',');
        document.getElementById(id + '-hitung').innerHTML = output
    }
    $("#rate").keyup(function () {
        var rate = $("#rate").val();
        for (var a = 0; a < document.getElementsByClassName('autoRate').length; a++) {
            document.getElementsByClassName('autoRate')[a].innerHTML = addPeriod(rate, ',')
        }
        $(".debetJournal").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        $(".kreditJournal").each(function (i) {
            changeTotal($(this).attr('id'))
        });
        changeJurnalAuto()
    });
    $(".debetJournal,.kreditJournal").keyup(function (e) {
        if ($(this).val() == '') {
        } else {
            changeTotal($(this).attr('id'));
            changeJurnalAuto()
        }
    });
    $(".debetJournal,.kreditJournal").blur(function (e) {
        if ($(this).val() == '') {
            $(this).val('0')
        }
        changeTotal($(this).attr('id'));
        changeJurnalAuto()
    })
}
function hitungDebetKredit(tipe) {
    var kurs = $('#rate').val();
    kurs = removePeriod(kurs, ',');
    var totalDebet = 0;
    var totalKredit = 0;
    $(".debetJournal").each(function (i) {
        var jumlah = document.getElementById($(this).attr('id')).value;
        totalDebet += parseFloat(removePeriod(jumlah, ','))
    });
    $(".kreditJournal").each(function (i) {
        var jumlah = document.getElementById($(this).attr('id')).value;
        totalKredit += parseFloat(removePeriod(jumlah, ','))
    });
    document.getElementById("totalDebet").innerHTML = 'Total Debet : ' + addPeriodJurnal(totalDebet * kurs, ',');
    document.getElementById("totalKredit").innerHTML = 'Total Credit : ' + addPeriodJurnal(totalKredit * kurs, ',');
    if (tipe == 0) {
        return totalDebet
    } else if (tipe == 1) {
        return totalKredit
    }
}
function addPeriodJurnal(nStr, add) {
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
    autoChangeJurnal();
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
    $("#btn-save").click(function () {
        var totalDebet = 0;
        var totalKredit = 0;
        $(".debetJournal").each(function (i) {
            var jumlah = document.getElementById($(this).attr('id')).value;
            totalDebet += parseFloat(removePeriod(jumlah, ','))
        });
        $(".kreditJournal").each(function (i) {
            var jumlah = document.getElementById($(this).attr('id')).value;
            totalKredit += parseFloat(removePeriod(jumlah, ','))
        });
        if (totalDebet != totalKredit) {
            alert('Credit and Debet not balance, form cannot be submit.');
            return false
        }
    });
    var currency = $("#currency").val().split('---;---');
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
    $('#journalID').load(journalSearch, {"date": $("#date").val(), "tipe": jenis})
});