$(document).ready(function () {

    /* Search on enter */
    $('#usersearch').keypress(function (event) {
        if (event.keyCode == 13) {
            $('.usersearch-btn').click();
        }
    });

    /**
     * User Search List
     */
    $(document).on('click', '.usersearch-btn', function () {
        var search_str = $("#usersearch").val();
        userListBySearch(search_str, 1);
    });

    /**
     * User search List By Pagination
     */
    $(document).on('click', '.user_search_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        var search_str = $("#usersearch").val();
        userListBySearch(search_str, pageNo);
    })

    /**
     * Function for searching the user
     */
    function userListBySearch(search_str, pageNo) {
        $.ajax({
            url: 'search-user?query=' + search_str + '&page=' + pageNo,
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
                        if (confirmdata.data[i].profile_picture == null || confirmdata.data[i].profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].profile_picture;
                        }
                        if (confirmdata.data[i].gender == 1) {
                            gender = "Male";
                        } else {
                            gender = "Female";
                        }
                        if (confirmdata.data[i].email == "") {
                            confirmdata.data[i].email = "Not Shared";
                        }
                        if (confirmdata.data[i].age == 0) {
                            confirmdata.data[i].age = "Not Shared";
                        }
                        if (confirmdata.data[i].country == "") {
                            confirmdata.data[i].country = "Not Shared";
                        }
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].first_name + ' ' + (confirmdata.data[i].last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + confirmdata.data[i].age + '</td>\
                                        <td>' + confirmdata.data[i].country + '</td>\
                                        <td>' + gender + '</td>\
                                        <td><a href=' + url + '/' + confirmdata.data[i].id + '><button type="button" class="btn btn-sm btn-success view viewbtn">View Profile</button></a> </td>\
                                    </tr>';
                    }
                }
                $("#users-list").html(list_html);
                if (confirmdata.last_page == 0)
                    confirmdata.last_page = 1;
                $(".totalList").html("Page " + confirmdata.current_page + " of " + confirmdata.last_page);
                var pag_list = "";
                var cls = "";
                if (pageNo != 1) {
                    pag_list += "<li><a href='javascript:;' class='search_page_dec'><<</a></li>";
                }
                var fromloop = 1;
                var toloop = 10;
                if (pageNo > 5) {
                    fromloop = pageNo - 4;
                    toloop = parseInt(pageNo) + 5;
                }
                if (toloop > confirmdata.last_page) {
                    toloop = confirmdata.last_page;
                }
                if (pageNo == confirmdata.last_page && confirmdata.last_page > 10) {
                    fromloop = pageNo - 10;
                }
                for (i = fromloop; i <= toloop; i++) {
                    if (i == confirmdata.current_page) {
                        cls = "page_active";
                    } else {
                        cls = "";
                    }
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='user_search_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (pageNo != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='search_page_inc'>>></a></li>";
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
     * User Pagination
     */
    $(document).on('click', '.user_page_num', function () {
        var pageNoId = $(this).attr('id');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        userList(pageNo);
    });

    /**
     * Function for listing the user
     */
    function userList(page) {
        $.ajax({
            url: 'user-list-ajax?page=' + page,
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
                        if (confirmdata.data[i].profile_picture == null || confirmdata.data[i].profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.data[i].profile_picture;
                        }
                        if (confirmdata.data[i].gender == 1) {
                            gender = "Male";
                        } else {
                            gender = "Female";
                        }
                        if (confirmdata.data[i].email == "") {
                            confirmdata.data[i].email = "Not Shared";
                        }
                        if (confirmdata.data[i].age == 0) {
                            confirmdata.data[i].age = "Not Shared";
                        }
                        if (confirmdata.data[i].country == "") {
                            confirmdata.data[i].country = "Not Shared";
                        }
                        list_html += '<tr>\
                                        <td>' + confirmdata.data[i].first_name + ' ' + (confirmdata.data[i].last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + confirmdata.data[i].age + '</td>\
                                        <td>' + confirmdata.data[i].country + '</td>\
                                        <td>' + gender + '</td>\
                                        <td><a href=' + url + '/' + confirmdata.data[i].id + '><button type="button" class="btn btn-sm btn-success view viewbtn">View Profile</button></a> </td>\
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
                    pag_list += "<li><a href='javascript:;' class='user_page_dec'><<</a></li>";
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
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='user_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.last_page) {
                    pag_list += "<li><a href='javascript:;' class='user_page_inc'>>></a></li>";
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
     * Next page of user list
     */
    $(document).on("click", ".user_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        })
        userList(nxtpage);
    });

    /**
     * Previous page of user list
     */
    $(document).on("click", ".user_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        })
        userList(prevpage);
    });

    /**
     * Next page of searched user list
     */
    $(document).on("click", ".search_page_inc", function () {
        var nxtpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                nxtpage = parseInt(pageNo) + 1;
            }
        });
        var search_str = $("#usersearch").val();
        userListBySearch(search_str, nxtpage);
    });

    /**
     * Previous page of searched user list
     */
    $(document).on("click", ".search_page_dec", function () {
        var prevpage;
        $(".pagination li").each(function () {
            if ($(this).find("a").hasClass("page_active")) {
                var pagenum = $(this).find("a").attr("id");
                var pageNoArr = pagenum.split('_');
                var pageNo = pageNoArr[1];
                prevpage = parseInt(pageNo) - 1;
            }
        });
        var search_str = $("#usersearch").val();
        userListBySearch(search_str, prevpage);
    });

    /**
     * Get the list of ratings on click of dater rating tab
     */
    $(document).on("click", ".get_ratings", function () {
        userid = $(this).attr("userid");
        getUserRatings(userid, 1);
    });

    /**
     * User Rating list pagination
     */
    $(document).on('click', '.user_rating_page_num', function () {
        var pageNoId = $(this).attr('id');
        var userid = $(this).attr('userid');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        getUserRatings(userid, pageNo);
    });

    /**
     * 
     * @param {integer} userid
     * @param {integer} page
     * @returns {array}
     * Function to load the list of user umojis
     */
    function getUserRatings(userid, page) {
        $.ajax({
            url: '../user-rating-list?page=' + page,
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            type: "POST",
            data: JSON.stringify({userid: userid}),
            headers:
                    {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
            success: function (confirmdata) {
                var length = confirmdata.users.data.length;
                var list_html = "";
                if (confirmdata.users.data.length == 0) {
                    list_html += '<tr>\
                                        <td colspan="5">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.users.data[i].created).toLocaleString();
                        if (confirmdata.users.data[i].rating_by.profile_picture == null || confirmdata.users.data[i].rating_by.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.users.data[i].rating_by.profile_picture;
                        }
                        list_html += '<tr>\
                                        <td>' + confirmdata.users.data[i].rating_by.first_name + ' ' + (confirmdata.users.data[i].rating_by.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.users.data[i].rating_by.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + confirmdata.rating_array[confirmdata.users.data[i].rating_to.gender][confirmdata.users.data[i].rating_number].name + '</td>\
                                        <td>' + finaldate + '</td>\
                                    </tr>';
                    }
                }
                $("#users-rating-list").html(list_html);
                if (confirmdata.users.last_page == 0)
                    confirmdata.users.last_page = 1;
                $(".totalList").html("Page " + confirmdata.users.current_page + " of " + confirmdata.users.last_page);
                var pag_list = "";
                var cls = "";
                if (page != 1) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='rating_page_dec'><<</a></li>";
                }
                var fromloop = 1;
                var toloop = 10;
                if (page > 5) {
                    fromloop = page - 4;
                    toloop = parseInt(page) + 5;
                }
                if (toloop > confirmdata.users.last_page) {
                    toloop = confirmdata.users.last_page;
                }
                if (page == confirmdata.users.last_page && confirmdata.users.last_page > 10) {
                    fromloop = page - 10;
                }
                for (i = fromloop; i <= toloop; i++) {
                    if (i == confirmdata.users.current_page) {
                        cls = "page_active";
                    } else {
                        cls = "";
                    }
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' userid='" + userid + "' class='user_rating_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.users.last_page) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='rating_page_inc'>>></a></li>";
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
     * Next page of rating list
     */
    $(document).on("click", ".rating_page_inc", function () {
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
        getUserRatings(userid, nxtpage);
    });

    /**
     * Previous page of rating list
     */
    $(document).on("click", ".rating_page_dec", function () {
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
        getUserRatings(userid, prevpage);
    });

    /**
     * Rating Search List
     */
    $(document).on('click', '.ratingsearch-btn', function () {
        var user_id = $(this).attr('user_id');
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        ratingListBySearch(from_date, to_date, user_id, 1);
    });

    /**
     * 
     * @param {integer} userid
     * @param {integer} page
     * @returns {array}
     * Function to load the list of user ratings
     */
    function ratingListBySearch(from_date, to_date, userid, page) {
        $.ajax({
            url: '../user-rating-search-list?page=' + page,
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
                var length = confirmdata.users.data.length;
                var list_html = "";
                if (confirmdata.users.data.length == 0) {
                    list_html += '<tr>\
                                        <td colspan="5">No records found</td>\
                                      </tr>';
                } else {
                    for (i = 0; i < length; i++) {
                        var finaldate = convertUTCDateToLocalDate(confirmdata.users.data[i].created).toLocaleString();
                        if (confirmdata.users.data[i].rating_by.profile_picture == null || confirmdata.users.data[i].rating_by.profile_picture == "") {
                            profileimg = domain_url + "/admin/img/default-img.png";
                        } else {
                            profileimg = confirmdata.users.data[i].rating_by.profile_picture;
                        }
                        list_html += '<tr>\
                                        <td>' + confirmdata.users.data[i].rating_by.first_name + ' ' + (confirmdata.users.data[i].rating_by.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.users.data[i].rating_by.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + confirmdata.rating_array[confirmdata.users.data[i].rating_to.gender][confirmdata.users.data[i].rating_number].name + '</td>\
                                        <td>' + finaldate + '</td>\
                                    </tr>';
                    }
                }
                $("#users-rating-list").html(list_html);
                if (confirmdata.users.last_page == 0)
                    confirmdata.users.last_page = 1;
                $(".totalList").html("Page " + confirmdata.users.current_page + " of " + confirmdata.users.last_page);
                var pag_list = "";
                var cls = "";
                if (page != 1) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='search_rating_page_dec'><<</a></li>";
                }
                var fromloop = 1;
                var toloop = 10;
                if (page > 5) {
                    fromloop = page - 4;
                    toloop = parseInt(page) + 5;
                }
                if (toloop > confirmdata.users.last_page) {
                    toloop = confirmdata.users.last_page;
                }
                if (page == confirmdata.users.last_page && confirmdata.users.last_page > 10) {
                    fromloop = page - 10;
                }
                for (i = fromloop; i <= toloop; i++) {
                    if (i == confirmdata.users.current_page) {
                        cls = "page_active";
                    } else {
                        cls = "";
                    }
                    pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' userid='" + userid + "' class='search_user_rating_page_num " + cls + "'>" + i + "</a></li>";
                }
                if (page != confirmdata.users.last_page) {
                    pag_list += "<li><a href='javascript:;' userid='" + userid + "' class='search_rating_page_inc'>>></a></li>";
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
                //location.href= domain_url+"/admin/login";
            }
        });
    }

    /**
     * Filter User Rating list pagination
     */
    $(document).on('click', '.search_user_rating_page_num', function () {
        var pageNoId = $(this).attr('id');
        var userid = $(this).attr('userid');
        var pageNoArr = pageNoId.split('_');
        var pageNo = pageNoArr[1];
        var search_str = $("#daterange_search").val();
        var date_arr = search_str.split(' - ');
        var from_date = convertLocalDateToUTCDate(new Date(date_arr[0]));
        var to_date = convertLocalDateToUTCDate(new Date(date_arr[1]));
        ratingListBySearch(from_date, to_date, userid, pageNo);
    });

    /**
     * Next page of filter rating list
     */
    $(document).on("click", ".search_rating_page_inc", function () {
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
        ratingListBySearch(from_date, to_date, userid, nxtpage);
    });

    /**
     * Previous page of filter rating list
     */
    $(document).on("click", ".search_rating_page_dec", function () {
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
        ratingListBySearch(from_date, to_date, userid, prevpage);
    });
});   