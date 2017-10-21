var config = {'.chosen-select': {}};
for (var selector in config) {
    $(selector).chosen({
        search_contains: true
    });
}
$(document).ready(function () {

    $("#searchInventory").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getSearchResultInventoryParcel, {id: $("#searchInventory").val()}).done(function (data) {
                $("#selectInventory").html(data);
            });
        }
    });

    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
        $('#deleteName').html($(this).data('name'));
    });
//    $('#example').dataTable({columnDefs: [{targets: [0], orderData: [0, 1]}, {targets: [1], orderData: [1, 0]}, {targets: [2], orderData: [2, 0]}]})

    $('#exampleParcel').dataTable({
        "draw": 10,
        "processing": true,
        "serverSide": true,
        "ajax": parcelDataBackup
    });
});

$("#btn-new").click(function () {
    window.location.href = parcelNew;
});