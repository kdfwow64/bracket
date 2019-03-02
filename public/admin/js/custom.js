$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

/**
 * 
 * @type window.location.href|DOMString
 * Get current url
 */
var url = window.location.href;

/**
 * Get domain name 
 */
domain_url = '';
if (document.domain == 'localhost') {
    domain_url = 'http://localhost/bracket_php/public';
} else {
    domain_url = 'https://' + document.domain;
}

/* Function for converting UTC to local time */

function convertUTCDateToLocalDate(dateString) {

    var reggie = /(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2}):(\d{2})/;
    var dateArray = reggie.exec(dateString);
    var dateObject = new Date(
            (+dateArray[3]),
            (+dateArray[1]) - 1, // Careful, month starts at 0!
            (+dateArray[2]),
            (+dateArray[4]),
            (+dateArray[5]),
            (+dateArray[6])
            );

    var date_to_string = new Date(dateObject).toString();
    var newDate = new Date(date_to_string);

    var offset = newDate.getTimezoneOffset();
    var minutes = newDate.getMinutes();
    newDate.setMinutes(minutes - offset);
    var month = newDate.getMonth() + 1;
    var day = newDate.getDate();
    var year = newDate.getFullYear();
    var hour = newDate.getHours();
    if(hour < 10) hour = "0"+hour;
    var minute = newDate.getMinutes();
    if(minute < 10) minute = "0"+minute;
    return month + "/" + day + "/" + year + " " + hour + ":" + minute;

}

/*
 * check if the input is number or not
 * @param {type} n
 * @returns {Boolean}
 */
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

/*
 * Convert local date to UTC
 * @param {date} local_date
 */
function convertLocalDateToUTCDate(local_date) {
    var month = local_date.getUTCMonth() + 1;
    var day = local_date.getUTCDate();
    var year = local_date.getUTCFullYear();
    var hour = local_date.getUTCHours();
    var minute = local_date.getUTCMinutes();
    var second = local_date.getUTCSeconds();
    return month + "/" + day + "/" + year + " " + hour + ":" + minute + ":" + second;
}


$(document).ready(function () {

    /**
     * initialize datepicker range
     */
    $("#daterange_search").attr("placeholder", "Select Period");
    $("#daterange_search_inapp").attr("placeholder", "Select Period");
    $('#daterange_search').daterangepicker({
        autoUpdateInput: false,
        maxDate: new Date(),
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('#daterange_search').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY 00:00:00') + ' - ' + picker.endDate.format('MM/DD/YYYY 23:59:59'));
    });

    $('#daterange_search').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    $('#daterange_search_inapp').daterangepicker({
        autoUpdateInput: false,
        maxDate: new Date(),
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('#daterange_search_inapp').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY 00:00:00') + ' - ' + picker.endDate.format('MM/DD/YYYY 23:59:59'));
    });

    $('#daterange_search_inapp').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });


    /**
     * Function for the tab functionality in user detail page
     */
    $(".nav-tabs a").click(function () {
        $(this).tab('show');
    });


});    