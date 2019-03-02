/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $("#change_password").validate({
        rules: {
            old_password: "required",
            new_password: "required",
            confirm_password: {
                equalTo: "#new_password"
            }
        },
        messages: {
            old_password: "Please enter old password.",
            new_password: "Please enter new password.",
            confirm_password: {
                equalTo: "The confirm password and new password does not match."
            }
        },
        submitHandler: function () {
            form.submit();
        }
    });
})

