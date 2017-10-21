$(document).ready(function () {
    //=================function=====================
    //====================================function====================================
    $("#btn-addRow").removeAttr("disabled");

    var dataUom;
    //get uom
    $.post(getUomThisInventory, {id: $('#inventory-0').val()}).done(function (data2) {
        $("#uom-0").html(data2);
        dataUom = data2;
    });
});