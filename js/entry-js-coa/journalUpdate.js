$("#btn-addRow").click(function () {
    var cur = $('#currency').val().split('---;---');
    $('#table-jurnal tr:last').after('<tr id="row' + baris + '">' + '<input type="hidden" class="hiddenTransactionID" value="-1" name="JournalTransactionID[]">' + '<td class="appd"  width="15%">' + textSelect + '</td>' + '<td class="dcmu" width="10%">' + '<input type="text" class="maxWidth debetJournal right numajaDesimal" name="Debet_value[]" maxlength="200" value="0" id="debet-' + baris + '">' + '</td>' + '<td class="dcmu" width="10%">' + '<input type="text" class="maxWidth kreditJournal right numajaDesimal" name="Kredit_value[]" maxlength="200" value="0" id="kredit-' + baris + '">' + '</td>' + '<td width="15%" class="autoCurrency left">' + cur[1] + '</td>' + '<td class="ratetd" width="15%" class="autoRate right">' + $('#rate').val() + '</td>' + '<td width="10%" class="right" id="debet-' + baris + '-hitung">' + '0' + '</td>' + '<td width="10%" class="right" id="kredit-' + baris + '-hitung">' + '0' + '</td>' + '<td>' + '<textarea name="notes[]" id="notes-' + baris + '" style="resize:none"></textarea>' + '</td>' + '<td width="5%">' + '<button type="button" class="btn btn-pure-xs btn-xs btn-deleteRow" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' + '</tr>');
    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
        hitungDebetKredit(1);
        if (tipeJournal != 'Memo') {
            autoJurnal();
            var text = $("#journalNotes").val().replace(/\n/g, '<br>');
            document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
        }
        cekJournalTransaction()
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
    autoChangeJurnal();
    if (tipeJournal != 'Memo') {
        autoJurnal()
    }
});
if (tipeJournal != 'Memo') {
    $("#btn-insertAccount").click(function () {
        $(".payment-auto").each(function () {
            var id = $(this).attr('id');
            if ($('#' + id).length > 0) {
                document.getElementById(id).remove()
            }
            hitungDebetKredit(1);
            if (tipeJournal != 'Memo') {
                autoJurnal();
                var text = $("#journalNotes").val().replace(/\n/g, '<br>');
                document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
            }
            cekJournalTransaction()
        });
        var currency = 0;
        var rate = 0;
        var fail = 0;
        var kosong = 0;
        $(".checkAccount").each(function (i) {
            if ($(this).prop('checked')) {
                var idCheck = $(this).attr('id');
                if (currency == 0 && rate == 0) {
                    currency = $('#' + idCheck + '-currency').val();
                    rate = $('#' + idCheck + '-rate').val()
                }
                if (currency != $('#' + idCheck + '-currency').val()) {
                    fail = 1
                }
                if (rate != $('#' + idCheck + '-rate').val()) {
                    fail = 1
                }
                if ($('#' + idCheck + '-nominal').val() <= 0) {
                    kosong = 1
                }
            }
        });
        $(".hiddenTransactionID").each(function (i) {
            if ($(this).val() != '-1') {
                var cur = $('#currency').val().split('---;---');
                if (cur[0] != currency) {
                    fail = 1
                }
                if (removePeriod($("#rate").val(), ',') != rate) {
                    fail = 1
                }
            }
        });
        if (kosong == 1) {
            alert('Account value must be more than zero.')
        } else if (fail == 1) {
            alert('Account must be in same currency and rate')
        } else {
            $("#rate").val(addPeriodJurnal(rate, ','));
            $("#cur" + currency).attr('selected', 'selected');
            $('#currency').trigger("chosen:updated");
            $("#rate").prop('readonly', true);
            $("#rate").css('background-color', '#eee');
            $("#currency").attr('disabled', true);
            $('#currency').trigger("chosen:updated");
            autoTransactionCurrency();
            var cur = $('#currency').val().split('---;---');
            $(".checkAccount").each(function (i) {
                if ($(this).prop('checked')) {
                    var idCheck = $(this).attr('id');
                    var idSalesPurchase = $('#' + idCheck).val();
                    var nominal = $('#' + idCheck + '-nominal').val();
                    var credit = nominal;
                    var debet = 0;
                    if (jenisDebetCredit == 'Debet') {
                        debet = nominal;
                        credit = 0
                    }
                    if($('#' + idCheck + '-textID').text().substring(0, 1) == 'R'){
                        var tampDebet = debet;
                        debet = credit;
                        credit = tampDebet;
                    }
                    var acc = account;
                    var accI = accountInternalID;
                    if(idSalesPurchase.substring(0, 1) == 'R'){
                        acc = accountR;
                        accI = accountRInternalID;
                    }
                    $('#table-jurnal tr:last').after('<tr class="payment-auto" id="row' + baris + '">' + '<input type="hidden" class="hiddenTransactionID" value="' + idSalesPurchase + '" name="JournalTransactionID[]">' + '<td class="appd"  width="15%">' + acc + '<input type="hidden" value="' + accI + '" name="coa[]">' + '</td>' + '<td class="dcmu right" width="10%">' + debet + '<input type="hidden" class="maxWidth debetJournal right numajaDesimal" name="Debet_value[]" maxlength="200" value="' + debet + '" id="debet-' + baris + '">' + '</td>' + '<td class="dcmu right" width="10%">' + credit + '<input type="hidden" class="maxWidth kreditJournal right numajaDesimal" name="Kredit_value[]" maxlength="200" value="' + credit + '" id="kredit-' + baris + '">' + '</td>' + '<td width="15%" class="autoCurrency left">' + cur[1] + '</td>' + '<td width="15%" class="autoRate right">' + $('#rate').val() + '</td>' + '<td width="10%" class="right" id="debet-' + baris + '-hitung">' + addPeriodJurnal(rate * removePeriod(debet, ','), ',') + '</td>' + '<td width="10%" class="right" id="kredit-' + baris + '-hitung">' + addPeriodJurnal(rate * removePeriod(credit, ','), ',') + '</td>' + '<td>' + '<textarea name="notes[]" id="notes-' + baris + '"></textarea>' + '</td>' + '<td width="5%">' + '<button class="btn btn-pure-xs btn-xs btn-deleteRow" type="button" data="row' + baris + '"><span class="glyphicon glyphicon-trash"></span></button>' + '</td>' + '</tr>');
                    baris++
                }
            });
            $(".btn-deleteRow").click(function () {
                if ($('#' + $(this).attr('data')).length > 0) {
                    document.getElementById($(this).attr('data')).remove()
                }
                hitungDebetKredit(1);
                if (tipeJournal != 'Memo') {
                    autoJurnal();
                    var text = $("#journalNotes").val().replace(/\n/g, '<br>');
                    document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
                }
                cekJournalTransaction()
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
            autoChangeJurnal();
            if (tipeJournal != 'Memo') {
                autoJurnal()
            }
            $('#multiselect').modal('hide')
        }
    })
}
function autoChangeJurnal() {
    myFunctionduit();
    function changeJurnalAuto() {
        if (tipeJournal != 'Memo') {
            var kredit = hitungDebetKredit(1);
            var debet = hitungDebetKredit(0);
            var cekBesar = '';
            if (kredit >= debet) {
                cekBesar = 'kredit';
                debet = kredit - debet;
                kredit = 0
            } else {
                cekBesar = 'debet';
                kredit = debet - kredit;
                debet = 0
            }
            
            debet = parseFloat(debet).toFixed(2);
            kredit = parseFloat(kredit).toFixed(2);
            document.getElementById("jurnalPenyeimbangDebet").innerHTML = addPeriodJurnal(debet, ',');
            document.getElementById("jurnalPenyeimbangKredit").innerHTML = addPeriodJurnal(kredit, ',');
            document.getElementById("debet-autoJurnal-hitung").innerHTML = addPeriodJurnal(debet * removePeriod($('#rate').val(), ','), ',');
            document.getElementById("kredit-autoJurnal-hitung").innerHTML = addPeriodJurnal(kredit * removePeriod($('#rate').val(), ','), ',')
        }
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
    if (tipeJournal != 'Memo') {
        $("#journalNotes").keyup(function () {
            var text = $("#journalNotes").val().replace(/\n/g, '<br>');
            document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
        })
    }
    function addPeriodJurnal(nStr, add) {
        nStr += '';
        var desimalnya = nStr.split(".");
        if (desimalnya.length > 1) {
            var desimalText = desimalnya[1];
            nStr = desimalnya[0]
        } else {
            var desimalText = "00"
        }
        nStr += '';
        x = nStr.split(add);
        x1 = x[0];
        x2 = x.length > 1 ? add + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + add + '$2')
        }
        return x1 + x2 + '.' + desimalText
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
    });
    $(".nominal").blur(function (e) {
        var balance = removePeriod(document.getElementById($(this).attr('id') + '-balance').innerHTML, ',');
        if ($(this).val() == '') {
            $(this).val('0')
        }
        var nominal = removePeriod($(this).val(), ',');
        if (nominal - balance > 0) {
            $(this).val(addPeriod(balance, ','))
        }
    })
}
function changeJurnalAuto() {
    if (tipeJournal != 'Memo') {
        var kredit = hitungDebetKredit(1);
        var debet = hitungDebetKredit(0);
        var cekBesar = '';
        if (kredit >= debet) {
            cekBesar = 'kredit';
            debet = kredit - debet;
            kredit = 0
        } else {
            cekBesar = 'debet';
            kredit = debet - kredit;
            debet = 0
        }
        debet = parseFloat(debet).toFixed(2);
        kredit = parseFloat(kredit).toFixed(2);
        document.getElementById("jurnalPenyeimbangDebet").innerHTML = addPeriodJurnal(debet, ',');
        document.getElementById("jurnalPenyeimbangKredit").innerHTML = addPeriodJurnal(kredit, ',');
        document.getElementById("debet-autoJurnal-hitung").innerHTML = addPeriodJurnal(debet * removePeriod($('#rate').val(), ','), ',');
        document.getElementById("kredit-autoJurnal-hitung").innerHTML = addPeriodJurnal(kredit * removePeriod($('#rate').val(), ','), ',')
    }
    var total = hitungDebetKredit(0)
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
function autoTransactionCurrency() {
    var currency = $("#currency").val().split('---;---');
    for (var a = 0; a < document.getElementsByClassName('autoCurrency').length; a++) {
        document.getElementsByClassName('autoCurrency')[a].innerHTML = currency[1]
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
}
function cekJournalTransaction() {
    var tamp = 0;
    $(".hiddenTransactionID").each(function (i) {
        if ($(this).val() != '-1') {
            tamp = 1
        }
    });
    if (tamp == 0) {
        $("#rate").prop('readonly', false);
        $("#rate").css('background-color', '');
        $("#currency").attr('disabled', false);
        $('#currency').trigger("chosen:updated")
    } else if (tamp == 1) {
        var currency = $("#currency").val().split('---;---');
        $("#rate").prop('readonly', true);
        $("#rate").css('background-color', '#eee');
        $("#currency").attr('disabled', true);
        $('#currency').trigger("chosen:updated")
    }
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
    if (tipeJournal == 'Memo') {
        document.getElementById("totalDebet").innerHTML = 'Total Debet : ' + addPeriodJurnal(totalDebet * kurs, ',');
        document.getElementById("totalKredit").innerHTML = 'Total Kredit : ' + addPeriodJurnal(totalKredit * kurs, ',')
    } else {
        if (totalDebet >= totalKredit) {
            document.getElementById("totalDebet").innerHTML = 'Total Debet : ' + addPeriodJurnal(totalDebet * kurs, ',');
            document.getElementById("totalKredit").innerHTML = 'Total Kredit : ' + addPeriodJurnal(totalDebet * kurs, ',')
        } else {
            document.getElementById("totalDebet").innerHTML = 'Total Debet : ' + addPeriodJurnal(totalKredit * kurs, ',');
            document.getElementById("totalKredit").innerHTML = 'Total Kredit : ' + addPeriodJurnal(totalKredit * kurs, ',')
        }
    }
    if (tipe == 0) {
        return totalDebet
    } else if (tipe == 1) {
        return totalKredit
    }
}
function addPeriodJurnal(nStr, add) {
    nStr += '';
    var desimalnya = nStr.split(".");
    if (desimalnya.length > 1) {
        var desimalText = desimalnya[1];
        nStr = desimalnya[0]
    } else {
        var desimalText = "00"
    }
    nStr += '';
    x = nStr.split(add);
    x1 = x[0];
    x2 = x.length > 1 ? add + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + add + '$2')
    }
    return x1 + x2 + '.' + desimalText
}
function autoJurnal() {
    var slip = $("#slip").val().split('---;---');
    slip = slip[0];
    var tampSplit = dataSlip.split('---;---');
    var name = '';
    var nomor = '';
    for (var check = 0; check < tampSplit.length; check++) {
        var nSearch = tampSplit[check].search(']');
        var res = tampSplit[check].substring(1, nSearch);
        if (slip == res) {
            var tampSplit2 = tampSplit[check].split('&"');
            nomor = tampSplit2[1];
            name = tampSplit2[3]
        }
    }
    var kredit = hitungDebetKredit(1);
    var debet = hitungDebetKredit(0);
    var cekBesar = '';
    if (kredit >= debet) {
        debet = kredit - debet;
        kredit = 0
    } else {
        cekBesar = 'debet';
        kredit = debet - kredit;
        debet = 0
    }
    var cur = $('#currency').val().split('---;---');
    if ($('#jurnalPenyeimbang').length > 0) {
        document.getElementById('jurnalPenyeimbang').remove()
    }
    $('#table-jurnal tr:last').after('<tr id="jurnalPenyeimbang">' + '<td class="left"  width="15%">' + nomor + ' ' + name + '</td>' + '<td width="10%" class="right" id="jurnalPenyeimbangDebet">' + addPeriodJurnal(debet, ',') + '</td>' + '<td width="10%" class="right" id="jurnalPenyeimbangKredit">' + addPeriodJurnal(kredit, ',') + '</td>' + '<td width="15%" class="autoCurrency left">' + cur[1] + '</td>' + '<td width="15%" class="autoRate right">' + $('#rate').val() + '</td>' + '<td width="10%" class="right" id="debet-autoJurnal-hitung">' + addPeriodJurnal(debet * removePeriod($('#rate').val(), ','), ',') + '</td>' + '<td width="10%" class="right" id="kredit-autoJurnal-hitung">' + addPeriodJurnal(kredit * removePeriod($('#rate').val(), ','), ',') + '</td>' + '<td id="notes-autoJurnal" class="left">' + 'Journal Auto' + '</td>' + '<td width="15%">' + ' - </td>' + '</tr>');
    var text = $("#journalNotes").val().replace(/\n/g, '<br>');
    document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
}
$(document).ready(function () {
    autoChangeJurnal();
    hitungDebetKredit(1);
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
    if (tipeJournal != 'Memo') {
        autoJurnal()
    }
    var slip = $("#slip").val().split('---;---');
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
        if (totalDebet != totalKredit && tipeJournal == 'Memo') {
            alert('Credit and Debet not balance, form cannot be submit.');
            return false
        }
    });
    $(".btn-deleteRow").click(function () {
        if ($('#' + $(this).attr('data')).length > 0) {
            document.getElementById($(this).attr('data')).remove()
        }
        hitungDebetKredit(1);
        if (tipeJournal != 'Memo') {
            autoJurnal();
            var text = $("#journalNotes").val().replace(/\n/g, '<br>');
            document.getElementById("notes-autoJurnal").innerHTML = 'Journal Auto ' + text
        }
        cekJournalTransaction()
    });
    $("#checkAll").click(function () {
        if ($('input:checkbox').prop('checked') == false) {
            $('input:checkbox').prop('checked', true)
        } else {
            $('input:checkbox').prop('checked', false)
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
    cekJournalTransaction()
});
$.validate({onSuccess: function () {
        $("#currency").attr('disabled', false);
        $('#currency').trigger("chosen:updated")
    }});
    $('#example').dataTable({
        columnDefs: [{
                targets: [0],
                orderData: [0, 1]
            }, {
                targets: [1],
                orderData: [1, 0]
            }, {
                targets: [4],
                orderData: [4, 0]
            }]
    });