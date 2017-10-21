$(document).ready(function () {
    var counterKanan = 1;
    $.validate({
        form: '#form-update-sales'
    });

    var dropdownSearch = function (parent) {
        var thus = this;
        this.parentDropdown = $(parent);
        var isiHTML = "";
        var stringQuery = "";
        this.search = function (el) {
            var inputValue = $(el.target).val();
            if (inputValue.length != 0) {
                if (el.which == 13) {
                    stringQuery = $('#input-inventory').val().replace(" ", "%");
                    isiHTML = "";
                    $(el.target).parents(thus.parentDropdown).find('.list-search-wrapper').show();
                    $(el.target).parents(thus.parentDropdown).find('#loading-menu').show();
                    $.getJSON(ajax1, {par1: stringQuery}, function (data) {
                        $.each(data, function (key, val) {
                            isiHTML += "<li data-jenis='inventory' data-id='" + val.InternalID + "'>(" + val.InventoryID + ") " + val.InventoryName + "</li>";
                        });
                    }).done(function () {
                        $(el.target).parents(thus.parentDropdown).find('#loading-menu').hide();
                    });
                }

            } else {
                $(el.target).parents(thus.parentDropdown).find('.list-search-wrapper').hide();
            }
        };
    };
    var dropdownSearchCustomer = function (parent) {
        var thus = this;
        this.parentDropdown = $(parent);
        var isiHTML = "";
        var stringQuery = "";
        this.search = function (el) {
            var inputValue = $(el.target).val();
            if (inputValue.length != 0) {
                if (el.which == 13) {
                    stringQuery = $('#input-customer').val().replace(" ", "%");
                    isiHTML = "";
                    $(el.target).parents(thus.parentDropdown).find('#search-customer').show();
                    $(el.target).parents(thus.parentDropdown).find('#loading-menu-customer').show();
                    $.getJSON(loadCustomer, {par1: stringQuery}, function (data) {
                        $.each(data, function (key, val) {
                            isiHTML += "<li data-id='" + val.InternalID + "'>(" + val.ACC6ID + ") " + val.ACC6Name + "</li>";
                        });
                        $('#customer-container').html(isiHTML);
                    }).done(function () {
                        $(el.target).parents(thus.parentDropdown).find('#loading-menu-customer').hide();
                    });
                }

            } else {
                $(el.target).parents(thus.parentDropdown).find('#search-customer').hide();
            }
        };
    };
    var nDropdownSearch = new dropdownSearch('.dropdown-search-wrapper');
    var nDropdownSearchCustomer = new dropdownSearchCustomer('#dropdown-customer');
    $("#input-inventory").on('keyup', function (e) {
        nDropdownSearch.search(e);
    });
    $("#input-customer").on('keyup', function (e) {
        nDropdownSearchCustomer.search(e);
    });
    $("#btn-plus").on("click", function () {
        if (($("#quantity").val()) == "") {
            $("#quantity").val(0);
        }
        $("#quantity").val(parseInt($("#quantity").val()) + 1);
        if ($('#input-inventory').attr('data-id') != null && $('#input-inventory').attr('data-jenis') != 'parcel') {
            $.getJSON(ajax3, {par1: $('#input-inventory').attr('data-id'), par2: $('#uom').val(), par3: $('#quantity').val(), priceT: $('#priceType').val()}, function (data) {
                $("#price").val(addPeriod(data[0].a, ','));
                if ($("#quantity").val() == "" || parseInt($("#quantity").val()) <= 0) {
                    $("#quantity").val(1);
                }
                $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
            });
        } else {
            $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
        }
    });
    $("#btn-min").on("click", function () {
        if (parseInt($("#quantity").val()) <= 0 || $("#quantity").val() == "")
            $("#quantity").val(1);
        $("#quantity").val(parseInt($("#quantity").val()) - 1);
        if ($('#input-inventory').attr('data-id') != null && $('#input-inventory').attr('data-jenis') != 'parcel') {
            $.getJSON(ajax3, {par1: $('#input-inventory').attr('data-id'), par2: $('#uom').val(), par3: $('#quantity').val(), priceT: $('#priceType').val()}, function (data) {
                $("#price").val(addPeriod(data[0].a, ','));
                if ($("#quantity").val() == "" || parseInt($("#quantity").val()) <= 0) {
                    $("#quantity").val(1);
                }
                $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
            });
        } else {
            $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
        }
    });
    $('#quantity').on('input', function () {
        if ($('#input-inventory').attr('data-id') != null && $('#input-inventory').attr('data-jenis') != 'parcel') {
            $.getJSON(ajax3, {par1: $('#input-inventory').attr('data-id'), par2: $('#uom').val(), par3: $('#quantity').val(), priceT: $('#priceType').val()}, function (data) {
                $("#price").val(addPeriod(data[0].a, ','));
                if ($("#quantity").val() == "" || parseInt($("#quantity").val()) <= 0) {
                    $("#quantity").val(1);
                    $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
                } else {
                    $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
                }
            });
        } else {
            $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
        }
    });
    $('#price').on('input', function () {
        if ($("#price").val() == "" || parseInt($("#price").val()) < 0) {
            $("#price").val(0);
        }
        $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
    });
    $('#btn-saveCart').on('click', function () {
        var isiHTML = "";
        counterKanan = 1;
        if ($('#input-inventory').val() != "") {
            $.getJSON(ajax5, {par1: $('#input-inventory').attr('data-id'), par2: $('#uom').val(), par3: $('#quantity').val(), par4: removePeriod($('#price').val(), ','), par5: $('#input-inventory').attr('data-jenis')}, function (data) {
                $.each(data, function (key, val) {
                    var inventory = val.id.split('------');
                    isiHTML += '<div class="white-pane__bordered margbot10" style="padding: 7px !important">\n\
        <div class="dropdown pull-right">\n\
        <button class="button btntrans btn-mini no-shadow dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">\n\
        <span class="glyphicon glyphicon-option-vertical"></span>\n\
        </button><ul class="dropdown-menu" aria-labelledby="dropdownMenu1">\n\
        <li><a href="#" onclick="updateAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '" data-product="' + inventory[0] + '" class="fw500 text-uppercase modal-edit-product"><b>Edit</b></a></li>\n\
        <li role="separator" class="divider"></li>\n\
        <li><a href="#" onclick="deleteAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '" data-toggle="modal" data-target="#modal-remove-product" class="fw500 text-uppercase"><b>Remove</b></a></li>\n\
        </ul></div>\n\
        <p class="fw400 margbot5">' + counterKanan+'. '+ val.name.substring(0, 40) + '</p>\n\
        <p class="fw300 margbot5 cgrey"><small><b>' + val.qty + ' ' + val.options.UomID + '</b> @' + addPeriod(val.price, ",") + ' : <b>Rp. ' + addPeriod((val.qty * val.price), ",") + '</b></small></p>\n\
        </div>';
        counterKanan++;
                });
                $('#cart-container').html(isiHTML);
                $('#input-inventory').val("");
                $('#quantity').val("1");
                $('#price').val("0");
            });
        }
    });
    $("#search-customer").on("click", "li", function (e) {
        $("#input-customer").val($(e.target).text());
        $("#input-customer").attr('data-id', $(e.target).attr('data-id'));
        $('#search-customer').hide();
    });
    $("#search-inventory").on("click", "li", function (e) {
        $("#input-inventory").val($(e.target).text());
        $("#input-inventory").attr('data-id', $(e.target).attr('data-id'));
        $("#input-inventory").attr('data-jenis', $(e.target).attr('data-jenis'));
        $('.list-search-wrapper').hide();
        var isiSimilarity = "";
        var isiUom = "";
        var selected = "";
        var inventoryInternalID = $(e.target).attr('data-id');
        if ($(e.target).attr('data-jenis') == 'inventory') {
            $("#input-inventory").attr('data-jenis', $(e.target).attr('data-jenis'));
                $.getJSON(ajax4, {par1: inventoryInternalID}, function (data) {
                    $.each(data, function (key, val) {
                        selected = "";
                        if (val.Default == '1') {
                            selected = "selected";
                        }
                        isiUom += '<option value="' + val.UomInternalID + '" ' + selected + '>' + val.UomID + '</option>';
                    })
                    $('#uom').html(isiUom);
                    $.getJSON(ajax3, {par1: $(e.target).attr('data-id'), par2: $('#uom').val(), par3: $('#quantity').val(), priceT: $('#priceType').val()}, function (data) {
                        $("#price").val(addPeriod(data[0].a, ','));
                        $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
                    });
                });
        } else {
            $("#input-inventory").attr('data-jenis', $(e.target).attr('data-jenis'));
            $('#similarity-inventory').attr('data-content', '-');
            isiUom = '<option value="-1">-</option>';
            $("#price").val(addPeriod($(e.target).data('price'), ','));
            $("#subtotal").html('Rp. ' + addPeriod(parseInt($("#quantity").val()) * parseInt(removePeriod($("#price").val(), ',')), ','));
            $('#uom').html(isiUom);
        }
    });
    $('#form-update-sales').find('input').on('keydown', function (el) {
        if (el.which == 13) {
            return false;
        }
    });

//    $('#cart-container').on('click', '.modal-edit-product', function (e) {
//        var selected = "";
//        var isiUom = "";
//        var UOMlama = "";
//        $.getJSON(getCart, {id: $(e.target).attr('data-id')}, function (data) {
//            $('#edit-price').val(data.price);
//            $('#edit-qty').val(data.qty);
//            $('#edit-name').html(data.name);
//            UOMlama = data.id.split('------');
//            $('#btn-edit-product').attr('data-id', $(e.target).attr('data-id'));
//            $.getJSON(ajax4, {par1: $(e.target).attr('data-product')}, function (data) {
//                $.each(data, function (key, val) {
//                    selected = "";
//                    if (val.UomInternalID == UOMlama[1]) {
//                        selected = "selected";
//                    }
//                    isiUom += '<option value="' + val.UomInternalID + '" ' + selected + '>' + val.UomID + '</option>';
//                })
//                $('#edit-uom').html(isiUom);
//                $('#modal-edit-product').modal('show');
//            });
//        });
//    });

    window.updateAttach = function (e) {
        var selected = "";
        var isiUom = "";
        var UOMlama = "";
        $.getJSON(getCart, {id: $(e).attr('data-id')}, function (data) {
            $('#edit-price').val(addPeriod(data.price, ','));
            $('#edit-qty').val(data.qty);
            $('#edit-name').html(data.name);
            UOMlama = data.id.split('------');
            $('#btn-edit-product').attr('data-id', $(e).attr('data-id'));
            $('#btn-edit-product').attr('data-jenis', $(e).attr('data-jenis'));

            if ($(e).attr('data-jenis') == 'inventory') {
                $.getJSON(ajax4, {par1: $(e).attr('data-product')}, function (data) {
                    $.each(data, function (key, val) {
                        selected = "";
                        if (val.UomInternalID == UOMlama[1]) {
                            selected = "selected";
                        }
                        isiUom += '<option value="' + val.UomInternalID + '" ' + selected + '>' + val.UomID + '</option>';
                    })
                    $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
                    $('#edit-uom').html(isiUom);
                    $('#modal-edit-product').modal('show');
                });
            } else {
                isiUom = '<option value="-1">-</option>';
                $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
                $('#edit-uom').html(isiUom);
                $('#modal-edit-product').modal('show');
            }
        });
    };
    window.deleteAttach = function (e) {
        $('#btn-remove-product').attr('data-id', $(e).attr('data-id'));
        $('#btn-remove-product').attr('data-jenis', $(e).attr('data-jenis'));
    };

    $('#btn-edit-product').on('click', function () {
    counterKanan = 1;
        $.getJSON(updateCart, {par1: $(this).attr('data-id'), par2: $('#edit-qty').val(), par3: removePeriod($('#edit-price').val(), ','), par4: $('#edit-uom').val(), par5: $(this).attr('data-jenis')}, function (data) {
            var isiHTML = "";
            $.each(data, function (key, val) {
                var inventory = val.id.split('------');
                isiHTML += '<div class="white-pane__bordered margbot10" style="padding: 7px !important">\n\
        <div class="dropdown pull-right">\n\
        <button class="button btntrans btn-mini no-shadow dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">\n\
        <span class="glyphicon glyphicon-option-vertical"></span>\n\
        </button><ul class="dropdown-menu" aria-labelledby="dropdownMenu1">\n\
        <li><a href="#" onclick="updateAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '"  data-product="' + inventory[0] + '" class="fw500 text-uppercase modal-edit-product"><b>Edit</b></a></li>\n\
        <li role="separator" class="divider"></li>\n\
        <li><a href="#" onclick="deleteAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '"  data-toggle="modal" data-target="#modal-remove-product" class="fw500 text-uppercase"><b>Remove</b></a></li>\n\
        </ul></div>\n\
        <p class="fw400 margbot5">' + counterKanan+'. '+ val.name.substring(0, 40) + '</p>\n\
        <p class="fw300 margbot5 cgrey"><small><b>' + val.qty + ' ' + val.options.UomID + '</b> @' + addPeriod(val.price, ",") + ' : <b>Rp. ' + addPeriod((val.qty * val.price), ",") + '</b></small></p>\n\
        </div>';
        counterKanan++;
            });
            $('#cart-container').html(isiHTML);
        });
        $('#modal-edit-product').modal('hide');
    });
    $('#btn-remove-product').on('click', function () {
    counterKanan = 1;
        $.getJSON(deleteCart, {par1: $(this).attr('data-id')}, function (data) {
            var isiHTML = "";
            $.each(data, function (key, val) {
                var inventory = val.id.split('------');
                isiHTML += '<div class="white-pane__bordered margbot10" style="padding: 7px !important">\n\
        <div class="dropdown pull-right">\n\
        <button class="button btntrans btn-mini no-shadow dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">\n\
        <span class="glyphicon glyphicon-option-vertical"></span>\n\
        </button><ul class="dropdown-menu" aria-labelledby="dropdownMenu1">\n\
        <li><a href="#" onclick="updateAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '"  data-product="' + inventory[0] + '" class="fw500 text-uppercase modal-edit-product"><b>Edit</b></a></li>\n\
        <li role="separator" class="divider"></li>\n\
        <li><a href="#" onclick="deleteAttach(this)" data-id="' + val.id + '" data-jenis="' + val.options.Type + '"  data-toggle="modal" data-target="#modal-remove-product" class="fw500 text-uppercase"><b>Remove</b></a></li>\n\
        </ul></div>\n\
        <p class="fw400 margbot5">' + counterKanan+'. '+ val.name.substring(0, 40) + '</p>\n\
        <p class="fw300 margbot5 cgrey"><small><b>' + val.qty + ' ' + val.options.UomID + '</b> @' + addPeriod(val.price, ",") + ' : <b>Rp. ' + addPeriod((val.qty * val.price), ",") + '</b></small></p>\n\
        </div>';
        counterKanan++;
            });
            $('#cart-container').html(isiHTML);
        });
        $('#modal-remove-product').modal('hide');
    });

    $('#btn-submit').on('click', function () {
        $.getJSON(checkCart, function (data) {
            if (data > 0) {
                $('#form-update-sales').submit();
            } else {
                alert('Cant update sales without Product selected');
                $('#myTabs a[href="#added-product"]').tab('show')
            }
        });
    });
    $('#btn-update-costumer').on('click', function () {
        $($(this).attr('data-toggle-form')).toggle();
    });
    $('[data-toggle="popover"]').popover({html: true});

    $('#btn-addCustomer').on('click', function () {
        var error = "";
        $('.form-error').remove();
        $('#update-customer-wrapper').find('input').css('border', '1px solid #ccc');
        var inputCustomer = $('#update-customer-wrapper').find('input');
        for (var i = 0; i < inputCustomer.length; i++) {
            if ($(inputCustomer[i]).val() == "") {
                $(inputCustomer[i]).css('border', '1px solid red');
                $(inputCustomer[i]).parents('.input-wrapper').append('<span class="help-block form-error" style="color:red;">*Please fill this field</span>');
                error = "err";
            }
        }
        $.getJSON(ajax7, {par1: $('#acc6ID').val()}, function (data) {
            if (data > 0) {
                $('#acc6ID').parents('.input-wrapper').append('<span class="help-block form-error" style="color:red;">*This ID is already taken</span>');
                $('#acc6ID').css('border', '1px solid red');
            } else {
                if (error == "") {
                    $.getJSON(ajax6, {par1: $('#acc6ID').val(), par2: $('#acc6Name').val(), par3: $('#address').val(), par4: $('#phone').val(), par5: $('#city').val()}, function (data) {
                        $('#message-container').html('<div class="alert alert-info alert-dismissible" role="alert">\n\
<button type="button" class="close no-shadow" data-dismiss="alert" aria-label="Close">\n\
<span aria-hidden="true">×</span></button>\n\
Customer has been added</div>')
                        $('.form-error').remove();
                        $('#btn-update-costumer').click();
                        $('#update-customer-wrapper').find('input').css('border', '1px solid #ccc');
                        $('#update-customer-wrapper').find('input').val(" ");
                    });
                }
            }
        });

    });

    $("#btn-plus2").on("click", function () {
        if (($("#edit-qty").val()) == "") {
            $("#edit-qty").val(0);
        }
        $("#edit-qty").val(parseInt($("#edit-qty").val()) + 1);
        if ($("#edit-qty").val() == "" || parseInt($("#edit-qty").val()) <= 0) {
            $("#edit-qty").val(1);
        }
        $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
    });
    $("#btn-min2").on("click", function () {
        if (parseInt($("#edit-qty").val()) <= 0 || $("#edit-qty").val() == "")
            $("#edit-qty").val(1);
        $("#edit-qty").val(parseInt($("#edit-qty").val()) - 1);
        if ($("#edit-qty").val() == "" || parseInt($("#edit-qty").val()) <= 0) {
            $("#edit-qty").val(1);
        }
        $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
    });
    $('#edit-qty').on('input', function () {
        if ($("#edit-qty").val() == "" || parseInt($("#edit-qty").val()) <= 0) {
            $("#edit-qty").val(1);
            $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
        } else {
            $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
        }
    });
    $('#edit-price').on('input', function () {
        if ($("#edit-price").val() == "" || parseInt($("#edit-price").val()) < 0) {
            $("#edit-price").val(0);
        }
        $("#subtotal2").html('Rp. ' + addPeriod(parseInt($("#edit-qty").val()) * parseInt(removePeriod($("#edit-price").val(), ',')), ','));
    });
});