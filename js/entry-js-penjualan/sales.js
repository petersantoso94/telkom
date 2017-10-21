var config = {'.chosen-select': {}};
for (var selector in config) {
    $(selector).chosen({
        search_contains: true
    });
}
$(document).ready(function () {
    $('#user-toggle').click(function () {
        if ($('#drop-user').hasClass('open')) {
            $('#user-toggle').removeClass('user-focus')
        } else {
            $('#user-toggle').addClass('user-focus')
        }
    });
    $('#searchwrap').hide();
    $('#search-button').click(function () {
        $('#searchwrap').slideToggle(300)
    });
    $('#cancel').click(function () {
        $('#searchwrap').slideUp(300)
    });
    $('#startDate').datepicker();
    $('#endDate').datepicker();
    $("#startDate").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#endDate").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('.appd').find('a.chosen-single').each(function () {
        $(this).addClass('chosenapp')
    });
    $('#endDate, #startDate').change(function () {
        if ($('#startDate').val() == '') {
            $('#startDate').val($('#endDate').val())
        } else if ($('#endDate').val() == '') {
            $('#endDate').val($('#startDate').val())
        } else if (dateCheckHigher($('#startDate').val(), $('#endDate').val()) == 'start') {
            $('#endDate').val($('#startDate').val())
        }
    });
    $('#startDateReport').datepicker();
    $('#endDateReport').datepicker();
    $("#startDateReport").datepicker("option", "dateFormat", 'dd-mm-yy');
    $("#endDateReport").datepicker("option", "dateFormat", 'dd-mm-yy');
    $('#endDateReport, #startDateReport').change(function () {
        if ($('#startDateReport').val() == '') {
            $('#startDateReport').val($('#endDateReport').val())
        } else if ($('#endDateReport').val() == '') {
            $('#endDateReport').val($('#startDateReport').val())
        } else if (dateCheckHigher($('#startDateReport').val(), $('#endDateReport').val()) == 'start') {
            $('#endDateReport').val($('#startDateReport').val())
        }
    });
    
    window.deleteAttach = function (element) {
        $('#idDelete').val($(element).data('internal'));
    };
    $("#btn-rSummary").click(function () {
        $('#jenisReport').val('summarySales');
        document.getElementById('titleReport').innerHTML = 'Summary Report'
    });
    $("#btn-rDetail").click(function () {
        $('#jenisReport').val('detailSales');
        document.getElementById('titleReport').innerHTML = 'Detail Report'
    });
    $("#btn-report-transaction").click(function () {
        if ($('#startDateReport').val() == '' && $('#endDateReport').val() == '') {
            var tanggal = new Date();
            var tanggalText = tanggal.getDate() + '-' + (tanggal.getMonth() + 1) + '-' + tanggal.getFullYear();
            $('#startDateReport').val(tanggalText);
            $('#endDateReport').val($('#startDateReport').val())
        } else if ($('#startDateReport').val() == '') {
            $('#startDateReport').val($('#endDateReport').val())
        } else if ($('#endDateReport').val() == '') {
            $('#endDateReport').val($('#startDateReport').val())
        } else if (dateCheckHigher($('#startDateReport').val(), $('#endDateReport').val()) == 'start') {
            $('#endDateReport').val($('#startDateReport').val())
        }
    });
    $('#example').dataTable({
        "draw": 10,
        "processing": true,
        "serverSide": true,
        "ajax": salesDataBackup
    })
});