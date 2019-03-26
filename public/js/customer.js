$(document).ready(function() {
    $("#input-search-customer").on("click", "li", function(e) {
        $("#form-customer").submit();
    });

    $.validate({
        modules: "security",
        form: '#form-addcustomer'
    });

    $("#customerID").blur(function() {
        $.post(checkCustomerID, {id: $("#customerID").val()}, function(data) {
            if (data == 1) {
                sukses('ID');
            } else {
                gagal('ID');
            }
        });
    });

    function gagal(data) {
        $('#spanError' + data).remove();
        $('#customer' + data).parent('div').append('<span class="help-block form-error" id="spanError' + data + '">Member ' + data + ' has already been taken</span>');
        $('#customer' + data).parent('div').removeClass('has-success');
        $('#customer' + data).parent('div').addClass('has-error');
        $('#customer' + data).css("border-color", "rgb(169, 68, 66)");
    }
    function sukses(data) {
        $('#spanError' + data).remove();
        $('#customer' + data).parent('div').removeClass('has-error');
        $('#customer' + data).parent('div').addClass('has-success');
        $('#customer' + data).css("border-color", "");
    }
});