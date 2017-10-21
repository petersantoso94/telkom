function removePeriod(nStr, remove) {
    if (nStr != '') {
        tamp = nStr.split(remove);
        nStr = '';
        for (var kembali = 0; kembali < tamp.length; kembali++) {
            nStr += tamp[kembali]
        }
    }
    return nStr
}
function addPeriod(nStr, add) {
    nStr += '';
    nStr = removePeriod(nStr, add);
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
function formatUang(text, depan, simbol, desimal) {
    var desimalnya = text.split(".");
    if (desimalnya.length > 1) {
        var desimalText = desimalnya[1]
    } else {
        var desimalText = "00"
    }
    var text = desimalnya[0];
    var tamp = text;
    var len = tamp.length;
    var count = 1;
    var temp = "";
    if (desimal == 1) {
        for (var awal = len - 1; awal >= 0; awal--) {
            if ((count - 1) % 3 == 0 && count - 1 > 0) {
                temp += ","
            }
            temp += tamp[awal];
            count += 1
        }
        len = temp.length;
        tamp = "";
        for (var awal = len - 1; awal >= 0; awal--) {
            tamp += temp[awal]
        }
        tamp += "." + desimalText
    } else {
        for (var awal = len - 1; awal >= 0; awal--) {
            if ((count - 1) % 3 == 0 && count - 1 > 0) {
                temp += "."
            }
            temp += tamp[awal];
            count += 1
        }
        len = temp.length;
        tamp = "";
        for (var awal = len - 1; awal >= 0; awal--) {
            tamp += temp[awal]
        }
    }
    if (depan == 1) {
        return simbol + " " + tamp
    } else {
        return tamp + " " + simbol
    }
}
$(document).ready(function () {
    $(".numaja").keypress(function (e) {
        if (e.keyCode == 9) {
            $(this).select();
        }
        if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0))
            return true;
        else
            return false
        
    });
    $(".numajaDesimal").keypress(function (e) {
        if (e.keyCode == 9) {
            $(this).select();
        }
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
    $(".numajaDesimalPlus").keypress(function (e) {
        if (e.keyCode == 9) {
            $(this).select();
        }
        if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 46) || (e.charCode == 43))
            return true;
        else
            return false
        
    });
    $(".numajaDanminus").keypress(function (e) {
        if (e.keyCode == 9) {
            $(this).select();
        }
        if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 45))
            return true;
        else
            return false
        
    })
});