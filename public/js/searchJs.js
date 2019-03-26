function searchData(cari, data) {
    cari = cari + '":';
    var all = data;
    var tamp = all.substring(2, all.length - 2);
    var nSearch = tamp.search(cari);
    
    cari = cari.substring(0, cari.length - 2);

    var nKoma = tamp.indexOf(",", nSearch);
    if (nKoma == "-1") {
        var data = tamp.substring(nSearch + cari.length + 2, tamp.length)
    } else {
        var data = tamp.substring(nSearch + cari.length + 2, nKoma)
    }
    nSearch = data.search('"');
    if (nSearch == "0" && nSearch != "-1") {
        data = data.substring(1, data.length)
    }
    nSearch = data.search('"');
    if (nSearch == data.length - 1 && nSearch != "-1") {
        data = data.substring(0, data.length - 1)
    }
    data = data.replace(/!#44!/g, ",");
    data = data.replace(/!#47!/g, "/");
    data = data.replace(/!#34!/g, '"');
    data = data.replace(/!#39!/g, "'");
    return data
}
function searchID(cari, data, text) {
    data = data.toUpperCase();
    text = text.toUpperCase();
    cari = cari.toUpperCase();

    var hasil = data.search('"' + text + '":"' + cari + '"');
    if (hasil < 0) {
        return false
    } else {
        return true
    }
}
function dateCheckHigher(startDate, endDate) {
    var splitStart = startDate.split('-');
    var splitEnd = endDate.split('-');
    if (splitStart[2] > splitEnd[2]) {
        return'start'
    } else if (splitStart[2] < splitEnd[2]) {
        return'end'
    } else if (splitStart[1] > splitEnd[1]) {
        return'start'
    } else if (splitStart[1] < splitEnd[1]) {
        return'end'
    } else if (splitStart[0] > splitEnd[0]) {
        return'start'
    } else if (splitStart[0] < splitEnd[0]) {
        return'end'
    } else {
        return'start'
    }
}
function findAlfaNumeric(cari, arr, rand) {
    var ke = -1;
    for (var i = 0; i < arr.length; i++) {
        if (cari == arr[i]) {
            ke = i
        }
    }
    if (ke == -1) {
        return'-1'
    }
    var index = 0;
    if ((ke - rand) < 0) {
        index = (ke - rand) + 76
    } else {
        index = ke - rand
    }
    return index
}
function decryptDataID(dataID, r) {
    var arr = ['h', '!', '3', 'z', 'a', 'g', '8', '%', '9', 'k', 'y', '@', 'b', 'f', '-', 'o', 'v', 'q', 'd', '7', '0', 'i', '^', '6', '#', '5', 'c', 'j', '*', 'e', '&', '(', 'm', 'l', '4', ')', '=', 'p', 'u', '_', 's', '2', 't', '+', 'r', '$', 'x', 'w', 'n', '1', 'H', 'Z', 'A', 'G', 'K', 'Y', 'B', 'F', 'O', 'V', 'Q', 'D', 'I', 'C', 'J', 'E', 'M', 'L', 'P', 'U', 'S', 'T', 'R', 'X', 'W', 'N'];
    var ke = 0;
    var kal = "";
    for (var i = 0; i < dataID.length; i++) {
        ke = findAlfaNumeric(dataID[i], arr, r);
        if (ke != '-1') {
            kal += arr[ke]
        } else {
            kal += dataID[i]
        }
    }
    return kal
}

$(document).ready(function () {
    $(".noSpecialCharacter").keypress(function (e) {
        if (e.keyCode == '34' || e.keyCode == '39') {
            e.preventDefault();
        }
    });
});