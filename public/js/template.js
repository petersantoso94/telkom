
$(document).ready(function () {
    
    var windowWidth = $(window).width();
    
    $('.side-nav').mCustomScrollbar({
        scrollbarPosition: "outside",
        autoExpandScrollbar: true,
        theme: "minimal-dark"
    });

    if (us != '-1') {
        $('.accchild,.masterchild,.transactionchild,.settingchild,.utilitychild,.accdecendchild').hide();
        if (toogle != 'accounting') {
            $('.accdecend').hide();
        }
        $('.accountind').click(function () {
            $('.accchild,.accdecend').slideToggle();

            if ($('.accdecendchild').hasClass('decendlive')) {
                $('.accdecendchild').slideUp();
                $('.accdecend').addClass('parentlive');
            } else {
                $('.accdecend').removeClass('parentlive');
            }

            $(this).toggleClass('parentlive');
            $('.master,.transaction,.utility,.setting').removeClass('parentlive');
            $('.masterchild,.transactionchild,.utilitychild,.settingchild').slideUp();
        });
        $('.accdecend').click(function () {
            $('.accdecendchild').slideToggle();
            $('.accdecendchild').addClass('decendlive');
            $(this).toggleClass('parentlive');
            $('.master,.transaction,.utility,.setting').removeClass('parentlive');
            $('.masterchild,.transactionchild,.utilitychild,.settingchild').slideUp();
        });
        $('.transaction').click(function () {
            $('.transactionchild').slideToggle();
            $('.accdecend').removeClass('parentlive');
            $('.accdecendchild').removeClass('decendlive');
            $(this).toggleClass('parentlive');
            $('.master,.accountind,.utility,.setting').removeClass('parentlive');
            $('.masterchild,.accchild,.utilitychild,.settingchild,.accdecend,.accdecendchild').slideUp();
        });
        $('.setting').click(function () {
            $('.settingchild').slideToggle();
            $('.accdecend').removeClass('parentlive');
            $('.accdecendchild').removeClass('decendlive');
            $(this).toggleClass('parentlive');
            $('.transaction,.accountind,.master,.utility').removeClass('parentlive');
            $('.masterchild,.transactionchild,.utilitychild,.accchild,.accdecend,.accdecendchild').slideUp();
        });
        $('.utility').click(function () {
            $('.utilitychild').slideToggle();
            $('.accdecend').removeClass('parentlive');
            $('.accdecendchild').removeClass('decendlive');
            $(this).toggleClass('parentlive');
            $('.transaction,.accountind,.master,.setting').removeClass('parentlive');
            $('.masterchild,.transactionchild,.accchild,.settingchild,.accdecend,.accdecendchild').slideUp();
        });

        if (toogle == 'accounting') {
            $('.accchild').slideDown();
            $('.accountind').addClass('parentlive');
            if (halamanAktif == 'coaLevel' || halamanAktif == 'coa5' || halamanAktif == 'coa6' || halamanAktif == 'coa') {
                $('.accdecend').addClass('parentlive');
                $('.accdecendchild').slideDown();
            }
        }
        if (toogle == 'transaction') {
            $('.transactionchild').slideDown();
            $('.transaction').addClass('parentlive');
        }
        if (toogle == 'setting') {
            $('.settingchild').slideDown();
            $('.setting').addClass('parentlive');
        }
        if (toogle == 'master') {
            $('.masterchild').slideDown();
            $('.master').addClass('parentlive');
        }
        if (toogle == 'utility') {
            $('.utilitychild').slideDown();
            $('.utility').addClass('parentlive');
        }
    } else {
        $('.master').addClass('parentlive');
        $('.masterchild').slideDown();
    }

    $('.master').click(function () {
        $('.masterchild').slideToggle();
        $('.accdecend').removeClass('parentlive');
        $('.accdecendchild').removeClass('decendlive');
        $(this).toggleClass('parentlive');
        $('.accountind,.transaction,.utility,.setting').removeClass('parentlive');
        $('.accchild,.transactionchild,.utilitychild,.settingchild,.accdecend,.accdecendchild').slideUp();
    });

    if (windowWidth <= 1220) {
        $('.side-nav').animate({'left': '-200px'}, 200);
        $('.wrap').animate({'paddingLeft': '0px'}, 200);
        $('.hamb').animate({'left': '15px'}, 200);
        $('.dataTables_length').animate({'left': '60px'}, 200);
    }

    $(window).resize(function () {
        windowWidth = $(window).width();

        if (windowWidth <= 1220) {
            $('.side-nav').animate({'left': '-200px'}, 200);
            $('.wrap').animate({'paddingLeft': '0px'}, 200);
            $('.hamb').animate({'left': '15px'}, 200);
            $('.dataTables_length').animate({'left': '60px'}, 200);
        }
        else {
            $('.wrap').animate({'paddingLeft': '200px'}, 200);
            $('.hamb').animate({'left': '215px'}, 200);
            $('.side-nav').animate({'left': '0px'}, 200);
            $('.dataTables_length').animate({'left': '270px'}, 200);
        }
    });



    $('.hamb').on('click', function () {

        if (windowWidth >= 1220) {
            if ($('.wrap').css('paddingLeft') == '0px') {
                $('.wrap').animate({'paddingLeft': '200px'}, 200);
            } else {
                $('.wrap').animate({'paddingLeft': '0px'}, 200);
            }
        }

        if (windowWidth >= 768) {
            if ($(this).css('left') == '215px') {
                $(this).animate({'left': '15px'}, 200);
            } else {
                $(this).animate({'left': '215px'}, 200);
            }
        }

        if ($('.dataTables_length').css('left') == '60px') {
            $('.dataTables_length').animate({'left': '270px'}, 200);
        } else {
            $('.dataTables_length').animate({'left': '60px'}, 200);
        }

        window.sideNavToggle = function() {
            if ($('.side-nav').css('left') == '-200px') {
                $('.side-nav').animate({'left': '0px'}, 200);
            } else {
                $('.side-nav').animate({'left': '-200px'}, 200);
            }
        }
        
        sideNavToggle();

    });
    
    $('#close-side-nav').on('click', function() {
        sideNavToggle();
    });

});