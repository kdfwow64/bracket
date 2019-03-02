/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    // Initializes and creates emoji set from sprite sheet
    window.emojiPicker = new EmojiPicker({
        emojiable_selector: '[data-emojiable=true]',
        assetsPath: '../lib/img',
        popupButtonClasses: 'fa fa-smile-o'
    });
    // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
    // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
    // It can be called as many times as necessary; previously converted input fields will not be converted again
    window.emojiPicker.discover();
});


/*
 * Function for the template in which we need users in select box
 */
function formatRepoUser(repo) {
    if (repo.loading)
        return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.first_name + " " + repo.last_name + "</div>";

    if (repo.email) {
        markup += "<div class='select2-result-repository__description'>" + repo.email + "</div>";
    }

    markup += "</div></div>";

    return markup;
}

/*
 * Function for showing the selected option in select box
 */
function formatRepoUserSelection(repo) {
    return repo.first_name + " " + repo.last_name || repo.email;
}

/*
 * Implemented the library of select2 for multiple user selection
 */
$(".js-user-multiple").select2({
    ajax: {
        url: "../search-user",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term, // search term
                page: params.page,
                push_notification: 'user_search'
            };
        },
        processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 2,
    templateResult: formatRepoUser, // omitted for brevity, see the source of this page
    templateSelection: formatRepoUserSelection // omitted for brevity, see the source of this page
});

/*
 * Function for the template in which we need users in select box
 */
function formatRepoLocation(repo) {
    if (repo.loading)
        return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.country + "</div>";

    markup += "</div></div>";

    return markup;
}

/*
 * Function for showing the selected option in select box
 */
function formatRepoLocationSelection(repo) {
    return repo.country;
}

/*
 * Implemented the library of select2 for multiple location selection
 */
$(".js-location-multiple").select2({
    ajax: {
        url: "../search-user",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term, // search term
                page: params.page,
                push_notification: 'location_search'
            };
        },
        processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 0,
    templateResult: formatRepoLocation, // omitted for brevity, see the source of this page
    templateSelection: formatRepoLocationSelection // omitted for brevity, see the source of this page
});

/*
 * Showing the select box of multiple users on the click of specific user radio button 
 */
$(document).on('click', '.select_to_radio', function () {
    if ($('#specific_users').is(':checked')) {
        $('.select-user').removeClass('hide');
        $('.select-user .select2-container').css('width', '1000px');
    } else {
        $('.select-user').addClass('hide');
        $('.js-users').val('');
    }
});

/*
 * Showing the select box of multiple location on the click of specific user radio button 
 */
$(document).on('click', '.select_to_radio', function () {
    if ($('#specific_location').is(':checked')) {
        $('.select-location').removeClass('hide');
        $('.select-location .select2-container').css('width', '1000px');
    } else {
        $('.select-location').addClass('hide');
        $('.js-location').val('');
    }
});

$("#add_push_notification").validate({
    rules: {
        notification_title: {
            required: true,
            maxlength: 100
        },
        notification_message: {
            required: true,
            maxlength: 250
        },
        send_to_radios: "required"
    },
    messages: {
        notification_title: {
            required: "Please enter title.",
            maxlength: "Please enter maximum 100 characters."
        },
        notification_message: {
            required: "Please enter message.",
            maxlength: "Please enter maximum 250 characters."
        },
        send_to_radios: "Please select whom to send notification"
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        var text = $('.emoji-wysiwyg-editor').text().length;
        var img_tag = $('.emoji-wysiwyg-editor img').length;
        var total_length = text + img_tag;
        if ($('.emoji-wysiwyg-editor').is(':empty')) {
            $('.emoji-wysiwyg-editor').parents('.form-group').append('<label id="notification_message-error" class="error" for="notification_messsage">Please enter message.</label>');
            return false;
        } else if (total_length > 250) {
            $('.emoji-wysiwyg-editor').parents('.form-group').append('<label id="notification_message-error" class="error" for="notification_messsage">Please enter only 250 characters.</label>');
        } else {
            form.submit();
        }
    }
});

/* Get list of notifications on page laod */
$(document).ready(function () {
    var uri = window.location.href;
    var lastslashindex = uri.split('/').pop();

    if (lastslashindex == "push-notification") {
        notificationList(1);
    }

})
/**
 * Notification Pagination
 */
$(document).on('click', '.notification_page_num', function () {
    var pageNoId = $(this).attr('id');
    var pageNoArr = pageNoId.split('_');
    var pageNo = pageNoArr[1];
    notificationList(pageNo);
});

/**
 * Function for listing the user
 */
