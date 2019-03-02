<?php

namespace App\Providers;

use App\Providers\BaseServiceProvider;
use App\Models\BracketMember;

/**
 * WildcardServiceProvider class contains methods for wildcard user management
 */
class WildcardServiceProvider extends BaseServiceProvider {

    /**
     * List of Wildcard Users
     * @return array
     */
    public static function getWildcardUser() {
        return BracketMember::getWildcardUser();
    }

    /**
     * Function to get the list of Wildcard users by date filter
     * @param array $request
     * @return array
     */
    public static function getDateFilterWildcardUsers($request) {
        $from_date = $request['from_date'];
        if (isset($request['to_date'])) {
            $to_date = $request['to_date'];
        }
        if ($from_date == "NaN/NaN/NaN NaN:NaN:NaN") {
            $filter_wildcard_user = BracketMember::getWildcardUser();
        } else {
            $filter_wildcard_user = BracketMember::getDateFilterWildcardUser($from_date, $to_date);
        }
        return $filter_wildcard_user;
    }

}
