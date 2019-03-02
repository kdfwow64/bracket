<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model {

    use SoftDeletes;

    protected $table = 'subscription';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * subscribed dater
     * @return array
     */
    function subscribedDater() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * Get the list of users who opted for in app purchase
     * @return array
     */
    public static function getSubscriptionUsers() {
        return Subscription::select("subscription.*", DB::raw("max(DATE_FORMAT(subscription.created_at, '%m-%d-%Y %H:%i:%s')) as created"), DB::raw('count(DISTINCT CONCAT(subscription.user_id, "_", subscription.type, "_", subscription.id)) as total'), DB::raw('sum(price) as amount'))
                        ->leftJoin('transaction', 'transaction.subscription_id', '=', 'subscription.id')
                        ->with(['subscribedDater'])
                        ->groupBy('subscription.user_id')
                        ->groupBy('subscription.type')
                        ->orderBy('subscription.updated_at','DESC')
                        ->paginate(config('constants.record_per_page'))->toArray();
    }

    /**
     * Count of number of subscription in current month
     * @return integer
     */
    public static function getTotalPurchases() {
        $currentMonth = date('m');
        return Subscription::whereRaw('MONTH(created_at) = ?', [$currentMonth])->count();
    }

    /**
     * Get inapp analytics
     * @return array
     */
    public static function inAppAnalytics($request = NULL) {
        $subscription_const = config('constants.subscription_id');
        $in_app_analytics['Monthly Subscription'] = Subscription::select('*')->where('type', $subscription_const['monthly'])->groupBy('user_id');
        if (isset($request) && !empty($request) && !empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $in_app_analytics['Monthly Subscription'] = $in_app_analytics['Monthly Subscription']->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $in_app_analytics['Monthly Subscription'] = count($in_app_analytics['Monthly Subscription']->get()->toArray());

        $in_app_analytics['Additional Bracket'] = Subscription::select('*')->where('type', $subscription_const['additional'])->groupBy('user_id');
        if (isset($request) && !empty($request) && !empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $in_app_analytics['Additional Bracket'] = $in_app_analytics['Additional Bracket']->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $in_app_analytics['Additional Bracket'] = count($in_app_analytics['Additional Bracket']->get()->toArray());
        return $in_app_analytics;
    }

    /**
     * Get amount analytics
     * @return array
     */
    public static function amountAnalytics($request = NULL) {
        $subscription_const = config('constants.subscription_id');
        $amount_analytics['Monthly Subscription'] = Subscription::select(DB::raw('sum(price) as amount'))->leftJoin('transaction', 'transaction.subscription_id', '=', 'subscription.id')->where('type', $subscription_const['monthly']);
        if (isset($request) && !empty($request) && !empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $amount_analytics['Monthly Subscription'] = $amount_analytics['Monthly Subscription']->whereBetween('transaction.start_date', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $amount_analytics['Monthly Subscription'] = $amount_analytics['Monthly Subscription']->first()->toArray();
        if ($amount_analytics['Monthly Subscription']['amount'] == NULL) {
            $amount_analytics['Monthly Subscription']['amount'] = 0;
        }
        $amount_analytics['Monthly Subscription'] = $amount_analytics['Monthly Subscription']['amount'];

        $amount_analytics['Additional Bracket'] = Subscription::select(DB::raw('sum(price) as amount'))->where('type', $subscription_const['additional']);
        if (isset($request) && !empty($request) && !empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $amount_analytics['Additional Bracket'] = $amount_analytics['Additional Bracket']->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $amount_analytics['Additional Bracket'] = $amount_analytics['Additional Bracket']->first()->toArray();
        if ($amount_analytics['Additional Bracket']['amount'] == NULL) {
            $amount_analytics['Additional Bracket']['amount'] = 0;
        }
        $amount_analytics['Additional Bracket'] = $amount_analytics['Additional Bracket']['amount'];
        return $amount_analytics;
    }

}
