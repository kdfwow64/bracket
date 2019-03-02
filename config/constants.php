<?php

return [
    'user_type' => array(
        'admin' => 1,
        'app_user' => 2,
    ),
    'device_type' => array(
        'ios' => 1
    ),
    'organisation_name' => 'Bracket',
    'gender' => array(
        'male' => 1,
        'female' => 0
    ),
    'gender_db' => array(
        1 => 'male',
        0 => 'female'
    ),
    'search' => array(
        'user_id' => 1,
        'facebook_id' => 2
    ),
    'status' => array(
        'success' => 1,
        'fail' => 0
    ),
    'user_default_value' => array(
        'start_radius' => 2,
        'end_radius' => 1000,
        'start_age' => 18,
        'end_age' => 99,
        'prefer_gender' => 0,
        'gender' => 1
    ),
    'push_notification' => array(
        'default' => array(
            array(
                'type' => 1,
                'title' => 'WELCOME TO BRACKET DATING!',
                'message' => 'We are so excited you decided to join us and we cant wait to help you #dateawinner.',
                'thread_id' => NULL
            ),
            array(
                'type' => 2,
                'title' => 'TUTORIAL',
                'message' => "Whether you are new or just need a refresher! We've got you covered!",
                'thread_id' => NULL
            )
        ),
        'three' => array(
            'type' => 3,
            'title' => "DON'T FORGET TO COMPLETE YOUR PROFILE!",
            'message' => 'It helps us make a better experience for you!',
            'push_message' => 'Help us, help you, by completing your Bracket Dating profile.',
            'thread_id' => NULL
        ),
        'silent' => array(
            'four' => array(
                'type' => 4,
                'title' => 'YOUR PROFILE IS UPDATED.',
                'message' => 'Login user profile is updated.',
                'thread_id' => NULL
            ),
            'twelve' => array(
                'type' => 12,
                'title' => 'RATE USER.',
                'message' => 'Hey its time to rate a user.',
                'thread_id' => NULL
            )
        ),
        'five' => array(
            'type' => 5,
            'title' => "HEY GOOD LOOKIN'",
            'message' => "Your rating has increased!",
            'push_message' => 'Awesome! Your Bracket rating has increased!',
            'thread_id' => NULL
        ),
        'six' => array(
            'type' => 6,
            'title' => 'NEW BRACKET AVAILABLE',
            'message_1' => 'Its time to start your first round.',
            'push_message_1' => 'Hooray! A new Bracket is ready for you!',
            'message_2' => 'Congratulations! Your bracket is waiting for you',
            'push_message_2' => 'Congratulations! Your bracket is waiting for you',
            'thread_id' => NULL
        ),
        'seven' => array(
            'type' => 7,
            'title' => 'GREAT PROGRESS!',
            'message' => 'Round 3 is now available! You are getting closer!.',
            'push_message' => 'Game on! Round 3 of Bracket is now available.',
            'thread_id' => NULL
        ),
        'eight' => array(
            'type' => 8,
            'title' => 'FINAL ROUND UNLOCKED!',
            'message' => "It's time to pick your winner! Choose wisely.",
            'push_message' => 'Champion round unlocked! Its time to select a winner!',
            'thread_id' => NULL
        ),
        'nine' => array(
            'type' => 9,
            'title' => 'WILD CARD',
            'message' => "Congratulations! You have been selected as a Wildcard and will be entered into higher rounds of other daters Brackets!",
            'push_message' => "Congratulations! You have been selected as a Wildcard and will be entered into higher rounds of other daters Brackets!",
            'thread_id' => NULL
        ),
        'ten' => array(
            'type' => 10,
            'title' => 'YOU ARE A WINNER',
            'message_1' => 'Congratulations! ',
            'message_2' => ' eliminated everyone else to talk to you!',
            'push_message_1' => 'Congratulations! ',
            'push_message_2' => ' thinks you are amazing and eliminated everyone else in the Bracket to talk to you! Chat now!',
            'thread_id' => NULL
        ),
        'eleven' => array(
            'type' => 11,
            'title' => ' 2 HOURS LEFT',
            'message_1' => 'Time is running out for you to start chatting with ',
            'message_2' => ' . Don’t miss out!',
            'push_message_1' => 'Only 2 hours left to start a chat with ',
            'push_message_2' => ' . Click here to chat!',
            'thread_id' => NULL
        ),
        'thirteen' => array(
            'type' => 13,
            'title' => '',
            'message' => '',
            'thread_id' => NULL
        ),
        'admin' => array(
            'type' => 11,
            'sub_type' => array('111', '112', '113', '114', '115'),
        ),
        'recipients_type' => array(
            1 => 'All Daters',
            2 => 'Only Males',
            3 => 'Only Females',
            4 => 'Selected Daters',
            5 => 'Selected Locations'
        ),
    ),
    'record_per_page' => 10,
    'img_rating' => array(
        1 => array(
            1 => array(
                'url' => 'admin/img/male-ratings/icHandsome.png',
                'name' => 'Handsome',
            ),
            2 => array(
                'url' => 'admin/img/male-ratings/icDashing.png',
                'name' => 'Dashing',
            ),
            3 => array(
                'url' => 'admin/img/male-ratings/icCasanova.png',
                'name' => 'Casanova',
            ),
            4 => array(
                'url' => 'admin/img/male-ratings/icPrince.png',
                'name' => 'Prince',
            ),
            5 => array(
                'url' => 'admin/img/male-ratings/icMrright.png',
                'name' => 'Mr. Right',
            ),
        ),
        0 => array(
            1 => array(
                'url' => 'admin/img/female-ratings/icAlluring.png',
                'name' => 'Alluring',
            ),
            2 => array(
                'url' => 'admin/img/female-ratings/icBeautiful.png',
                'name' => 'Beautiful',
            ),
            3 => array(
                'url' => 'admin/img/female-ratings/icGorgeous.png',
                'name' => 'Gorgeous',
            ),
            4 => array(
                'url' => 'admin/img/female-ratings/icRavishing.png',
                'name' => 'Ravishing',
            ),
            5 => array(
                'url' => 'admin/img/female-ratings/icGoddess.png',
                'name' => 'Goddess',
            ),
        ),
    ),
    'bracket' => array(
        'last_round' => 5,
        'first_round' => 1,
        'is_complete_true' => 1,
        'is_complete_false' => 0,
        'is_paid_bracket' => 1,
        'is_free_bracket' => 0,
        'members_count' => 19,
        'round_three' => 3,
        'round_three_wild_card_type' => 2,
        'round_four' => 4,
        'round_four_wild_card_type' => 3,
        'non_wild_card_type' => 1,
    ),
    'unblocked_flag' => 1,
    'subscription_type' => array(
        2 => 'Monthly Subscription',
        1 => 'Additional Bracket',
    ),
    'subscription_id' => array(
        'monthly' => 2,
        'additional' => 1,
    )
];
