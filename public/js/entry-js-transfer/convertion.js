var config = {
    '.chosen-select': {}
};
for (var selector in config) {
    $(selector).chosen({
        search_contains: true
    });
}
$(document).ready(function () {

    $("#searchInventoryUom").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getResultSearchConvertion, {id: $("#searchInventoryUom").val()}).done(function (data) {
                $("#selectInventoryUom").html(data);
            });
        }
    });
    $("#searchInventoryUomUpdate").keydown(function (event) {
        if (event.keyCode == 13) { //enter
            event.preventDefault();
            $.post(getResultSearchConvertionUpdate, {id: $("#searchInventoryUomUpdate").val()}).done(function (data) {
                $("#selectInventoryUomUpdate").html(data);
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
    $(".btn-edit").click(function () {


        var data = $(this).data('all');
        data = decryptDataID(data, b);
        var id = searchData("InventoryUomInternalID1", data);

        $('#inventoryUom1Update').html('<option selected value ="' + searchData("InternalID", data) +
                '---;---' + searchData("Value", data) + '---;---' +
                searchData("InventoryInternalID", data) + ' ">' + searchData("InventoryName1", data) +
                ' - ' + searchData("UomID1", data) + '</option>');

        tampUom1Update = $("#inventoryUom1Update").val();
        uom1Update = tampUom1Update.split("---;---");

        $.post(getSelectedInventoryUom2Update, {id: id}).done(function (select) {
            $("#liSelectUpdate").html(select);
        });
        $('#quantityUpdate').val(searchData("Quantity", data));
        $('#quantityResultUpdate').val(searchData("QuantityResult", data));
        $('#quantityResultLabelUpdate').html(searchData("QuantityResult", data));
        $('#warehouseInternalIDUpdate').val(searchData("WarehouseInternalID", data));
        $('#warehouseInternalIDUpdate').trigger("chosen:updated");
        $('#remarkUpdate').val(searchData("Remark", data));
        $('#idUpdate').val(searchData("InternalID", data));
//        document.getElementById('createdDetail').innerHTML = searchData("UserRecord", data) + " " + searchData("dtRecordformat", data);
//        if (searchData("UserModified", data) == "0") {
//            document.getElementById('modifiedDetail').innerHTML = '-';
//        } else {
//            document.getElementById('modifiedDetail').innerHTML = searchData("UserModified", data) + " " + searchData("dtModifformat", data);
//        }
//
//        $("#type" + searchData("InventoryTypeInternalID", data)).attr('selected', 'selected');

    });
//    nilaiAwal = parseFloat(uom1[1]);


//    qty = $("#quantity").val();
//    result = qty * (nilaiAwal / nilaiAkhir);
//    $("#quantityResult").val(result);
//    $("#quantityResultLabel").html(" " + result + " " + uom);
});
$.validate({
    form: '#form-insert'
});
$.validate({
    form: '#form-update'
});
//$("#inventoryUom1").change(function () {
//    tamp = $(this).val();
//    tamp1 = tamp.split("---;---");
//    nilaiAwal = parseFloat(tamp1[1]);
//
//    qty = $("#quantity").val();
//    result = qty * (nilaiAwal / nilaiAkhir);
//    $("#quantityResult").val(result);
//    $("#quantityResultLabel").html(" " + result + " " + uom);
//});

//$("#quantity").keyup(function () {
//    qty = $("#quantity").val();
//    result = qty * (nilaiAwal / nilaiAkhir);
//    $("#quantityResult").val(result);
//    $("#quantityResultLabel").html(" " + result + " " + uom);
//});