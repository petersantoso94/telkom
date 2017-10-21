(function ($) {
    $(document).ready(function () {

        //declare interaction

        var toggleTabNav = function () {

            var sectionTab = $(this).attr('data-show');
            var sectionNotThisTab = $(this).siblings('.tab-nav').attr('data-show');

            if ($(this).hasClass('active-tab'))
                return;

            $(this).addClass('active-tab');
            $($(this).siblings('.tab-nav')).removeClass('active-tab');
            $(sectionTab).show();
            $(sectionNotThisTab).hide();
        };

        var toggleSideNav = function (e) {
            e.stopPropagation();

            if ($(e.target).hasClass('sidebar-open')) {
                
                $('#sidebar .sidebar-sheet').removeClass('sidebar-sheet-open');
                setTimeout(function () {
                    $('#sidebar').removeClass('sidebar-open');
                }, 200);
                
                return;
            }

            $('#sidebar').addClass('sidebar-open');
            setTimeout(function () {
                $('#sidebar .sidebar-sheet').addClass('sidebar-sheet-open');
            }, 200);
        };

        //attach event

        $('.tab-nav').on('click', toggleTabNav);
        $('#logo').on('click', toggleSideNav);
        $('#sidebar').on('click', toggleSideNav);

    });
})(jQuery);
