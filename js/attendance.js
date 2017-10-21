(function ($) {
    $(document).ready(function () {
        var today = new Date();
        var tableAttendanceField = $('#table-attendance').find('.number-calendar');
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1).getDay();

        var numberCalendarWrapper = '<tr class="number-calendar-wrapper">' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '<td class="number-calendar"></td>' +
                '</tr>';

        var AttendanceCalendar = {
            dateToSet: {
                date: today.getDate(),
                month: today.getMonth(),
                year: today.getFullYear(),
                firstDay: firstDay
            },
            getDate: {
                today: function () {
                    return today.getDate();
                },
                month: function (month) {

                    switch (month) {
                        case 0 :
                            return 'january';
                            break;
                        case 1 :
                            return 'february';
                            break;
                        case 2 :
                            return 'march';
                            break;
                        case 3 :
                            return 'april';
                            break;
                        case 4 :
                            return 'may';
                            break;
                        case 5 :
                            return 'june';
                            break;
                        case 6 :
                            return 'july';
                            break;
                        case 7 :
                            return 'august';
                            break;
                        case 8 :
                            return 'september';
                            break;
                        case 9 :
                            return 'october';
                            break;
                        case 10 :
                            return 'november';
                            break;
                        default :
                            return 'december';
                            break;
                    }
                },
                year: function () {
                    return today.getFullYear();
                },
                totalMonth: function () {
                    if ((AttendanceCalendar.dateToSet.month + 1) % 2 !== 0 ||
                            (AttendanceCalendar.dateToSet.month + 1) === 8) {
                        if ((AttendanceCalendar.dateToSet.month + 1) === 9 ||
                                (AttendanceCalendar.dateToSet.month + 1) === 11) {
                            return 30;
                        }
                        return 31;
                    } else {
                        if ((AttendanceCalendar.dateToSet.month + 1) === 2) {
                            if (AttendanceCalendar.dateToSet.year % 4 === 0) {
                                return 29;
                            } else {
                                return 28;
                            }
                        } else if ((AttendanceCalendar.dateToSet.month + 1) === 10 ||
                                (AttendanceCalendar.dateToSet.month + 1) === 12) {
                            return 31;
                        }
                        return 30;
                    }
                }
            }, getNextPrevMonth: function () {
                AttendanceCalendar.dateToSet.firstDay =
                        new Date(AttendanceCalendar.dateToSet.year, AttendanceCalendar.dateToSet.month, 1)
                        .getDay();

                //bug untuk else if dibawah ketika frebruari dan tanggal awal sunday
//                else if (AttendanceCalendar.dateToSet.firstDay >= 0 && AttendanceCalendar.getDate.totalMonth() <= 28) {
//                    $('#table-attendance').find('.number-calendar-wrapper:last-child').remove();
//                    tableAttendanceField = $('#table-attendance').find('.number-calendar');
//                }

                if (AttendanceCalendar.dateToSet.firstDay > 4 && AttendanceCalendar.getDate.totalMonth() > 30) {
                    $('#table-attendance').find('tbody').append(numberCalendarWrapper);
                    tableAttendanceField = $('#table-attendance').find('.number-calendar');
                } else {
                    if ($('#table-attendance').find('.number-calendar-wrapper').length > 5) {
                        $('#table-attendance').find('.number-calendar-wrapper:last-child').remove();
                        tableAttendanceField = $('#table-attendance').find('.number-calendar');
                    }
                }

                for (var i = 1; i <= AttendanceCalendar.getDate.totalMonth(); i++) {

                    var loopDay = new Date(AttendanceCalendar.dateToSet.year,
                            AttendanceCalendar.dateToSet.month,
                            i).getDay();

                    if (loopDay === 0 || loopDay === 6) {
                        $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).addClass('holiday');
                    } else if (loopDay === 1) {
                        $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).addClass('absent');
                    } else if (loopDay === 3) {
                        $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).addClass('absentp');
                    } else {
                        $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).addClass('present');
                    }

                    if (AttendanceCalendar.dateToSet.month === today.getMonth() &&
                            AttendanceCalendar.dateToSet.year === today.getFullYear() &&
                            i === AttendanceCalendar.getDate.today()) {
                        $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).addClass('today');
                    }

                    $(tableAttendanceField[AttendanceCalendar.dateToSet.firstDay]).text(i);
                    AttendanceCalendar.dateToSet.firstDay++;
                }
            },
            nextMonth: function () {

                $(tableAttendanceField).removeClass('today').text('');
                $(tableAttendanceField).removeClass('present').text('');
                $(tableAttendanceField).removeClass('holiday').text('');
                $(tableAttendanceField).removeClass('absent').text('');
                $(tableAttendanceField).removeClass('absentp').text('');

                if (AttendanceCalendar.dateToSet.month >= 11) {
                    $('#this-month-year').text(AttendanceCalendar.getDate.month(
                            (AttendanceCalendar.dateToSet.month = 0))
                            + ' ' + (AttendanceCalendar.dateToSet.year += 1));

                    AttendanceCalendar.getNextPrevMonth();
                } else {
                    $('#this-month-year').text(AttendanceCalendar.getDate.month(
                            (AttendanceCalendar.dateToSet.month += 1))
                            + ' ' + AttendanceCalendar.dateToSet.year);

                    AttendanceCalendar.getNextPrevMonth();
                }
            },
            prevMonth: function () {

                $(tableAttendanceField).removeClass('today').text('');
                $(tableAttendanceField).removeClass('present').text('');
                $(tableAttendanceField).removeClass('holiday').text('');
                $(tableAttendanceField).removeClass('absent').text('');
                $(tableAttendanceField).removeClass('absentp').text('');

                if (AttendanceCalendar.dateToSet.month < 1) {
                    $('#this-month-year').text(AttendanceCalendar.getDate.month(
                            (AttendanceCalendar.dateToSet.month = 11))
                            + ' ' + (AttendanceCalendar.dateToSet.year -= 1));

                    AttendanceCalendar.getNextPrevMonth();
                } else {
                    $('#this-month-year').text(AttendanceCalendar.getDate.month(
                            (AttendanceCalendar.dateToSet.month -= 1))
                            + ' ' + AttendanceCalendar.dateToSet.year);

                    AttendanceCalendar.getNextPrevMonth();
                }
            },
            setFirstCalendar: function () {
                firstDay = new Date(today.getFullYear(), today.getMonth(), 1).getDay();

                $(tableAttendanceField).removeClass('today').text('');
                $(tableAttendanceField).removeClass('present').text('');
                $(tableAttendanceField).removeClass('holiday').text('');
                $(tableAttendanceField).removeClass('absent').text('');
                $(tableAttendanceField).removeClass('absentp').text('');

                $('#this-month-year').text(AttendanceCalendar.getDate.month(
                        (AttendanceCalendar.dateToSet.month = today.getMonth()))
                        + ' ' + (AttendanceCalendar.dateToSet.year = AttendanceCalendar.getDate.year()));

                if (firstDay > 4 && AttendanceCalendar.getDate.totalMonth() > 30) {
                    $('#table-attendance').find('tbody').append(numberCalendarWrapper);
                    tableAttendanceField = $('#table-attendance').find('.number-calendar');
                } else {
                    if ($('#table-attendance').find('.number-calendar-wrapper').length > 5) {
                        $('#table-attendance').find('.number-calendar-wrapper:last-child').remove();
                        tableAttendanceField = $('#table-attendance').find('.number-calendar');
                    }
                }


                for (var i = 1; i <= AttendanceCalendar.getDate.totalMonth(); i++) {

                    var loopDay = new Date(today.getFullYear(), today.getMonth(), i).getDay();

                    if (loopDay === 0 || loopDay === 6) {
                        $(tableAttendanceField[firstDay]).addClass('holiday');
                    } else if (loopDay === 1 || loopDay === 3) {
                        $(tableAttendanceField[firstDay]).addClass('absent');
                    } else if (loopDay === 4) {
                        $(tableAttendanceField[firstDay]).addClass('absentp');
                    } else {
                        $(tableAttendanceField[firstDay]).addClass('present');
                    }

                    if (i === AttendanceCalendar.getDate.today()) {
                        $(tableAttendanceField[firstDay]).addClass('today');
                    }

                    $(tableAttendanceField[firstDay]).text(i);
                    firstDay++;
                }
            }
        };

        AttendanceCalendar.setFirstCalendar();
        $('#prev-month').on('click', AttendanceCalendar.prevMonth);
        $('#next-month').on('click', AttendanceCalendar.nextMonth);
        $('#today-month').on('click', AttendanceCalendar.setFirstCalendar);
    });
})(jQuery);