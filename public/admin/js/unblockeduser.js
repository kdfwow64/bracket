$(document).ready(function () {
    /**
     * Blocked user listing pagination
     */
    $(document).on('click', '.blocked_user_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        getBlockedUsersList(pageNo);
    });

    /**
     * List of blocked users
     */
    function getBlockedUsersList(page) {
        $.ajax({
            url: 'unblocked-user-list-ajax?page=' + page,
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
                                        <td colspan="4">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        if (confirmdata.data[i].blocked_to.profile_picture == null || confirmdata.data[i].blocked_to.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].blocked_to.profile_picture;
                        }
                        if (confirmdata.data[i].blocked_to.email == "")
                            confirmdata.data[i].blocked_to.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].blocked_to.first_name + ' ' + (confirmdata.data[i].blocked_to.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].blocked_to.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td><a href="unblocked-user/' + confirmdata.data[i].blocked_user_id + '">' + confirmdata.data[i].total + ' dater(s)</a></td>\
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
                    pag_list += "<li><a href='javascript:;' class='blocked_user_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='blocked_user_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='blocked_user_page_inc'>>></a></li>";
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
     * Next page of blocked user list
     */
    $(document).on("click", ".blocked_user_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        })
        getBlockedUsersList(nxtpage);
    });

    /**
     * Previous page of blocked user list
     */
    $(document).on("click", ".blocked_user_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        })
        getBlockedUsersList(prevpage);
    });

    /* ==================================== */

    /* Get list of unblocked by users on page load */
    var uri = window.location.href;
    var lastslashindex = uri.split('/').pop();

    if (isNumber(lastslashindex) == true) {
        getBlockedbyUsersList(1, lastslashindex);
    }

    /**
     * Blocked user listing pagination
     */
    $(document).on('click', '.blockedby_user_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        var userid = $(this).attr('userid');
        getBlockedbyUsersList(pageNo, userid);
    });

    /**
     * List of unblocked users
     */
    function getBlockedbyUsersList(page, userid) {
        $.ajax({
            url: '../unblockedby-user-list-ajax?page=' + page + '&id=' + userid,
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
                                        <td colspan="5">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].deleted).toLocaleString();
                        if (confirmdata.data[i].blocked_by.profile_picture == null || confirmdata.data[i].blocked_by.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].blocked_by.profile_picture;
                        }
                        if (confirmdata.data[i].blocked_by.email == "")
                            confirmdata.data[i].blocked_by.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].blocked_by.first_name + ' ' + (confirmdata.data[i].blocked_by.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].blocked_by.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + finaldate + '</td>\
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
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='blockedby_user_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' userid='" + userid + "' class='blockedby_user_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='blockedby_user_page_inc'>>></a></li>";
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
     * Next page of blocked user list
     */
    $(document).on("click", ".blockedby_user_page_inc", function () {
        var nxtpage;
        var userid = $(this).attr('userid');
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        })
        getBlockedbyUsersList(nxtpage, userid);
    });

    /**
     * Previous page of blocked user list
     */
    $(document).on("click", ".blockedby_user_page_dec", function () {
        var prevpage;
        var userid = $(this).attr('userid');
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        })
        getBlockedbyUsersList(prevpage, userid);
    });

    /**
     * Blocked user Search List
     */
    $(document).on('click', '.blockedusersearch-btn', function () {
        var user_id = $(this).attr('user_id');
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        blockeduserListBySearch(from_date, to_date, user_id, 1);
    });

    /**
     * 
     * @param {integer} userid
     * @param {integer} page
     * @returns {array}
     * Function to load the list of blocked user
     */
    function blockeduserListBySearch(from_date, to_date, userid, page) {
        $.ajax({
            url: '../unblocked-user-search-list?page=' + page,
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            type: "POST",
            data: JSON.stringify({userid: userid, from_date: from_date, to_date: to_date}),
            headers:
                    {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
            success: function (confirmdata) {
                var length = confirmdata.data.length;
                var list_html = "";
                if (confirmdata.data.length == 0) {
                    list_html += '<tr>\
                                        <td colspan="5">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].deleted).toLocaleString();
                        if (confirmdata.data[i].blocked_by.profile_picture == null || confirmdata.data[i].blocked_by.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].blocked_by.profile_picture;
                        }
                        if (confirmdata.data[i].blocked_by.email == "")
                            confirmdata.data[i].blocked_by.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].blocked_by.first_name + ' ' + (confirmdata.data[i].blocked_by.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].blocked_by.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + finaldate + '</td>\
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
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='filter_blockedby_user_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' userid='" + userid + "' class='filter_blockedby_user_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='filter_blockedby_user_page_inc'>>></a></li>";
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
     * Filter Blocked User list pagination
     */
    $(document).on('click', '.filter_blockedby_user_page_num', function () {
        var pageNoId = $(this).attr('id');
        var userid = $(this).attr('userid');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        blockeduserListBySearch(from_date, to_date, userid, pageNo);
    });

    /**
     * Next page of filter rating list
     */
    $(document).on("click", ".filter_blockedby_user_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        });
        var userid = $(this).attr('userid');
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        blockeduserListBySearch(from_date, to_date, userid, nxtpage);
    });

    /**
     * Previous page of filter rating list
     */
    $(document).on("click", ".filter_blockedby_user_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        });
        var userid = $(this).attr('userid');
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        blockeduserListBySearch(from_date, to_date, userid, prevpage);
    });

});    