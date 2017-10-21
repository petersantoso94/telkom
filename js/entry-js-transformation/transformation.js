var config = {
    '.chosen-select': {}
};
for (var selector in config) {
    $(selector).chosen({
        search_contains: true
    });
}
$(document).ready(function () {

    $("#searchInventory").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getSearchResultInventoryTransformation, {id: $("#searchInventory").val()}).done(function (data) {
                $("#selectInventory").html(data);
            });
        }
    });

    $(".btn-delete").click(function () {
        $('#idDelete').val($(this).data('internal'));
    });
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
});