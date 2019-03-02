<?php

return
        [
            'error' => array(
                'empty_json' => 'Provided json is empty.',
                'invalid_json' => 'Provided json is invalid.',
                'exception' => 'Server Error.',
            ),
            'fail' => array(
                'user_not_found' => 'User not found.',
                'unauthorized' => 'User is not authorized.',
                'question_not_found' => 'Questions not found.',
                'account_deleted' => 'User account is deleted.',
                'image_not_found' => 'Image not found.',
                'push_not_found' => 'Push not found.',
                'chat_thread_not_found' => 'Chat thread id not found',
                'rating_user_not_found' => 'User not found for rating.',
                'rating_already_done' => 'You already rate this user.',
                'bracket_already_running' => 'Please complete your previous bracket first.',
                'members_not_found_for_bracket' => 'Please change your prefrences.',
                'bracket_users_data_not_complete' => 'Either bracket winner or looser id are less.',
                'bracket_round_played' => 'You have already played current round.',
                'daily_bracket_limit_over' => 'No more bracket left out with you.',
                'in_app' => 'In app verification fail on server.',
            ),
            'success' => array(
                'sign_in' => 'User sign-in successfully.',
                'sign_up' => 'User sign-up successfully.',
                'user_profile_create' => 'User profile created successfully.',
                'user_profile_update' => 'User profile updated successfully.',
                'image_upload' => 'Image upload successfully.',
                'user_sign_out' => 'User sign-out successfully.',
                'user_push_status' => 'User push notification status updated successfully.',
                'user_delete' => 'User account deleted successfully.',
                'image_position_change' => 'Image position change successfully.',
                'image_deleted' => 'Image deleted change successfully.',
                'push_delete' => 'Push deleted successfully.',
                'chat_thread_delete' => 'Chat thread deleted successfully.',
                'push_update' => 'Push updated successfully.',
                'chat_thread_updated' => 'Chat thread updated successfully.',
                'rate_done' => 'You are one step closer to being a Wild Card!',
                'block_done' => 'Block done successfully.',
                'un_blocked_done' => 'Unblock done successfully.',
                'bracket_round_data_saved' => 'Bracket current round data saved successfully.',
                'in_app' => 'In app data save successfully.'
            ),
            'admin' => array(
                'password_reset' => 'Your temporary password has been sent to your mail.',
                'password_updated' => 'Password has been updated successfully.',
                'old_password_validation' => 'Current password is not correct.',
                'repeat_password_validation' => 'Current password and new password can not be same.',
                'user_not_found' => 'User does not exist',
                'recipient_not_found' => 'Recipient does not exist',
                'force_change_password' => 'Please change the password to continue using Admin CMS.',
                'push_notification_sent' => 'Push notification has been sent to specified daters.',
                'excel_mail_sent' => 'Your Dater’s Data request has been processed & you will get an email having details, shortly.',
            )
];
