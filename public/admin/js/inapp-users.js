$(document).ready(function () {

    /* Get the list of inapp users on page load */
    getInAppUsersList(1);

    /**
     * Wildcard user listing pagination
     */
    $(document).on('click', '.inapp_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        getInAppUsersList(pageNo);
    });

    /**
     * List of wildcard users
     */
    function getInAppUsersList(page) {
        $.ajax({
            url: 'in-app-user-list-ajax?page=' + page,
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            type: "GET",
            success: function (confirmdata) {
                var length = confirmdata.data.length;
                var list_html = "";
                if (confirmdata.data.length == 0) {
                    list_html += '<tr>\
                                        <td colspan="6">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].created).toLocaleString();
                        if (confirmdata.data[i].type == 2) {
                            subscription_type = 'Monthly Subscription';
                        } else {
                            subscription_type = 'Additional Bracket';
                        }
                        if (confirmdata.data[i].subscribed_dater.email == "")
                            confirmdata.data[i].subscribed_dater.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].subscribed_dater.first_name + ' ' + (confirmdata.data[i].subscribed_dater.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].subscribed_dater.email + '</td>\
                                        <td>' + subscription_type + '</td>\
                                        <td>' + confirmdata.data[i].total + '</td>\
                                        <td>' + finaldate + '</td>\
                                        <td>$' + confirmdata.data[i].amount + '</td>\
                                    </tr>';
                    }
                }
                $("#users-list").html(list_html);
                if (confirmdata.last_page == 0)
                    confirmdata.last_page = 1;
                $(".totalList").html("Page " + confirmdata.current_page + " of " + confirmdata.last_page);
                var pag_list = "";
                var cls = "";
                if (page != 1) {
                    pag_list += "<li><a href='javascript:;' class='inapp_page_dec'><<</a></li>";
                }
                var fromloop = 1;
                var toloop = 10;
                if (page > 5) {
                    fromloop = page - 4;
                    toloop = parseInt(page) + 5;
                }
                if (toloop > confirmdata.last_page) {
                    toloop = confirmdata.last_page;
                }
                if (page == confirmdata.last_page && confirmdata.last_page > 10) {
                    fromloop = page - 10;
                }
                for (i = fromloop; i <= toloop; i++) {
                    if (i == confirmdata.current_page) {
                        cls = "page_active";
                    } else {
                        cls = "";
                    }
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='inapp_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='inapp_page_inc'>>></a></li>";
                }

                $(".pagination").html(pag_list);
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

    /**
     * Next page of wildcard user list
     */
    $(document).on("click", ".inapp_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        })
        getInAppUsersList(nxtpage);
    });

    /**
     * Previous page of wildcard user list
     */
    $(document).on("click", ".inapp_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        })
        getInAppUsersList(prevpage);
    });

});    