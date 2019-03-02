/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {

    /**
     * User Analytics Search List
     */

    $(document).on('click', '.analyticsfilter-btn', function () {
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        analyticsDataBySearch(from_date, to_date);
    });

    /**
     * Search function for dashboard analytics
     * @param date from_date
     * @param date to_date
     * @returns Array
     */
    function analyticsDataBySearch(from_date, to_date) {
        $.ajax({
            url: 'analytics-data-search-list',
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            type: "POST",
            data: JSON.stringify({from_date: from_date, to_date: to_date}),
            headers:
                    {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
            success: function (confirmdata) {
                var occupation_length = confirmdata.occupation_analytics.length;
                var occupation_list_html = "";
                var age_list_html = "";
                var gender_list_html = "";
                var bracket_list_html = "";
                if (occupation_length == 0) {
                    occupation_list_html += '<tr>\
                                        <td colspan="2">No daters available</td>\
                                      </tr>';
                } else {
                    $.each(confirmdata.occupation_analytics, function (key, value) {
                        occupation_list_html += '<tr>\
                                        <td>' + key + '</td>\
                                        <td>' + value + '</td>\
                                    </tr>';
                    });
                }

                $.each(confirmdata.age_analytics, function (key, value) {
                    age_list_html += '<tr>\
                                    <td>' + key + '</td>\
                                    <td>' + value + '</td>\
                                </tr>';
                });

                $.each(confirmdata.gender_analytics, function (key, value) {
                    gender_list_html += '<tr>\
                                    <td>No. of ' + key.charAt(0).toUpperCase() + key.slice(1) + ' Daters</td>\
                                    <td>' + value + '</td>\
                                </tr>';
                });

                $.each(confirmdata.bracket_analytics, function (key, value) {
                    bracket_list_html += '<tr>\
                                    <td>No. of ' + key + '</td>\
                                    <td>' + value + '</td>\
                                </tr>';
                });

                $("#gender-analytics").html(gender_list_html);
                $("#age-analytics").html(age_list_html);
                $("#occupation-analytics").html(occupation_list_html);
                $("#bracket-analytics").html(bracket_list_html);
                $(".download-xls-btn a").attr('href', domain_url + '/admin/downloadExcel?from_date=' + from_date + '&to_date=' + to_date);

            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    msg = 'Please verify your network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
                location.href = domain_url + "/admin/login";
            }
        });
    }

    $(document).on('click', '.inapp_analytics_tab', function () {
        inAppDataAnalytics('', '');
    });

    /**
     * User Analytics Search List
     */
    $(document).on('click', '.inapp-analyticsfilter-btn', function () {
        var search_str = $("#daterange_search_inapp").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        inAppDataAnalytics(from_date, to_date);
    });

    /**
     * Search function for inapp analytics
     * @param date from_date
     * @param date to_date
     * @returns Array
     */
    function inAppDataAnalytics(from_date, to_date) {
        $.ajax({
            url: 'in-app-analytics-data',
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            type: "POST",
            data: JSON.stringify({from_date: from_date, to_date: to_date}),
            headers:
                    {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
            success: function (confirmdata) {
                var inapp_list_html = "";
                var amount_list_html = "";
                $.each(confirmdata.in_app_analytics, function (key, value) {
                    inapp_list_html += '<tr>\
                                        <td>' + key + '</td>\
                                        <td>' + value + '</td>\
                                    </tr>';
                });

                $.each(confirmdata.amount_analytics, function (key, value) {
                    amount_list_html += '<tr>\
                                        <td>' + key + '</td>\
                                        <td>$' + value + '</td>\
                                    </tr>';
                });

                $("#inapp-analytics").html(inapp_list_html);
                $("#amount-analytics").html(amount_list_html);
                $(".inapp-download-xls-btn a").attr('href', domain_url + '/admin/inAppDownloadExcel?from_date=' + from_date + '&to_date=' + to_date);

            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    msg = 'Please verify your network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert(msg);
                location.href = domain_url + "/admin/login";
            }
        });
    }

});


