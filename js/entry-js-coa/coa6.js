$(document).ready(function () {
    $('#exampleCustomer').dataTable({
        "draw": 10,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url":COA6CustomerDataBackup,
        }
    });
    
    $('#exampleSupplier').dataTable({
        "draw":10,
        "processing":true,
        "serverSide":true,
        "ajax":COA6SupplierDataBackup
    });
    
    $(".autoTab").focus(function (e) {
        $(this).val("");
    });
    $(".autoTab").keyup(function (e) {
        var tamp = $(this).val();
        var length = document.getElementById($(this).attr('id')).maxLength;
        if (tamp.length == length)
        {
            var inputs = $(this).closest('form').find(':input');
            inputs.eq(inputs.index(this) + 1).focus();
        }
        $('#taxID').val($('#taxID1').val() + "." + $('#taxID2').val() + "." + $('#taxID3').val() + "." + $('#taxID4').val() + "-" + $('#taxID5').val() + "." + $('#taxID6').val());
        $('#taxIDupdate').val($('#taxID1update').val() + "." + $('#taxID2update').val() + "." + $('#taxID3update').val() + "." + $('#taxID4update').val() + "-" + $('#taxID5update').val() + "." + $('#taxID6update').val());
    });
    window.updateCustomer = function(element){
         var data = $(element).data('all');
        var level = $(element).data('level');
//        data = decryptDataID(data, b);

        $('#nameUpdate').val(searchData("ACC" + level + "Name", data));
        $('#addressUpdate').val(searchData("Address", data));
        $('#cityUpdate').val(searchData("City", data));
        $('#blockUpdate').val(searchData("Block", data));
        $('#addressNumberUpdate').val(searchData("AddressNumber", data));
        $('#rtUpdate').val(searchData("RT", data));
        $('#rwUpdate').val(searchData("RW", data));
        $('#emailUpdate').val(searchData("Email", data));
        $('#districtUpdate').val(searchData("District", data));
        $('#subdistrictUpdate').val(searchData("Subdistrict", data));
        $('#provinceUpdate').val(searchData("Province", data));
        $('#postalCodeUpdate').val(searchData("PostalCode", data));
        $('#originUpdate').val(searchData("Origin", data));
        $('#faxUpdate').val(searchData("Fax", data));
        $('#phoneUpdate').val(searchData("Phone", data));
        $('#contactPersonUpdate').val(searchData("ContactPerson", data));
        $('#creditLimitUpdate').val(addPeriod(searchData("CreditLimit", data), ','));
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
        if (searchData("UserModified", data) == "0") {
            document.getElementById('modifiedDetail').innerHTML = '-';
        } else {
            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
        }

        var taxID = searchData("TaxID", data);
        var taxIDTamp = taxID.split('-');
        var taxIDTampKiri = taxIDTamp[0].split('.');
        var taxIDTampKanan = taxIDTamp[1].split('.');
        $('#taxID1update').val(taxIDTampKiri[0]);
        $('#taxID2update').val(taxIDTampKiri[1]);
        $('#taxID3update').val(taxIDTampKiri[2]);
        $('#taxID4update').val(taxIDTampKiri[3]);
        $('#taxID5update').val(taxIDTampKanan[0]);
        $('#taxID6update').val(taxIDTampKanan[1]);
        $('#taxIDupdate').val($('#taxID1update').val() + "." + $('#taxID2update').val() + "." + $('#taxID3update').val() + "." + $('#taxID4update').val() + "-" + $('#taxID5update').val() + "." + $('#taxID6update').val());
    };

    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
        $('#AccID').val($(this).data('id'));
    });
 
function gagal() {
    if ($('#spanErrorID')) {
        $('#spanErrorID').remove();
    }
    $('#accID').parent('div').append('<span class="help-block form-error" id="spanErrorID">Account ID has already been taken</span>');
    $('#accID').parent('div').removeClass('has-success');
    $('#accID').parent('div').addClass('has-error');
    $('#accID').css("border-color", "rgb(169, 68, 66)");
}
function sukses() {
    $('#spanErrorID').remove();
    $('#accID').parent('div').removeClass('has-error');
    $('#accID').parent('div').addClass('has-success');
    $('#accID').css("border-color", "");
}
function validationID(dataID, status, r) {
    dataID = decryptDataID(dataID, r);
    var search = $('#accID').val();
    if (search == '') {
        return false;
    } else if (searchID(search, dataID, 'accID')) {
        gagal();
        if (status == 1) {
            return false; // Will stop the submission of the form
        }
    } else {
        sukses();
        if (status == 1) {
            return true; //form submit
        }
    }
}
$.validate({
    form: '#form-update',
    onSuccess: function () {
        $('#creditLimitUpdate').val(removePeriod($('#creditLimitUpdate').val(), ','));
    }
});
$.validate({
    form: '#form-insert',
    onError: function () {
        var dataID = a;
        validationID(dataID, 1, b);
    },
    onSuccess: function () {
        $('#uangCredit').val(removePeriod($('#uangCredit').val(), ','));
        var dataID = a;
        return validationID(dataID, 1, b);
    }
});
$("#accID").blur(function () {
    var dataID = a;
    validationID(dataID, 0, b);
});
});