function notificationList(page) {
    $.ajax({
        url: 'push-notification-list-ajax?page=' + page,
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
                    var finaldate = convertUTCDateToLocalDate(confirmdata.data[i].created).toLocaleString();
                    if (confirmdata.data[i].recipient_id == 1) {
                        var recipient = "All Daters";
                    }
                    if (confirmdata.data[i].recipient_id == 2) {
                        var recipient = "Only Males";
                    }
                    if (confirmdata.data[i].recipient_id == 3) {
                        var recipient = "Only Females";
                    }
                    if (confirmdata.data[i].recipient_id == 4) {
                        var recipient = "Selected Daters";
                    }
                    if (confirmdata.data[i].recipient_id == 5) {
                        var recipient = "Selected Locations";
                    }
                    list_html += '<tr>\
                                        <td>' + confirmdata.data[i].title + '</td>\
                                        <td>' + confirmdata.data[i].message + '</td>\
                                        <td>' + finaldate + '</td>\
                                        <td><a href="' + domain_url + '/admin/push-notification/' + confirmdata.data[i].id + '">' + recipient + '</a></td>\
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
                pag_list += "<li><a href='javascript:;' class='notification_page_dec'><<</a></li>";
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
                pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' class='notification_page_num " + cls + "'>" + i + "</a></li>";
            }
            if (page != confirmdata.last_page) {
                pag_list += "<li><a href='javascript:;' class='notification_page_inc'>>></a></li>";
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
$(document).on("click", ".notification_page_inc", function () {
    var nxtpage;
    $(".pagination li").each(function () {
        if ($(this).find("a").hasClass("page_active")) {
            var pagenum = $(this).find("a").attr("id");
            var pageNoArr = pagenum.split('_');
            var pageNo = pageNoArr[1];
            nxtpage = parseInt(pageNo) + 1;
        }
    })
    notificationList(nxtpage);
});

/**
 * Previous page of user list
 */
$(document).on("click", ".notification_page_dec", function () {
    var prevpage;
    $(".pagination li").each(function () {
        if ($(this).find("a").hasClass("page_active")) {
            var pagenum = $(this).find("a").attr("id");
            var pageNoArr = pagenum.split('_');
            var pageNo = pageNoArr[1];
            prevpage = parseInt(pageNo) - 1;
        }
    })
    notificationList(prevpage);
});


/**
 * Recipients Pagination
 */
$(document).on('click', '.recipient_page_num', function () {
    var pageNoId = $(this).attr('id');
    var user_id = $(this).attr('user_id');
    var pageNoArr = pageNoId.split('_');
    var pageNo = pageNoArr[1];
    recipientList(user_id, pageNo);
});

/**
 * Function for listing the user
 */
function recipientList(user_id, page) {
    $.ajax({
        url: '../notification-recipient-list-ajax?page=' + page,
        cache: false,
        processData: false,
        dataType: "json",
        contentType: "application/json",
        type: "POST",
        data: JSON.stringify({user_id: user_id}),
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
                    if (confirmdata.data[i].user_details.profile_picture == null || confirmdata.data[i].user_details.profile_picture == "") {
                        profileimg = domain_url + "/admin/img/default-img.png";
                    } else {
                        profileimg = confirmdata.data[i].user_details.profile_picture;
                    }
                    if (confirmdata.data[i].user_details.gender == 1) {
                        gender = "Male";
                    } else {
                        gender = "Female";
                    }
                    if(confirmdata.data[i].user_details.email == ""){
                        confirmdata.data[i].user_details.email = "Not Shared";
                    }
                    if(confirmdata.data[i].user_details.country == ""){
                        confirmdata.data[i].user_details.country = "Not Shared";
                    }
                    list_html += '<tr>\
                                        <td>' + confirmdata.data[i].user_details.first_name + ' ' + (confirmdata.data[i].user_details.last_name).slice(0, 1) + '</td>\
                                        <td>' + confirmdata.data[i].user_details.email + '</td>\
                                        <td>\
                                            <a class="" href="#">\
                                                <img class="img-responsive roundimg" src="' + profileimg + '" alt="">\
                                            </a>\
                                        </td>\
                                        <td>' + gender + '</td>\
                                        <td>' + confirmdata.data[i].user_details.country + '</td>\
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
                pag_list += "<li><a href='javascript:;' user_id='" + confirmdata.data[0].id + "' class='recipient_page_dec'><<</a></li>";
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
                pag_list += "<li><a href='javascript:;' id='pagenum_" + i + "' user_id='" + confirmdata.data[0].id + "' class='recipient_page_num " + cls + "'>" + i + "</a></li>";
            }
            if (page != confirmdata.last_page) {
                pag_list += "<li><a href='javascript:;' user_id='" + confirmdata.data[0].id + "' class='recipient_page_inc'>>></a></li>";
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
$(document).on("click", ".recipient_page_inc", function () {
    var nxtpage;
    var user_id = $(this).attr("user_id");
    $(".pagination li").each(function () {
        if ($(this).find("a").hasClass("page_active")) {
            var pagenum = $(this).find("a").attr("id");
            var pageNoArr = pagenum.split('_');
            var pageNo = pageNoArr[1];
            nxtpage = parseInt(pageNo) + 1;
        }
    })
    recipientList(user_id, nxtpage);
});

/**
 * Previous page of user list
 */
$(document).on("click", ".recipient_page_dec", function () {
    var prevpage;
    var user_id = $(this).attr("user_id");
    $(".pagination li").each(function () {
        if ($(this).find("a").hasClass("page_active")) {
            var pagenum = $(this).find("a").attr("id");
            var pageNoArr = pagenum.split('_');
            var pageNo = pageNoArr[1];
            prevpage = parseInt(pageNo) - 1;
        }
    })
    recipientList(user_id, prevpage);
});