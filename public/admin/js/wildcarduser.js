$(document).ready(function () {
    /**
     * Wildcard user listing pagination
     */
    $(document).on('click', '.wildcarduser_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        getWildcardUsersList(pageNo);
    });

    /* Get the list of wildcard daters on page load */
    getWildcardUsersList(1)

    /**
     * List of wildcard users
     */
    function getWildcardUsersList(page) {
        $.ajax({
            url: 'wildcard-user-list-ajax?page=' + page,
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
                                        <td colspan="7">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].created).toLocaleString();
                        if (confirmdata.data[i].wildcard_dater.profile_picture == null || confirmdata.data[i].wildcard_dater.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].wildcard_dater.profile_picture;
                        }
                        if (confirmdata.data[i].wildcard_dater.average_rating == 1) {
                            bucket = '1 and 2';
                        } else if (confirmdata.data[i].wildcard_dater.average_rating == 5) {
                            bucket = '4 and 5';
                        } else {
                            bucket = parseInt(confirmdata.data[i].wildcard_dater.average_rating - 1) + ' , ' + confirmdata.data[i].wildcard_dater.average_rating + ' and ' + parseInt(confirmdata.data[i].wildcard_dater.average_rating + 1)
                        }
                        if (confirmdata.data[i].wildcard_dater.email == "")
                            confirmdata.data[i].wildcard_dater.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].wildcard_dater.first_name + ' ' + (confirmdata.data[i].wildcard_dater.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].wildcard_dater.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + bucket + '</td>\
                                        <td>' + finaldate + '</td>\
                                        <td>' + confirmdata.data[i].total + '</td>\
                                        <td>' + confirmdata.data[i].wildcard_dater.rating_done + '</td>\
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
                    pag_list += "<li><a href='javascript:;' class='wildcard_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='wildcarduser_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='wildcard_page_inc'>>></a></li>";
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
    $(document).on("click", ".wildcard_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        })
        getWildcardUsersList(nxtpage);
    });

    /**
     * Previous page of wildcard user list
     */
    $(document).on("click", ".wildcard_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        })
        getWildcardUsersList(prevpage);
    });


    /**
     * wildcard user Search List
     */
    $(document).on('click', '.wildcardsearch-btn', function () {
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        wildcardListBySearch(from_date, to_date, 1);
    });

    /**
     * 
     * @param {integer} userid
     * @param {integer} page
     * @returns {array}
     * Function to load the list of blocked user
     */
    function wildcardListBySearch(from_date, to_date, page) {
        $.ajax({
            url: 'wildcard-user-search-list?page=' + page,
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
                var length = confirmdata.data.length;
                var list_html = "";
                if (confirmdata.data.length == 0) {
                    list_html += '<tr>\
                                        <td colspan="7">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].created).toLocaleString();
                        if (confirmdata.data[i].wildcard_dater.profile_picture == null || confirmdata.data[i].wildcard_dater.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].wildcard_dater.profile_picture;
                        }
                        if (confirmdata.data[i].wildcard_dater.average_rating == 1) {
                            bucket = '1 and 2';
                        } else if (confirmdata.data[i].wildcard_dater.average_rating == 5) {
                            bucket = '4 and 5';
                        } else {
                            bucket = parseInt(confirmdata.data[i].wildcard_dater.average_rating - 1) + ' , ' + confirmdata.data[i].wildcard_dater.average_rating + ' and ' + parseInt(confirmdata.data[i].wildcard_dater.average_rating + 1)
                        }
                        if (confirmdata.data[i].wildcard_dater.email == "")
                            confirmdata.data[i].wildcard_dater.email = "Not Shared";
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].wildcard_dater.first_name + ' ' + (confirmdata.data[i].wildcard_dater.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].wildcard_dater.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + bucket + '</td>\
                                        <td>' + finaldate + '</td>\
                                        <td>' + confirmdata.data[i].total + '</td>\
                                        <td>' + confirmdata.data[i].wildcard_dater.rating_done + '</td>\
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
                    pag_list += "<li><a href='javascript:;' class='filter_wildcard_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='filter_wildcarduser_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='filter_wildcard_page_inc'>>></a></li>";
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
    $(document).on('click', '.filter_wildcarduser_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        wildcardListBySearch(from_date, to_date, pageNo);
    });

    /**
     * Next page of filter rating list
     */
    $(document).on("click", ".filter_wildcard_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        });
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        wildcardListBySearch(from_date, to_date, nxtpage);
    });

    /**
     * Previous page of filter rating list
     */
    $(document).on("click", ".filter_wildcard_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        });
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        wildcardListBySearch(from_date, to_date, prevpage);
    });

});    