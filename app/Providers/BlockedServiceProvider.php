<?php

namespace App\Providers;

use App\Providers\BaseServiceProvider;
use App\Models\BlockedUser;

/**
 * BlockedServiceProvider class contains methods for blocked/unblocked user management
 */
class BlockedServiceProvider extends BaseServiceProvider {

    /**
     * List of Blocked/Unblocked Users
     * @return array
     */
    public static function getBlockedUnblockedUser($flag = NULL) {
        return BlockedUser::getBlockedUser($flag);
    }

    /**
     * List of users who blocked/unblocked User
     * @return array
     */
    public static function getBlockedUnblockedUserDetail($id, $flag = NULL) {
        return BlockedUser::getBlockedUserDetail($id, $flag);
    }

    /**
     * Function to get the list of blocked/unblocked users by date filter
     * @param array $request
     * @return array
     */
    public static function getDateFilterBlockedUnblockedUsers($request, $flag = NULL) {
        $userid = $request['userid'];
        $from_date = $request['from_date'];
        if (isset($request['to_date'])) {
            $to_date = $request['to_date'];
        }
        if ($from_date == "NaN/NaN/NaN NaN:NaN:NaN") {
            $filter_blocked_user = BlockedUser::getBlockedUserDetail($userid, $flag);
        } else {
            $filter_blocked_user = BlockedUser::getDateFilterBlockedUser($userid, $from_date, $to_date, $flag);
        }
        return $filter_blocked_user;
    }

}
